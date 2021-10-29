<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\Repository\EventSubscriber;

use Ibexa\Contracts\Core\Repository\ContentTypeService;
use Ibexa\Contracts\Core\Repository\Events\User\DeleteUserEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class DeleteUserSubscriber implements EventSubscriberInterface
{
    /** @var \Ibexa\Contracts\Core\Repository\ContentTypeService */
    private $contentTypeService;

    public function __construct(ContentTypeService $contentTypeService)
    {
        $this->contentTypeService = $contentTypeService;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            DeleteUserEvent::class => 'onDeleteUser',
        ];
    }

    public function onDeleteUser(DeleteUserEvent $event): void
    {
        $this->contentTypeService->deleteUserDrafts($event->getUser()->id);
    }
}

class_alias(DeleteUserSubscriber::class, 'eZ\Publish\Core\Repository\EventSubscriber\DeleteUserSubscriber');
