<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace eZ\Bundle\EzPublishCoreBundle\EventListener;

use eZ\Publish\Core\Helper\ContentPreviewHelper;
use eZ\Publish\Core\MVC\Symfony\Controller\Content\PreviewController;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class PreviewRequestListener implements EventSubscriberInterface
{
    /** @var \Symfony\Component\HttpFoundation\RequestStack */
    private $requestStack;

    /** @var \eZ\Publish\Core\Helper\ContentPreviewHelper */
    private $contentPreviewHelper;

    public function __construct(RequestStack $requestStack, ContentPreviewHelper $contentPreviewHelper)
    {
        $this->requestStack = $requestStack;
        $this->contentPreviewHelper = $contentPreviewHelper;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 200],
        ];
    }

    /**
     * @param \Symfony\Component\HttpKernel\Event\RequestEvent $event
     */
    public function onKernelRequest(RequestEvent $event): void
    {
        $this->contentPreviewHelper->setPreviewActive(true);

        if ($event->getRequestType() === HttpKernelInterface::MASTER_REQUEST) {
            return;
        }

        $parentRequest = $this->requestStack->getParentRequest();
        if ($parentRequest !== null && $parentRequest->attributes->get(PreviewController::PREVIEW_PARAMETER_NAME, false)) {
            $this->requestStack->getCurrentRequest()->attributes->set(PreviewController::PREVIEW_PARAMETER_NAME, true);
        }
    }
}
