<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Core\Persistence\Legacy\Notification;

use Ibexa\Contracts\Core\Persistence\Notification\Notification;
use Ibexa\Contracts\Core\Persistence\Notification\UpdateStruct;
use Ibexa\Core\Persistence\Legacy\Notification\Mapper;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ibexa\Core\Persistence\Legacy\Notification\Mapper
 */
class MapperTest extends TestCase
{
    /** @var \Ibexa\Core\Persistence\Legacy\Notification\Mapper */
    private $mapper;

    protected function setUp(): void
    {
        $this->mapper = new Mapper();
    }

    public function testExtractNotificationsFromRows()
    {
        $rows = [
            [
                'id' => 1,
                'owner_id' => 5,
                'type' => 'FOO',
                'created' => 1529913161,
                'is_pending' => 0,
                'data' => null,
            ],
            [
                'id' => 1,
                'owner_id' => 5,
                'type' => 'BAR',
                'created' => 1529910161,
                'is_pending' => 1,
                'data' => json_encode([
                    'foo' => 'Foo',
                    'bar' => 'Bar',
                    'baz' => ['B', 'A', 'Z'],
                ]),
            ],
        ];

        $objects = [
            new Notification([
                'id' => 1,
                'ownerId' => 5,
                'type' => 'FOO',
                'created' => 1529913161,
                'isPending' => false,
                'data' => [],
            ]),
            new Notification([
                'id' => 1,
                'ownerId' => 5,
                'type' => 'BAR',
                'created' => 1529910161,
                'isPending' => true,
                'data' => [
                    'foo' => 'Foo',
                    'bar' => 'Bar',
                    'baz' => ['B', 'A', 'Z'],
                ],
            ]),
        ];

        $this->assertEquals($objects, $this->mapper->extractNotificationsFromRows($rows));
    }

    public function testExtractNotificationsFromRowsThrowsRuntimeException()
    {
        $this->expectException(\RuntimeException::class);

        $rows = [
            [
                'id' => 1,
                'owner_id' => 5,
                'type' => 'FOO',
                'created' => 1529913161,
                'is_pending' => false,
                'data' => '{ InvalidJSON }',
            ],
        ];

        $this->mapper->extractNotificationsFromRows($rows);
    }

    public function testCreateNotificationFromUpdateStruct()
    {
        $updateStruct = new UpdateStruct([
            'isPending' => false,
        ]);

        $this->assertEquals(new Notification([
            'isPending' => false,
        ]), $this->mapper->createNotificationFromUpdateStruct($updateStruct));
    }
}

class_alias(MapperTest::class, 'eZ\Publish\Core\Persistence\Legacy\Tests\Notification\MapperTest');
