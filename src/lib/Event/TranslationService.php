<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\Event;

use Ibexa\Contracts\Core\Repository\Decorator\TranslationServiceDecorator;
use Ibexa\Contracts\Core\Repository\TranslationService as TranslationServiceInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class TranslationService extends TranslationServiceDecorator
{
    /** @var \Symfony\Contracts\EventDispatcher\EventDispatcherInterface */
    protected $eventDispatcher;

    public function __construct(
        TranslationServiceInterface $innerService,
        EventDispatcherInterface $eventDispatcher
    ) {
        parent::__construct($innerService);

        $this->eventDispatcher = $eventDispatcher;
    }
}

class_alias(TranslationService::class, 'eZ\Publish\Core\Event\TranslationService');
