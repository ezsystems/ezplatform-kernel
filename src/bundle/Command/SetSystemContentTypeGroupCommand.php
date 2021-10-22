<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Command;

use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\PermissionResolver;
use eZ\Publish\API\Repository\UserService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @internal
 */
final class SetSystemContentTypeGroupCommand extends Command
{
    private const DEFAULT_REPOSITORY_USER = 'admin';

    private const BEFORE_RUNNING_HINTS = <<<EOT
<error>Before you continue:</error>
- Make sure to back up your database.
- Run this command in production environment using <info>--env=prod</info>
- Manually clear SPI/HTTP cache after running this command.
EOT;

    protected static $defaultName = 'ibexa:content-type-group:set-system';

    /** @var \eZ\Publish\API\Repository\ContentTypeService */
    private $contentTypeService;

    /** @var \eZ\Publish\API\Repository\PermissionResolver */
    private $permissionResolver;

    /** @var \eZ\Publish\API\Repository\UserService */
    private $userService;

    public function __construct(
        ContentTypeService $contentTypeService,
        PermissionResolver $permissionResolver,
        UserService $userService
    ) {
        parent::__construct();

        $this->contentTypeService = $contentTypeService;
        $this->permissionResolver = $permissionResolver;
        $this->userService = $userService;
    }

    protected function configure()
    {
        $beforeRunningHints = self::BEFORE_RUNNING_HINTS;
        $this
            ->addArgument('content-type-group-identifier', InputArgument::REQUIRED, 'ContentTypGroup identifier')
            ->addOption(
                'system',
                null,
                InputOption::VALUE_NEGATABLE,
                'Please set true if ContentTypeGroup should be a system group or false if it should not be a system group.'
            )
            ->addOption(
                'user',
                'u',
                InputOption::VALUE_OPTIONAL,
                'eZ Platform username (with Role containing at least content policies: remove, read, versionread)',
                self::DEFAULT_REPOSITORY_USER
            )
            ->setDescription('Sets information if ContentTypeGroup is a system group')
            ->setHelp(
                <<<EOT
The command <info>%command.name%</info> sets `is_system` flag for ContentTypeGroup which determines if ContentTypeGroup is a system group.

{$beforeRunningHints}
EOT
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if ($input->getOption('system') === null) {
            $output->error('Please provide `system` option to determine if ContentTypeGroup should be system group or not.');

            return 0;
        }

        $this->permissionResolver->setCurrentUserReference(
            $this->userService->loadUserByLogin($input->getOption('user'))
        );

        $io = new SymfonyStyle($input, $output);
        $io->title('Sets ContentTypeGroup as a system group or not.');
        $io->writeln([
            'This setting determines if ContentTypeGroup is visible on the list of ContentTypeGroups.',
        ]);

        $identifier = ($input->getArgument('content-type-group-identifier'));
        try {
            $contentTypeGroup = $this->contentTypeService->loadContentTypeGroupByIdentifier($identifier);
        } catch (NotFoundException $e) {
            $io->warning(sprintf('Can\'t find ContentTypeGroup with identifier: %s', $identifier));
        }
        $isSystem = $input->getOption('system');
        $isSystemText = $isSystem ? 'system' : 'no system';
        $io->note(sprintf('ContentTypeGroup with identifier `%s` will be set as a %s group.', $identifier, $isSystemText));

        if (!$io->confirm('Do you want to continue?')) {
            return 0;
        }

        $updateStruct = $this->contentTypeService->newContentTypeGroupUpdateStruct();
        $updateStruct->isSystem = $isSystem;

        $this->contentTypeService->updateContentTypeGroup($contentTypeGroup, $updateStruct);

        $io->success(sprintf('Done! ContentTypeGroup is set as a %s group.', $isSystemText));
        $io->info('Please, remember to manually clear SPI/HTTP cache after running this command.');

        return 0;
    }
}
