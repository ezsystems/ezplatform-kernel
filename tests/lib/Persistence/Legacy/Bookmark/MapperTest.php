<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Core\Persistence\Legacy\Bookmark;

use Ibexa\Contracts\Core\Persistence\Bookmark\Bookmark;
use Ibexa\Contracts\Core\Persistence\Bookmark\CreateStruct;
use Ibexa\Core\Persistence\Legacy\Bookmark\Mapper;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ibexa\Core\Persistence\Legacy\Bookmark\Mapper
 */
class MapperTest extends TestCase
{
    /** @var \Ibexa\Core\Persistence\Legacy\Bookmark\Mapper */
    private $mapper;

    protected function setUp(): void
    {
        $this->mapper = new Mapper();
    }

    public function testCreateBookmarkFromCreateStruct()
    {
        $createStruct = new CreateStruct([
            'name' => 'Contact',
            'locationId' => 54,
            'userId' => 87,
        ]);

        $this->assertEquals(new Bookmark([
            'name' => 'Contact',
            'locationId' => 54,
            'userId' => 87,
        ]), $this->mapper->createBookmarkFromCreateStruct($createStruct));
    }

    public function testExtractBookmarksFromRows()
    {
        $rows = [
            [
                'id' => '12',
                'name' => 'Home',
                'node_id' => '2',
                'user_id' => '78',
            ],
            [
                'id' => '75',
                'name' => 'Contact',
                'node_id' => '54',
                'user_id' => '87',
            ],
        ];

        $objects = [
            new Bookmark([
                'id' => 12,
                'name' => 'Home',
                'locationId' => 2,
                'userId' => 78,
            ]),
            new Bookmark([
                'id' => 75,
                'name' => 'Contact',
                'locationId' => 54,
                'userId' => 87,
            ]),
        ];

        $this->assertEquals($objects, $this->mapper->extractBookmarksFromRows($rows));
    }
}

class_alias(MapperTest::class, 'eZ\Publish\Core\Persistence\Legacy\Tests\Bookmark\MapperTest');
