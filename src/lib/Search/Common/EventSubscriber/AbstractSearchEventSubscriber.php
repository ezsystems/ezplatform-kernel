<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\Search\Common\EventSubscriber;

use Ibexa\Contracts\Core\Persistence\Handler as PersistenceHandler;
use Ibexa\Contracts\Core\Search\Handler as SearchHandler;

/**
 * @internal
 */
abstract class AbstractSearchEventSubscriber
{
    /** @var \Ibexa\Contracts\Core\Search\Handler */
    protected $searchHandler;

    /** @var \Ibexa\Contracts\Core\Persistence\Handler */
    protected $persistenceHandler;

    public function __construct(
        SearchHandler $searchHandler,
        PersistenceHandler $persistenceHandler
    ) {
        $this->searchHandler = $searchHandler;
        $this->persistenceHandler = $persistenceHandler;
    }

    public function indexSubtree(int $locationId): void
    {
        $contentHandler = $this->persistenceHandler->contentHandler();
        $locationHandler = $this->persistenceHandler->locationHandler();

        $processedContentIdSet = [];
        $subtreeIds = $locationHandler->loadSubtreeIds($locationId);
        $contentInfoList = $contentHandler->loadContentInfoList(array_values($subtreeIds));

        foreach ($subtreeIds as $locationId => $contentId) {
            $this->searchHandler->indexLocation(
                $locationHandler->load($locationId)
            );

            if (isset($processedContentIdSet[$contentId])) {
                continue;
            }

            $this->searchHandler->indexContent(
                $contentHandler->load(
                    $contentId,
                    $contentInfoList[$contentId]->currentVersionNo
                )
            );

            // Content could be found in multiple Locations of the subtree,
            // but we need to (re)index it only once
            $processedContentIdSet[$contentId] = true;
        }
    }
}

class_alias(AbstractSearchEventSubscriber::class, 'eZ\Publish\Core\Search\Common\EventSubscriber\AbstractSearchEventSubscriber');
