<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\Persistence\Cache;

use Ibexa\Core\Persistence\Cache\Adapter\TransactionAwareAdapterInterface;
use Ibexa\Core\Persistence\Cache\InMemory\InMemoryCache;
use Ibexa\Contracts\Core\Persistence\Handler as PersistenceHandler;
use Ibexa\Core\Persistence\Cache\Tag\CacheIdentifierGeneratorInterface;

/**
 * Internal abstract handler for use in other SPI Persistence Cache Handlers.
 *
 * @internal Only for use as a Handler abstract in {@see \Ibexa\Core\Persistence\Cache\}.
 */
abstract class AbstractInMemoryPersistenceHandler extends AbstractInMemoryHandler
{
    /** @var \Ibexa\Contracts\Core\Persistence\Handler */
    protected $persistenceHandler;

    /** @var \Ibexa\Core\Persistence\Cache\Tag\CacheIdentifierGeneratorInterface */
    protected $cacheIdentifierGenerator;

    /**
     * Setups current handler with everything needed.
     *
     * @param \Ibexa\Core\Persistence\Cache\Adapter\TransactionAwareAdapterInterface $cache
     * @param \Ibexa\Core\Persistence\Cache\PersistenceLogger $logger
     * @param \Ibexa\Core\Persistence\Cache\InMemory\InMemoryCache $inMemory
     * @param \Ibexa\Contracts\Core\Persistence\Handler $persistenceHandler
     * @param \Ibexa\Core\Persistence\Cache\Tag\CacheIdentifierGeneratorInterface $cacheIdentifierGenerator
     */
    public function __construct(
        TransactionAwareAdapterInterface $cache,
        PersistenceLogger $logger,
        InMemoryCache $inMemory,
        PersistenceHandler $persistenceHandler,
        CacheIdentifierGeneratorInterface $cacheIdentifierGenerator
    ) {
        parent::__construct($cache, $logger, $inMemory);

        $this->persistenceHandler = $persistenceHandler;
        $this->cacheIdentifierGenerator = $cacheIdentifierGenerator;

        $this->init();
    }

    /**
     * Optional function to initialize handler without having to overload __construct().
     */
    protected function init(): void
    {
        // overload to add init logic if needed in handler
    }
}

class_alias(AbstractInMemoryPersistenceHandler::class, 'eZ\Publish\Core\Persistence\Cache\AbstractInMemoryPersistenceHandler');
