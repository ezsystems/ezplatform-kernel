<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Core\Command;

use Exception;
use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\Repository;
use eZ\Publish\API\Repository\Values\Content\ContentList;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\ContentType\ContentType;
use eZ\Publish\API\Repository\Values\ContentType\FieldDefinition;
use eZ\Publish\API\Repository\Values\Filter\Filter;
use eZ\Publish\SPI\Persistence\User\Handler;
use InvalidArgumentException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class ExpireUserPasswordsCommand extends Command
{
    public const REQUIRE_NEW_PASSWORD_VALUE = true;

    protected static $defaultName = 'ibexa:user:expire-password';

    public const DEFAULT_BATCH_SIZE = 50;

    public const DEFAULT_PASSWORD_TTL = 90;

    public const USER_FIELDTYPE_IDENTIFIER = 'ezuser';

    public const BEFORE_RUNNING_HINTS = <<<EOT
<error>Before you continue:</error>
- Make sure to back up your database.
- Take installation offline, during the script execution the database should not be modified.
- Run this command without memory limit.
- Run this command in production environment using <info>--env=prod</info>
EOT;

    /** @var \eZ\Publish\API\Repository\Repository */
    private $repository;

    /** @var \eZ\Publish\API\Repository\ContentService */
    private $contentService;

    /** @var \eZ\Publish\API\Repository\ContentTypeService */
    private $contentTypeService;

    /** @var \eZ\Publish\SPI\Persistence\User\Handler */
    private $userHandler;

    public function __construct(
        Repository $repository,
        ContentService $contentService,
        ContentTypeService $contentTypeService,
        Handler $userHandler
    ) {
        $this->repository = $repository;
        $this->contentService = $contentService;
        $this->contentTypeService = $contentTypeService;
        $this->userHandler = $userHandler;

        parent::__construct();
    }

    protected function configure(): void
    {
        $beforeRunningHints = self::BEFORE_RUNNING_HINTS;
        $this
            ->setDescription('Expire passwords for selected users.')
            ->addOption(
                'user-id',
                'u',
                InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
                'Expire password for specific User identified by ID'
            )
            ->addOption(
                'user-group-id',
                'ug',
                InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
                'Expire passwords of all users assigned to specific User Group'
            )
            ->addOption(
                'user-content-type-identifier',
                'ct',
                InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
                'Expire passwords of all users based on specific Content Type'
            )
            ->addOption(
                'force',
                'f',
                InputOption::VALUE_NONE,
                'Perform setting user passwords as expired'
            )
            ->addOption(
                'iteration-count',
                'c',
                InputOption::VALUE_REQUIRED,
                'Number of users to process at once',
                self::DEFAULT_BATCH_SIZE
            )
            ->addOption(
                'password-ttl',
                't',
                InputOption::VALUE_REQUIRED,
                'After how many days passwords expire. Set when Content Type needs to be updated.',
                self::DEFAULT_PASSWORD_TTL
            )
            ->setHelp(
                <<<EOT
The command <info>%command.name%</info> expires passwords of specific users. 
Use this tool wisely as next time affect users tries to log in, they will be forced to set a new password.
Note: This script can potentially run for a very long time, and in Symfony dev environment it will consume memory exponentially with size of dataset.

{$beforeRunningHints}
EOT
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $iterationCount = $input->getOption('iteration-count');
        $userIds = $input->getOption('user-id');
        $userGroupIds = $input->getOption('user-group-id');
        $force = $input->getOption('force');

        if (!empty($userIds) && !empty($userGroupIds)) {
            throw new InvalidArgumentException('You cannot use --user-id and --user-group-id options at once.');
        }

        $userContentTypeIdentifiers = $input->getOption('user-content-type-identifier');

        $this->supplySearchCriteria($output, $userIds, $userGroupIds, $userContentTypeIdentifiers);

        $filter = new Filter();
        $this->applyCriteria($filter, $userIds, $userGroupIds, $userContentTypeIdentifiers);

        $contentList = $this->repository->sudo(function () use ($filter): ContentList {
            return $this->contentService->find($filter);
        });
        $totalCount = $contentList->getTotalCount();

        if ($totalCount === 0) {
            $output->writeln('<info>There are no users matching given criteria</info>');

            return Command::SUCCESS;
        }

        $output->writeln(sprintf(
            '<info>Found %d users matching given criteria</info>',
            $totalCount
        ));

        $progressBar = new ProgressBar($output, $totalCount);
        $progressBar->start();
        $output->write(PHP_EOL);

        $processedUsersCount = 0;
        $processedContentTypes = [];

        $this->repository->beginTransaction();
        try {
            do {
                $filter
                    ->withLimit((int) $iterationCount)
                    ->withOffset($processedUsersCount);

                $contentList = $this->repository->sudo(function () use ($filter): ContentList {
                    return $this->contentService->find($filter);
                });

                $this->processContentList(
                    $contentList,
                    $processedContentTypes,
                    $processedUsersCount,
                    $input,
                    $output,
                    $progressBar
                );

                $processedUsersCount += count($contentList->getIterator());
            } while ($processedUsersCount < $totalCount);
        } catch (Exception $e) {
            $this->repository->rollback();
            $output->writeln(
                '<error>Something went wrong. See the exception below, '
                . 'fix the issue and rerun this command</error>'
            );

            throw $e;
        }

        $progressBar->finish();

        $output->writeln(sprintf(
            '<info>Expired passwords of %d users</info>',
            $processedUsersCount
        ));

        if ($force) {
            $this->repository->commit();
        } else {
            $this->repository->rollback();

            $output->writeln(
                '<info>No changes made. If you want to proceed rerun '
                . 'this command with --force flag.</info>'
            );
        }

        return Command::SUCCESS;
    }

    /**
     * @param array<int> $userIds
     * @param array<int> $userGroupIds
     * @param array<string> $userContentTypeIdentifiers
     */
    private function applyCriteria(
        Filter $filter,
        array $userIds,
        array $userGroupIds,
        array $userContentTypeIdentifiers
    ): void {
        $filter->andWithCriterion(
            new Query\Criterion\IsUserBased()
        );

        if (!empty($userIds)) {
            $filter->andWithCriterion(new Query\Criterion\ContentId($userIds));
        }

        if (!empty($userGroupIds)) {
            $filter->andWithCriterion(new Query\Criterion\ParentLocationId($userGroupIds));
        }

        if (!empty($userContentTypeIdentifiers)) {
            $filter->andWithCriterion(new Query\Criterion\ContentTypeIdentifier($userContentTypeIdentifiers));
        }
    }

    /**
     * @param array<int> $userIds
     * @param array<int> $userGroupIds
     * @param array<string> $userContentTypeIdentifiers
     */
    private function supplySearchCriteria(
        OutputInterface $output,
        array $userIds,
        array $userGroupIds,
        array $userContentTypeIdentifiers
    ): void {
        $output->writeln('<info>Criteria used to find users:</info>');

        if (!empty($userIds)) {
            $output->writeln(
                sprintf("<info>\tUser ID: %s</info>", implode(', ', $userIds))
            );
        }

        if (!empty($userGroupIds)) {
            $output->writeln(
                sprintf("<info>\tUser Group ID: %s</info>", implode(', ', $userGroupIds))
            );
        }

        if (!empty($userContentTypeIdentifiers)) {
            $output->writeln(
                sprintf(
                    "<info>\tUser Content Type Identifier: %s</info>",
                    implode(', ', $userContentTypeIdentifiers)
                )
            );
        }
    }

    /**
     * @param array<string> $processedContentTypes
     */
    private function processContentList(
        ContentList $contentList,
        array $processedContentTypes,
        int $processedUsersCount,
        InputInterface $input,
        OutputInterface $output,
        ProgressBar $progressBar
    ): void {
        $processedUsersFromBatch = 0;
        foreach ($contentList as $content) {
            $contentType = $content->getContentType();

            if (
                $this->doesContentTypeNeedUpdate($contentType, $processedContentTypes)
            ) {
                $this->updateContentType(
                    $contentType,
                    $input,
                    $output
                );

                $processedContentTypes[] = $contentType->identifier;
            }

            $spiUser = $this->userHandler->load($content->id);
            $updatedUser = clone $spiUser;
            $updatedUser->passwordUpdatedAt = 1;
            $this->userHandler->updatePassword($updatedUser);

            $output->writeln(sprintf(
                '<info>Processed user %d/%d: %s</info>',
                $processedUsersCount + $processedUsersFromBatch,
                $contentList->getTotalCount(),
                $spiUser->login
            ));

            $progressBar->advance();
            $output->write(PHP_EOL);

            ++$processedUsersFromBatch;
        }
    }

    private function updateContentType(
        ContentType $contentType,
        InputInterface $input,
        OutputInterface $output
    ): void {
        $passwordTTL = (int) $input->getOption('password-ttl');
        $fieldDefinitions = $contentType->getFieldDefinitionsOfType(self::USER_FIELDTYPE_IDENTIFIER);
        $userFieldDefinition = $fieldDefinitions->first();

        $validatorConfiguration = $userFieldDefinition->getValidatorConfiguration();
        $fieldSettings = $userFieldDefinition->getFieldSettings();

        $output->writeln(sprintf(
            '<info>Content Type "%s" needs to be updated:</info>',
            $contentType->identifier
        ));

        if (
            $validatorConfiguration['PasswordValueValidator']['requireNewPassword']
            !== self::REQUIRE_NEW_PASSWORD_VALUE
        ) {
            $output->writeln(sprintf(
                "\tPrevent reusing old password: %s -> %s",
                $validatorConfiguration['PasswordValueValidator']['requireNewPassword']
                    ? 'enabled'
                    : 'disabled',
                self::REQUIRE_NEW_PASSWORD_VALUE
                    ? 'enabled'
                    : 'disabled'
            ));
        }

        if ($fieldSettings['PasswordTTL'] !== $passwordTTL) {
            $output->writeln(sprintf(
                "\tDays before password expires: %d -> %d",
                $fieldSettings['PasswordTTL'],
                $passwordTTL
            ));
        }

        //  enforce CT to use password expiration feature
        $validatorConfiguration['PasswordValueValidator']['requireNewPassword'] =
            self::REQUIRE_NEW_PASSWORD_VALUE;
        $fieldSettings['PasswordTTL'] = $passwordTTL;

        $this->doUpdateContentType(
            $contentType,
            $fieldSettings,
            $validatorConfiguration,
            $userFieldDefinition
        );
    }

    /**
     * @param array<string, mixed> $fieldSettings
     * @param array<string, mixed> $validatorConfiguration
     */
    private function doUpdateContentType(
        ContentType $contentType,
        array $fieldSettings,
        array $validatorConfiguration,
        FieldDefinition $fieldDefinition
    ): void {
        $this->repository->sudo(
            function () use (
                $contentType,
                $fieldSettings,
                $validatorConfiguration,
                $fieldDefinition
            ): void {
                $contentTypeDraft = $this->contentTypeService->createContentTypeDraft($contentType);
                $fieldDefinitionUpdateStruct = $this->contentTypeService->newFieldDefinitionUpdateStruct();

                $fieldDefinitionUpdateStruct->fieldSettings = $fieldSettings;
                $fieldDefinitionUpdateStruct->validatorConfiguration = $validatorConfiguration;

                $this->contentTypeService->updateFieldDefinition(
                    $contentTypeDraft,
                    $fieldDefinition,
                    $fieldDefinitionUpdateStruct
                );

                $this->contentTypeService->publishContentTypeDraft($contentTypeDraft);
            }
        );
    }

    /**
     * @param array<string> $processedContentTypes
     */
    private function doesContentTypeNeedUpdate(ContentType $contentType, array $processedContentTypes): bool
    {
        $fields = $contentType->getFieldDefinitionsOfType(self::USER_FIELDTYPE_IDENTIFIER);
        $count = $fields->count();

        if ($count !== 1) {
            throw new InvalidArgumentException(sprintf(
                'Expected exactly 1 "%s" field type in "%s" Content Type, found %d',
                self::USER_FIELDTYPE_IDENTIFIER,
                $contentType->identifier,
                $count
            ));
        }

        $fieldDefinition = $fields->first();

        $validatorConfiguration = $fieldDefinition->getValidatorConfiguration();
        $fieldSettings = $fieldDefinition->getFieldSettings();

        $isUpdateNeeded = !$validatorConfiguration['PasswordValueValidator']['requireNewPassword']
            || 0 === $fieldSettings['PasswordTTL'];
        $isContentTypeAlreadyProcessed = in_array($contentType->identifier, $processedContentTypes, true);

        return $isUpdateNeeded && !$isContentTypeAlreadyProcessed;
    }
}
