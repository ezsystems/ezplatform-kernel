<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Core\Persistence\Cache;

use Ibexa\Contracts\Core\Persistence\TransactionHandler;

/**
 * @covers \Ibexa\Core\Persistence\Cache\TransactionHandler
 */
class TransactionHandlerTest extends AbstractCacheHandlerTest
{
    public function getHandlerMethodName(): string
    {
        return 'transactionHandler';
    }

    public function getHandlerClassName(): string
    {
        return TransactionHandler::class;
    }

    public function providerForUnCachedMethods(): array
    {
        // string $method, array $arguments, array $arguments, array? $cacheTagGeneratingArguments, array? $cacheKeyGeneratingArguments, array? $tags, string? $key
        return [
            ['beginTransaction', []],
            ['commit', []],
        ];
    }

    public function providerForCachedLoadMethodsHit(): array
    {
        // string $method, array $arguments, array? $cacheIdentifierGeneratorArguments, array? $cacheIdentifierGeneratorResults, string $key, mixed? $data
        return [
        ];
    }

    public function providerForCachedLoadMethodsMiss(): array
    {
        // string $method, array $arguments, array? $cacheIdentifierGeneratorArguments, array? $cacheIdentifierGeneratorResults, string $key, mixed? $data
        return [
        ];
    }

    public function testRollback()
    {
        $this->loggerMock
            ->expects($this->once())
            ->method('logCall');

        $this->cacheMock
            ->expects($this->never())
            ->method('clear');

        $this->cacheMock
            ->expects($this->once())
            ->method('rollbackTransaction');

        $innerHandlerMock = $this->createMock(TransactionHandler::class);
        $this->persistenceHandlerMock
            ->expects($this->once())
            ->method('transactionHandler')
            ->willReturn($innerHandlerMock);

        $innerHandlerMock
            ->expects($this->once())
            ->method('rollback');

        $handler = $this->persistenceCacheHandler->transactionHandler();
        $handler->rollback();
    }

    public function testCommitStopsCacheTransaction()
    {
        $this->loggerMock
            ->expects($this->once())
            ->method('logCall');

        $this->cacheMock
            ->expects($this->once())
            ->method('commitTransaction');

        $innerHandlerMock = $this->createMock(TransactionHandler::class);
        $this->persistenceHandlerMock
            ->expects($this->once())
            ->method('transactionHandler')
            ->willReturn($innerHandlerMock);

        $innerHandlerMock
            ->expects($this->once())
            ->method('commit');

        $handler = $this->persistenceCacheHandler->transactionHandler();
        $handler->commit();
    }

    public function testBeginTransactionStartsCacheTransaction()
    {
        $this->loggerMock
            ->expects($this->once())
            ->method('logCall');

        $this->cacheMock
            ->expects($this->once())
            ->method('beginTransaction');

        $innerHandlerMock = $this->createMock(TransactionHandler::class);
        $this->persistenceHandlerMock
            ->expects($this->once())
            ->method('transactionHandler')
            ->willReturn($innerHandlerMock);

        $innerHandlerMock
            ->expects($this->once())
            ->method('beginTransaction');

        $handler = $this->persistenceCacheHandler->transactionHandler();
        $handler->beginTransaction();
    }
}

class_alias(TransactionHandlerTest::class, 'eZ\Publish\Core\Persistence\Cache\Tests\TransactionHandlerTest');
