<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Bundle\Core\EventListener;

use Ibexa\Contracts\Core\Persistence\Content\ContentInfo;
use Ibexa\Contracts\Core\Persistence\Content\Location;
use Ibexa\Contracts\Core\Persistence\Handler as PersistenceHandler;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Search\Handler as SearchHandler;
use Ibexa\Core\Search\Common\BackgroundIndexer as BackgroundIndexerInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Kernel and Console terminate event based background indexer.
 */
class BackgroundIndexingTerminateListener implements BackgroundIndexerInterface, EventSubscriberInterface
{
    use LoggerAwareTrait;

    /** @var \Ibexa\Contracts\Core\Persistence\Handler */
    protected $persistenceHandler;

    /** @var \Ibexa\Contracts\Core\Search\Handler */
    protected $searchHandler;

    /** @var \Ibexa\Contracts\Core\Persistence\Content\ContentInfo[] */
    protected $contentInfo = [];

    /** @var \Ibexa\Contracts\Core\Persistence\Content\Location[] */
    protected $locations = [];

    public function __construct(PersistenceHandler $persistenceHandler, SearchHandler $searchHandler)
    {
        $this->persistenceHandler = $persistenceHandler;
        $this->searchHandler = $searchHandler;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::TERMINATE => 'reindex',
            KernelEvents::EXCEPTION => 'reindex',
            ConsoleEvents::TERMINATE => 'reindex',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function registerContent(ContentInfo $contentInfo)
    {
        $this->contentInfo[] = $contentInfo;
    }

    /**
     * {@inheritdoc}
     */
    public function registerLocation(Location $location)
    {
        $this->locations[] = $location;
    }

    public function reindex()
    {
        $contentHandler = $this->persistenceHandler->contentHandler();
        $contentIndexed = [];
        $contentRemoved = [];
        foreach ($this->contentInfo as $contentInfo) {
            if (isset($contentIndexed[$contentInfo->id]) || isset($contentRemoved[$contentInfo->id])) {
                continue;
            }

            try {
                // In case version has changed we make sure to fetch fresh ContentInfo
                $contentInfo = $contentHandler->loadContentInfo($contentInfo->id);
                if ($contentInfo->isPublished) {
                    $this->searchHandler->indexContent(
                        $contentHandler->load($contentInfo->id, $contentInfo->currentVersionNo)
                    );
                    $contentIndexed[$contentInfo->id] = $contentInfo->id;
                    continue;
                }
            } catch (NotFoundException $e) {
                // Catch this so we delete the index for this content below
            }

            $this->searchHandler->deleteContent($contentInfo->id);
            if ($contentInfo->mainLocationId) {
                $this->searchHandler->deleteLocation($contentInfo->mainLocationId, $contentInfo->id);
            }
            $contentRemoved[$contentInfo->id] = $contentInfo->id;
        }
        $this->contentInfo = [];

        foreach ($this->locations as $location) {
            if (isset($contentIndexed[$location->contentId]) || isset($contentRemoved[$location->contentId])) {
                continue;
            }

            try {
                // In case version has changed we make sure to fetch fresh ContentInfo
                $contentInfo = $contentHandler->loadContentInfo($location->contentId);
                if ($contentInfo->isPublished) {
                    $this->searchHandler->indexContent(
                        $contentHandler->load($contentInfo->id, $contentInfo->currentVersionNo)
                    );
                    $contentIndexed[$contentInfo->id] = $contentInfo->id;
                    continue;
                }
            } catch (NotFoundException $e) {
                // Catch this so we delete the index for this content below
            }

            $this->searchHandler->deleteContent($location->contentId);
            $this->searchHandler->deleteLocation($location->id, $location->contentId);
            $contentRemoved[$location->contentId] = $location->contentId;
        }
        $this->locations = [];

        if ($this->logger instanceof LoggerInterface && (!empty($contentIndexed) || !empty($contentRemoved))) {
            $this->logger->warning(
                sprintf(
                    'Exceptions detected on search index. Content %s was re-indexed and %s was removed from index',
                    implode(', ', $contentIndexed),
                    implode(', ', $contentRemoved)
                )
            );
        }
    }
}

class_alias(BackgroundIndexingTerminateListener::class, 'eZ\Bundle\EzPublishCoreBundle\EventListener\BackgroundIndexingTerminateListener');
