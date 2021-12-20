<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace eZ\Bundle\EzPublishIOBundle\EventListener;

use eZ\Bundle\EzPublishIOBundle\BinaryStreamResponse;
use eZ\Publish\Core\IO\IOConfigProvider;
use eZ\Publish\Core\IO\IOServiceInterface;
use eZ\Publish\Core\IO\Values\MissingBinaryFile;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Listens for IO files requests, and streams them.
 */
class StreamFileListener implements EventSubscriberInterface
{
    /** @var \eZ\Publish\Core\IO\IOServiceInterface */
    private $ioService;

    /** @var \eZ\Publish\Core\IO\IOConfigProvider */
    private $ioConfigResolver;

    public function __construct(IOServiceInterface $ioService, IOConfigProvider $ioConfigResolver)
    {
        $this->ioService = $ioService;
        $this->ioConfigResolver = $ioConfigResolver;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 42],
        ];
    }

    public function onKernelRequest(RequestEvent $event)
    {
        if ($event->getRequestType() !== HttpKernelInterface::MASTER_REQUEST) {
            return;
        }

        $request = $event->getRequest();
        $urlPrefix = $this->ioConfigResolver->getUrlPrefix();
        $pathInfo = $request->getPathInfo();

        if (strpos($urlPrefix, '://') !== false) {
            $uri = $request->getSchemeAndHttpHost() . $pathInfo;
        } else {
            $uri = $pathInfo;
        }

        if (!$this->isIoUri($uri, $urlPrefix)) {
            return;
        }

        $binaryFile = $this->ioService->loadBinaryFileByUri(urldecode($uri));
        if ($binaryFile instanceof MissingBinaryFile) {
            throw new NotFoundHttpException("Could not find 'BinaryFile' with identifier '$uri'");
        }

        $event->setResponse(
            new BinaryStreamResponse(
                $binaryFile,
                $this->ioService
            )
        );
    }

    /**
     * Tests if $uri is an IO file uri root.
     *
     * @param string $uri
     * @param string $urlPrefix
     *
     * @return bool
     */
    private function isIoUri($uri, $urlPrefix)
    {
        return strpos(ltrim($uri, '/'), $urlPrefix) === 0;
    }
}
