<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\Search\Common\EventSubscriber;

use Ibexa\Contracts\Core\Repository\Events\Trash\RecoverEvent;
use Ibexa\Contracts\Core\Repository\Events\Trash\TrashEvent;
use Ibexa\Contracts\Core\Repository\Values\Content\TrashItem;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class TrashEventSubscriber extends AbstractSearchEventSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            RecoverEvent::class => 'onRecover',
            TrashEvent::class => 'onTrash',
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
}

class_alias(TrashEventSubscriber::class, 'eZ\Publish\Core\Search\Common\EventSubscriber\TrashEventSubscriber');
