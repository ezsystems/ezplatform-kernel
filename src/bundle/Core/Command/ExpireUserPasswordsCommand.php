<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Core\Command;

use Exception;
use eZ\Publish\API\Repository\Repository;
use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\ContentList;
use eZ\Publish\API\Repository\Values\Content\Field;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\LogicalAnd;
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

class ExpireUserPasswordsCommand extends Command
{
    protected static $defaultName = 'ibexa:user:expire-password';

    public const DEFAULT_BATCH_SIZE = 50;

    public const BEFORE_RUNNING_HINTS = <<<EOT
<error>Before you continue:</error>
- Make sure to back up your database.
- Take installation offline, during the script execution the database should not be modified.
- Run this command without memory limit.
- Run this command in production environment using <info>--env=prod</info>
EOT;

    /** @var \eZ\Publish\API\Repository\Repository */
    private $repository;

    /** @var \eZ\Publish\SPI\Persistence\User\Handler */
    private $userHandler;

    public function __construct(
        Repository $repository,
        Handler $userHandler
    ) {
        $this->repository = $repository;
        $this->userHandler = $userHandler;

        parent::__construct();
    }

    protected function configure()
    {
        $beforeRunningHints = self::BEFORE_RUNNING_HINTS;
        $this
            ->setDescription('Expire passwords for selected users.')
            ->addOption(
                'user-id',
                null,
                InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
                'Expire password for specific User identified by ID'
            )
            ->addOption(
                'user-group-id',
                null,
                InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
                'Expire passwords of all users assigned to specific User Group'
            )
            ->addOption(
                'user-content-type-identifier',
                null,
                InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
                'Expire passwords of all users based on specific Content Type'
            )
            ->addOption(
                'force',
                null,
                InputOption::VALUE_OPTIONAL,
                'Perform setting user passwords as expired',
                false
            )
            ->addOption(
                'batch-size',
                null,
                InputOption::VALUE_OPTIONAL,
                'Number of users to process at once',
                self::DEFAULT_BATCH_SIZE
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
        $batchSize = $input->getOption('batch-size');
        $userIds = $input->getOption('user-id');
        $userGroupIds = $input->getOption('user-group-id');
        $force = $input->getOption('force');

        if (!empty($userIds) && !empty($userGroupIds)) {
            throw new InvalidArgumentException('You cannot use --user-id and --user-group-id options at once.');
        }

        $userContentTypeIdentifiers = $input->getOption('user-content-type-identifier');

        $this->summarizeSearchCriteria($output, $userIds, $userGroupIds, $userContentTypeIdentifiers);

        $criteria = $this->getCriteria($userIds, $userGroupIds, $userContentTypeIdentifiers);

        $filter = new Filter(new LogicalAnd($criteria));
        $filter->withLimit(0);

        $contentList = $this->repository->sudo(static function (Repository $repository) use ($filter): ContentList {
            return $repository->getContentService()->find($filter);
        });
        $count = $contentList->getTotalCount();

        if ($count === 0) {
            $output->writeln('<info>There are no users matching given criteria</info>');

            return Command::SUCCESS;
        }

        $output->writeln(sprintf(
            '<info>Found %d User(s) matching given criteria</info>',
            $count
        ));

        $displayProgressBar = !($output->isVerbose() || $output->isVeryVerbose() || $output->isDebug());

        if ($displayProgressBar) {
            $progressBar = new ProgressBar($output, $count);
            $progressBar->setFormat(
                '%current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s% %memory:6s%' . PHP_EOL
            );
            $progressBar->start();
        }

        $processedUsersCount = 0;
        $processedContentTypes = [];

        $this->repository->beginTransaction();
        try {
            do {
                $filter
                    ->withLimit((int) $batchSize)
                    ->withOffset($processedUsersCount);

                $contentList = $this->repository->sudo(static function (Repository $repository) use ($filter): ContentList {
                    return $repository->getContentService()->find($filter);
                });

                /** @var \eZ\Publish\API\Repository\Values\Content\Content $content */
                foreach ($contentList as $content) {
                    $fields = $content->getFields();
                    $userField = $this->findUserField($fields);

                    if (null === $userField) {
                        // content item isn't of a user content type
                        continue;
                    }

                    $contentType = $content->getContentType();
                    $fieldDefinition = $contentType->getFieldDefinition($userField->fieldDefIdentifier);

                    $validatorConfiguration = $fieldDefinition->getValidatorConfiguration();
                    $fieldSettings = $fieldDefinition->getFieldSettings();

                    if (
                        (
                            !$validatorConfiguration['PasswordValueValidator']['requireNewPassword']
                            || 0 === $fieldSettings['PasswordTTL']
                        )
                        && !in_array($contentType->identifier, $processedContentTypes, true)
                    ) {
                        $output->writeln(sprintf(
                            '<info>Content Type "%s" needs to be updated</info>',
                            $contentType->identifier
                        ));

                        //  enforce CT to use password expiration feature
                        $validatorConfiguration['PasswordValueValidator']['requireNewPassword'] = true;
                        $fieldSettings['PasswordTTL'] = 90;

                        $this->updateContentType(
                            $contentType,
                            $fieldSettings,
                            $validatorConfiguration,
                            $fieldDefinition
                        );

                        $processedContentTypes[] = $contentType->identifier;
                    }

                    $spiUser = $this->userHandler->load($content->id);
                    $updatedUser = clone $spiUser;
                    $updatedUser->passwordUpdatedAt = 1;
                    $this->userHandler->updatePassword($updatedUser);

                    ++$processedUsersCount;

                    if ($displayProgressBar) {
                        $progressBar->advance(1);
                    }
                }
            } while ($processedUsersCount < $count);
        } catch (Exception $e) {
            $this->repository->rollback();
            $output->writeln(
                '<error>Something went wrong. See the exception below, '
                . 'fix the issue and rerun this command</error>'
            );

            throw $e;
        }

        if ($force) {
            $this->repository->commit();
        } else {
            $this->repository->rollback();

            $output->writeln(
                '<warning>No changes made. If you want to proceed rerun '
                . 'this command with --force flag.</warning>'
            );
        }

        $output->writeln(sprintf(
            '<info>Expired passwords of %d user(s)</info>',
            $processedUsersCount
        ));

        return Command::SUCCESS;
    }

    /**
     * @param array<int> $userIds
     * @param array<int> $userGroupIds
     * @param array<string> $userContentTypeIdentifiers
     *
     * @return array<\eZ\Publish\API\Repository\Values\Content\Query\Criterion>
     */
    private function getCriteria(
        array $userIds,
        array $userGroupIds,
        array $userContentTypeIdentifiers
    ): array {
        $criteria = [];

        if (!empty($userIds)) {
            $criteria[] = new Query\Criterion\ContentId($userIds);
        }

        if (!empty($userGroupIds)) {
            $criteria[] = new Query\Criterion\ParentLocationId($userGroupIds);
        }

        if (!empty($userContentTypeIdentifiers)) {
            $criteria[] = new Query\Criterion\ContentTypeIdentifier($userContentTypeIdentifiers);
        }

        return $criteria;
    }

    /**
     * @param array<int> $userIds
     * @param array<int> $userGroupIds
     * @param array<string> $userContentTypeIdentifiers
     */
    private function summarizeSearchCriteria(
        OutputInterface $output,
        $userIds,
        $userGroupIds,
        $userContentTypeIdentifiers
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
     * @param array<string, mixed> $fieldSettings
     * @param array<string, mixed> $validatorConfiguration
     */
    private function updateContentType(
        ContentType $contentType,
        array $fieldSettings,
        array $validatorConfiguration,
        FieldDefinition $fieldDefinition
    ): void {
        $this->repository->sudo(
            static function (
                Repository $repository
            ) use (
                $contentType,
                $fieldSettings,
                $validatorConfiguration,
                $fieldDefinition
            ): void {
                $contentTypeDraft = $repository
                    ->getContentTypeService()
                    ->createContentTypeDraft($contentType);
                $fieldDefinitionUpdateStruct = $repository
                    ->getContentTypeService()
                    ->newFieldDefinitionUpdateStruct();

                $fieldDefinitionUpdateStruct->fieldSettings = $fieldSettings;
                $fieldDefinitionUpdateStruct->validatorConfiguration = $validatorConfiguration;

                $repository
                    ->getContentTypeService()
                    ->updateFieldDefinition(
                        $contentTypeDraft,
                        $fieldDefinition,
                        $fieldDefinitionUpdateStruct
                    );

                $repository
                    ->getContentTypeService()
                    ->publishContentTypeDraft($contentTypeDraft);
            }
        );
    }

    /**
     * @param array<\eZ\Publish\API\Repository\Values\Content\Field> $fields
     */
    private function findUserField(array $fields): ?Field
    {
        $userField = null;

        foreach ($fields as $field) {
            if ($field->fieldTypeIdentifier !== 'ezuser') {
                continue;
            }
            $userField = $field;
        }

        return $userField;
    }
}
