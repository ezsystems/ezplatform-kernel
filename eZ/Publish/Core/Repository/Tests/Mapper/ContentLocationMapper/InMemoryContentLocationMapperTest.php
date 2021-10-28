<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace eZ\Publish\Core\Repository\Tests\Mapper\ContentLocationMapper;

use eZ\Publish\Core\Repository\Mapper\ContentLocationMapper\InMemoryContentLocationMapper;
use PHPUnit\Framework\TestCase;

class InMemoryContentLocationMapperTest extends TestCase
{
    /** @var \eZ\Publish\Core\Repository\Mapper\ContentLocationMapper\ContentLocationMapper */
    private $mapper;

    protected function setUp(): void
    {
        parent::setUp();

        $map = [
            1 => 2,
            3 => 4,
            5 => 6,
        ];
        $this->mapper = new InMemoryContentLocationMapper($map);
    }

    public function testGetMapping(): void
    {
        self::assertEquals(2, $this->mapper->getMapping(1));
    }

    public function testHasMapping(): void
    {
        self::assertTrue($this->mapper->hasMapping(5));
        self::assertFalse($this->mapper->hasMapping(7));
    }

    public function testSetMapping(): void
    {
        self::assertFalse($this->mapper->hasMapping(7));

        $this->mapper->setMapping(7, 8);

        self::assertTrue($this->mapper->hasMapping(7));
        self::assertEquals(8, $this->mapper->getMapping(7));
    }

    public function testRemoveMapping(): void
    {
        self::assertTrue($this->mapper->hasMapping(3));

        $this->mapper->removeMapping(3);

        self::assertFalse($this->mapper->hasMapping(3));
    }
}
