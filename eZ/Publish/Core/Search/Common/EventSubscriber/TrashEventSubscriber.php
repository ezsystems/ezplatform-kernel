<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace eZ\Publish\Core\Search\Common\EventSubscriber;

use eZ\Publish\API\Repository\Events\Trash\DeleteTrashItemEvent;
use eZ\Publish\API\Repository\Events\Trash\RecoverEvent;
use eZ\Publish\API\Repository\Events\Trash\TrashEvent;
use eZ\Publish\API\Repository\Values\Content\TrashItem;
use eZ\Publish\SPI\Persistence\Handler as PersistenceHandler;
use eZ\Publish\SPI\Search\Handler as SearchHandler;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class TrashEventSubscriber extends AbstractSearchEventSubscriber implements EventSubscriberInterface
{
    public function __construct(SearchHandler $searchHandler, PersistenceHandler $persistenceHandler)
    {
        parent::__construct($searchHandler, $persistenceHandler);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            RecoverEvent::class => 'onRecover',
            TrashEvent::class => 'onTrash',
            DeleteTrashItemEvent::class => 'onDeleteTrashItem',
        ];
    }

    public function onRecover(RecoverEvent $event)
    {
        $this->indexSubtree($event->getLocation()->id);
    }

    public function onTrash(TrashEvent $event)
    {
        if ($event->getTrashItem() instanceof TrashItem) {
            $this->searchHandler->deleteContent(
                $event->getLocation()->contentId
            );
        }

        $this->searchHandler->deleteLocation(
            $event->getLocation()->id,
            $event->getLocation()->contentId
        );
    }

    public function onDeleteTrashItem(DeleteTrashItemEvent $event): void
    {
        $contentHandler = $this->persistenceHandler->contentHandler();

        $reverseRelationContentIds = $event->getResult()->reverseRelationContentIds;
        foreach ($reverseRelationContentIds as $contentId) {
            $persistenceContent = $contentHandler->load($contentId);

            $this->searchHandler->indexContent($persistenceContent);
        }
    }
}
