<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\Search\Common\EventSubscriber;

use Ibexa\Contracts\Core\Repository\Events\Section\AssignSectionEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SectionEventSubscriber extends AbstractSearchEventSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            AssignSectionEvent::class => 'onAssignSection',
        ];
    }

    public function onAssignSection(AssignSectionEvent $event)
    {
        $contentInfo = $this->persistenceHandler->contentHandler()->loadContentInfo($event->getContentInfo()->id);
        $this->searchHandler->indexContent(
            $this->persistenceHandler->contentHandler()->load($contentInfo->id, $contentInfo->currentVersionNo)
        );
    }
}

class_alias(SectionEventSubscriber::class, 'eZ\Publish\Core\Search\Common\EventSubscriber\SectionEventSubscriber');
