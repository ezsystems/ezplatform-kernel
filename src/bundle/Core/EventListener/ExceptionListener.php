<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Bundle\Core\EventListener;

use Exception;
use Ibexa\Contracts\Core\Repository\Exceptions\BadStateException;
use Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException;
use Ibexa\Core\Base\Translatable;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Contracts\Translation\TranslatorInterface;

class ExceptionListener implements EventSubscriberInterface
{
    /** @var \Symfony\Contracts\Translation\TranslatorInterface */
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::EXCEPTION => ['onKernelException', 10],
        ];
    }

    public function onKernelException(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();

        if ($exception instanceof NotFoundException) {
            $event->setThrowable(new NotFoundHttpException($this->getTranslatedMessage($exception), $exception));
        } elseif ($exception instanceof UnauthorizedException) {
            $event->setThrowable(new AccessDeniedException($this->getTranslatedMessage($exception), $exception));
        } elseif ($exception instanceof BadStateException || $exception instanceof InvalidArgumentException) {
            $event->setThrowable(new BadRequestHttpException($this->getTranslatedMessage($exception), $exception));
        } elseif ($exception instanceof Translatable) {
            $event->setThrowable(
                new HttpException(
                    Response::HTTP_INTERNAL_SERVER_ERROR,
                    get_class($exception) . ': ' . $this->getTranslatedMessage($exception),
                    $exception
                )
            );
        }
    }

    /**
     * Translates the exception message if it is translatable.
     *
     * @param \Exception $exception
     *
     * @return string
     */
    private function getTranslatedMessage(Exception $exception)
    {
        $message = $exception->getMessage();
        if ($exception instanceof Translatable) {
            $message = $this->translator->trans($exception->getMessageTemplate(), $exception->getParameters(), 'repository_exceptions');
        }

        return $message;
    }
}

class_alias(ExceptionListener::class, 'eZ\Bundle\EzPublishCoreBundle\EventListener\ExceptionListener');
