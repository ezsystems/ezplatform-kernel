<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Core\Command;

use Ibexa\Bundle\Core\URLChecker\URLCheckerInterface;
use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Contracts\Core\Repository\URLService;
use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\Contracts\Core\Repository\Values\URL\Query\Criterion;
use Ibexa\Contracts\Core\Repository\Values\URL\Query\SortClause;
use Ibexa\Contracts\Core\Repository\Values\URL\URLQuery;
use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CheckURLsCommand extends Command implements BackwardCompatibleCommand
{
    private const DEFAULT_ITERATION_COUNT = 50;
    private const DEFAULT_REPOSITORY_USER = 'admin';

    /** @var \Ibexa\Contracts\Core\Repository\UserService */
    private $userService;

    /** @var \Ibexa\Contracts\Core\Repository\PermissionResolver */
    private $permissionResolver;

    /** @var \Ibexa\Contracts\Core\Repository\URLService */
    private $urlService;

    /** @var \Ibexa\Bundle\Core\URLChecker\URLCheckerInterface */
    private $urlChecker;

    public function __construct(
        UserService $userService,
        PermissionResolver $permissionResolver,
        URLService $urlService,
        URLCheckerInterface $urlChecker
    ) {
        parent::__construct('ibexa:check-urls');

        $this->userService = $userService;
        $this->permissionResolver = $permissionResolver;
        $this->urlService = $urlService;
        $this->urlChecker = $urlChecker;
    }

    public function configure(): void
    {
        $this->setAliases($this->getDeprecatedAliases());
        $this->setDescription('Checks validity of external URLs');
        $this->addOption(
            'iteration-count',
            'c',
            InputOption::VALUE_OPTIONAL,
            'Number of URLs to check in a single iteration set to avoid using too much memory',
            self::DEFAULT_ITERATION_COUNT
        );

        $this->addOption(
            'user',
            'u',
            InputOption::VALUE_OPTIONAL,
            'eZ Platform username (with Role containing at least content Policies: read, versionread, edit, remove, versionremove)',
            self::DEFAULT_REPOSITORY_USER
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->permissionResolver->setCurrentUserReference(
            $this->userService->loadUserByLogin($input->getOption('user'))
        );

        $limit = $input->getOption('iteration-count');
        if (!ctype_digit($limit) || (int)$limit < 1) {
            throw new RuntimeException("'--iteration-count' should be > 0, you passed '{$limit}'");
        }

        $limit = (int)$limit;

        $query = new URLQuery();
        $query->filter = new Criterion\VisibleOnly();
        $query->sortClauses = [
            new SortClause\URL(),
        ];
        $query->offset = 0;
        $query->limit = $limit;

        $totalCount = $this->getTotalCount();

        $progress = new ProgressBar($output, $totalCount);
        $progress->start();
        while ($query->offset < $totalCount) {
            $this->urlChecker->check($query);

            $progress->advance(min($limit, $totalCount - $query->offset));
            $query->offset += $limit;
        }
        $progress->finish();

        return 0;
    }

    private function getTotalCount(): int
    {
        $query = new URLQuery();
        $query->filter = new Criterion\VisibleOnly();
        $query->limit = 0;

        return $this->urlService->findUrls($query)->totalCount;
    }

    public function getDeprecatedAliases(): array
    {
        return ['ezplatform:check-urls'];
    }
}

class_alias(CheckURLsCommand::class, 'eZ\Bundle\EzPublishCoreBundle\Command\CheckURLsCommand');
