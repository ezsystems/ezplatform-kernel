<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Bundle\Core\Command;

use Ibexa\Contracts\Core\Repository\ContentTypeService;
use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Contracts\Core\Repository\SearchService;
use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\Repository\Values\Content\LocationQuery;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

/**
 * Console command for deep copying subtree from one location to another.
 */
class CopySubtreeCommand extends Command implements BackwardCompatibleCommand
{
    /** @var \Ibexa\Contracts\Core\Repository\LocationService */
    private $locationService;

    /** @var \Ibexa\Contracts\Core\Repository\PermissionResolver */
    private $permissionResolver;

    /** @var \Ibexa\Contracts\Core\Repository\UserService */
    private $userService;

    /** @var \Ibexa\Contracts\Core\Repository\ContentTypeService */
    private $contentTypeService;

    /** @var \Ibexa\Contracts\Core\Repository\SearchService */
    private $searchService;

    /**
     * @param \Ibexa\Contracts\Core\Repository\LocationService $locationService
     * @param \Ibexa\Contracts\Core\Repository\PermissionResolver $permissionResolver
     * @param \Ibexa\Contracts\Core\Repository\UserService $userService
     * @param \Ibexa\Contracts\Core\Repository\ContentTypeService $contentTypeService
     * @param \Ibexa\Contracts\Core\Repository\SearchService $searchService
     */
    public function __construct(
        LocationService $locationService,
        PermissionResolver $permissionResolver,
        UserService $userService,
        ContentTypeService $contentTypeService,
        SearchService $searchService
    ) {
        parent::__construct();
        $this->locationService = $locationService;
        $this->permissionResolver = $permissionResolver;
        $this->userService = $userService;
        $this->contentTypeService = $contentTypeService;
        $this->searchService = $searchService;
    }

    protected function configure()
    {
        $this
            ->setName('ibexa:copy-subtree')
            ->setAliases($this->getDeprecatedAliases())
            ->addArgument(
                'source-location-id',
                InputArgument::REQUIRED,
                'ID of source Location'
            )
            ->addArgument(
                'target-location-id',
                InputArgument::REQUIRED,
                'ID of target Location'
            )
            ->addOption(
                'user',
                'u',
                InputOption::VALUE_OPTIONAL,
                'eZ Platform username (with Role containing at least content policies: create, read)',
                'admin'
            )
            ->setDescription('Copies a subtree from one Location to another');
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        parent::initialize($input, $output);
        $this->permissionResolver->setCurrentUserReference(
            $this->userService->loadUserByLogin($input->getOption('user'))
        );
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int|null
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     * @throws \Ibexa\Core\Base\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $sourceLocationId = $input->getArgument('source-location-id');
        $targetLocationId = $input->getArgument('target-location-id');

        $sourceLocation = $this->locationService->loadLocation($sourceLocationId);
        $targetLocation = $this->locationService->loadLocation($targetLocationId);

        if (stripos($targetLocation->pathString, $sourceLocation->pathString) !== false) {
            throw new InvalidArgumentException(
                'target-location-id',
                'Cannot copy subtree to its own descendant Location'
            );
        }

        $targetContentType = $this->contentTypeService->loadContentType(
            $targetLocation->getContentInfo()->contentTypeId
        );

        if (!$targetContentType->isContainer) {
            throw new InvalidArgumentException(
                'target-location-id',
                'The selected Location cannot contain children'
            );
        }
        $questionHelper = $this->getHelper('question');
        $question = new ConfirmationQuestion(
            sprintf(
                'Are you sure you want to copy `%s` subtree (no. of children: %d) into `%s`? This may take a while for a big number of nested children [Y/n]? ',
                $sourceLocation->contentInfo->name,
                $this->getAllChildrenCount($sourceLocation),
                $targetLocation->contentInfo->name
            )
        );

        if (!$input->getOption('no-interaction') && !$questionHelper->ask($input, $output, $question)) {
            return 0;
        }

        $this->locationService->copySubtree(
            $sourceLocation,
            $targetLocation
        );

        $output->writeln(
            '<info>Finished</info>'
        );

        return 0;
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Location $location
     *
     * @return int
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    protected function getAllChildrenCount(Location $location): int
    {
        $query = new LocationQuery([
            'filter' => new Criterion\Subtree($location->pathString),
        ]);

        $searchResults = $this->searchService->findLocations($query);

        return $searchResults->totalCount;
    }

    public function getDeprecatedAliases(): array
    {
        return ['ezplatform:copy-subtree'];
    }
}

class_alias(CopySubtreeCommand::class, 'eZ\Bundle\EzPublishCoreBundle\Command\CopySubtreeCommand');
