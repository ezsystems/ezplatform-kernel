<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Core\Persistence\Cache;

use Ibexa\Core\Persistence\Cache\PersistenceLogger;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ibexa\Core\Persistence\Cache\PersistenceLogger::getName
 */
class PersistenceLoggerTest extends TestCase
{
    /** @var \Ibexa\Core\Persistence\Cache\PersistenceLogger */
    protected $logger;

    /**
     * Setup the HandlerTest.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->logger = new PersistenceLogger();
    }

    /**
     * Tear down test (properties).
     */
    protected function tearDown(): void
    {
        unset($this->logger);
        parent::tearDown();
    }

    public function testGetName()
    {
        $this->assertEquals(PersistenceLogger::NAME, $this->logger->getName());
    }

    public function testGetCount()
    {
        $this->assertEquals(0, $this->logger->getCount());
    }

    public function testGetCalls()
    {
        $this->assertEquals([], $this->logger->getCalls());
    }

    public function testLogCall()
    {
        $this->assertNull($this->logger->logCall(__METHOD__));
        $this->logger->logCall(__METHOD__);
        $this->logger->logCall(__METHOD__);
        $this->logger->logCall(__METHOD__, [33]);

        return $this->logger;
    }

    /**
     * @depends testLogCall
     *
     * @param \Ibexa\Core\Persistence\Cache\PersistenceLogger $logger
     */
    public function testGetCountValues($logger)
    {
        $this->assertEquals(4, $logger->getCount());

        return $logger;
    }

    /**
     * @depends testGetCountValues
     *
     * @param \Ibexa\Core\Persistence\Cache\PersistenceLogger $logger
     */
    public function testGetCallValues($logger)
    {
        $calls = $logger->getCalls();
        // As we don't care about the hash index we get the array values instead
        $calls = array_values($calls);

        $method = __CLASS__ . '::testLogCall';

        $this->assertEquals($method, $calls[0]['method']);
        $this->assertEquals([], $calls[0]['arguments']);
        $this->assertCount(1, $calls[0]['traces']);
        $this->assertEquals(['uncached' => 3, 'miss' => 0, 'hit' => 0, 'memory' => 0], $calls[0]['stats']);

        $this->assertEquals($method, $calls[1]['method']);
        $this->assertEquals([33], $calls[1]['arguments']);
        $this->assertCount(1, $calls[1]['traces']);
        $this->assertEquals(['uncached' => 1, 'miss' => 0, 'hit' => 0, 'memory' => 0], $calls[1]['stats']);
    }
}

class_alias(PersistenceLoggerTest::class, 'eZ\Publish\Core\Persistence\Cache\Tests\PersistenceLoggerTest');
