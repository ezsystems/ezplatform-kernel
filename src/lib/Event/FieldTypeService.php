<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\Event;

use Ibexa\Contracts\Core\Repository\Decorator\FieldTypeServiceDecorator;
use Ibexa\Contracts\Core\Repository\FieldTypeService as FieldTypeServiceInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class FieldTypeService extends FieldTypeServiceDecorator
{
    /** @var \Symfony\Contracts\EventDispatcher\EventDispatcherInterface */
    protected $eventDispatcher;

    public function __construct(
        FieldTypeServiceInterface $innerService,
        EventDispatcherInterface $eventDispatcher
    ) {
        parent::__construct($innerService);

        $this->eventDispatcher = $eventDispatcher;
    }
}

class_alias(FieldTypeService::class, 'eZ\Publish\Core\Event\FieldTypeService');
