<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\Event;

use Ibexa\Contracts\Core\Repository\Decorator\SearchServiceDecorator;
use Ibexa\Contracts\Core\Repository\SearchService as SearchServiceInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class SearchService extends SearchServiceDecorator
{
    /** @var \Symfony\Contracts\EventDispatcher\EventDispatcherInterface */
    protected $eventDispatcher;

    public function __construct(
        SearchServiceInterface $innerService,
        EventDispatcherInterface $eventDispatcher
    ) {
        parent::__construct($innerService);

        $this->eventDispatcher = $eventDispatcher;
    }
}

class_alias(SearchService::class, 'eZ\Publish\Core\Event\SearchService');
