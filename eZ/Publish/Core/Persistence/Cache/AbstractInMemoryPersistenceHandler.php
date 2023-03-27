<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace eZ\Publish\Core\Persistence\Cache;

use eZ\Publish\Core\Persistence\Cache\Adapter\TransactionAwareAdapterInterface;
use eZ\Publish\Core\Persistence\Cache\InMemory\InMemoryCache;
use eZ\Publish\SPI\Persistence\Handler as PersistenceHandler;
use Ibexa\Core\Persistence\Cache\CacheIndicesValidatorInterface;
use Ibexa\Core\Persistence\Cache\Identifier\CacheIdentifierGeneratorInterface;
use Ibexa\Core\Persistence\Cache\Identifier\CacheIdentifierSanitizer;
use Ibexa\Core\Persistence\Cache\LocationPathConverter;

/**
 * Internal abstract handler for use in other SPI Persistence Cache Handlers.
 *
 * @internal Only for use as abstract in eZ\Publish\Core\Persistence\Cache\*Handlers.
 */
abstract class AbstractInMemoryPersistenceHandler extends AbstractInMemoryHandler
{
    /** @var \eZ\Publish\SPI\Persistence\Handler */
    protected $persistenceHandler;

    /** @var \Ibexa\Core\Persistence\Cache\Identifier\CacheIdentifierGeneratorInterface */
    protected $cacheIdentifierGenerator;

    /** @var \Ibexa\Core\Persistence\Cache\Identifier\CacheIdentifierSanitizer */
    protected $cacheIdentifierSanitizer;

    /** @var \Ibexa\Core\Persistence\Cache\LocationPathConverter */
    protected $locationPathConverter;

    public function __construct(
        TransactionAwareAdapterInterface $cache,
        PersistenceLogger $logger,
        InMemoryCache $inMemory,
        PersistenceHandler $persistenceHandler,
        CacheIdentifierGeneratorInterface $cacheIdentifierGenerator,
        CacheIdentifierSanitizer $cacheIdentifierSanitizer,
        LocationPathConverter $locationPathConverter,
        ?CacheIndicesValidatorInterface $cacheIndicesValidator = null
    ) {
        parent::__construct($cache, $logger, $inMemory, $cacheIndicesValidator);

        $this->persistenceHandler = $persistenceHandler;
        $this->cacheIdentifierGenerator = $cacheIdentifierGenerator;
        $this->cacheIdentifierSanitizer = $cacheIdentifierSanitizer;
        $this->locationPathConverter = $locationPathConverter;

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
