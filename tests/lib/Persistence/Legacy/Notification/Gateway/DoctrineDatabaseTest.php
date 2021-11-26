<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Core\Persistence\Legacy\Notification\Gateway;

use Doctrine\DBAL\FetchMode;
use Ibexa\Contracts\Core\Persistence\Notification\CreateStruct;
use Ibexa\Contracts\Core\Persistence\Notification\Notification;
use Ibexa\Core\Persistence\Legacy\Notification\Gateway\DoctrineDatabase;
use Ibexa\Tests\Core\Persistence\Legacy\TestCase;

/**
 * @covers \Ibexa\Core\Persistence\Legacy\Notification\Gateway\DoctrineDatabase::insert
 */
class DoctrineDatabaseTest extends TestCase
{
    public const EXISTING_NOTIFICATION_ID = 1;
    public const EXISTING_NOTIFICATION_DATA = [
        'id' => 1,
        'owner_id' => 14,
        'is_pending' => 1,
        'type' => 'Workflow:Review',
        'created' => 1529995052,
        'data' => null,
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $this->insertDatabaseFixture(
            __DIR__ . '/../_fixtures/notifications.php'
        );
    }

    public function testInsert()
    {
        $id = $this->getGateway()->insert(new CreateStruct([
            'ownerId' => 14,
            'isPending' => true,
            'type' => 'Workflow:Review',
            'created' => 1529995052,
            'data' => null,
        ]));

        $data = $this->loadNotification($id);

        $this->assertEquals([
            'id' => $id,
            'owner_id' => '14',
            'is_pending' => 1,
            'type' => 'Workflow:Review',
            'created' => '1529995052',
            'data' => 'null',
        ], $data);
    }

    public function testGetNotificationById()
    {
        $data = $this->getGateway()->getNotificationById(self::EXISTING_NOTIFICATION_ID);

        $this->assertEquals([
            self::EXISTING_NOTIFICATION_DATA,
        ], $data);
    }

    public function testUpdateNotification()
    {
        $notification = new Notification([
            'id' => self::EXISTING_NOTIFICATION_ID,
            'ownerId' => 14,
            'isPending' => false,
            'type' => 'Workflow:Review',
            'created' => 1529995052,
            'data' => null,
        ]);

        $this->getGateway()->updateNotification($notification);

        $this->assertEquals([
            'id' => (string) self::EXISTING_NOTIFICATION_ID,
            'owner_id' => '14',
            'is_pending' => '0',
            'type' => 'Workflow:Review',
            'created' => '1529995052',
            'data' => null,
        ], $this->loadNotification(self::EXISTING_NOTIFICATION_ID));
    }

    public function testCountUserNotifications()
    {
        $this->assertEquals(5, $this->getGateway()->countUserNotifications(
            self::EXISTING_NOTIFICATION_DATA['owner_id']
        ));
    }

    public function testCountUserPendingNotifications()
    {
        $this->assertEquals(
            3,
            $this->getGateway()->countUserPendingNotifications(
                self::EXISTING_NOTIFICATION_DATA['owner_id']
            )
        );
    }

    public function testLoadUserNotifications()
    {
        $userId = 14;
        $offset = 1;
        $limit = 3;

        $results = $this->getGateway()->loadUserNotifications($userId, $offset, $limit);

        $this->assertEquals([
            [
                'id' => '4',
                'owner_id' => '14',
                'is_pending' => 1,
                'type' => 'Workflow:Review',
                'created' => '1530005852',
                'data' => null,
            ],
            [
                'id' => '3',
                'owner_id' => '14',
                'is_pending' => 0,
                'type' => 'Workflow:Reject',
                'created' => '1530002252',
                'data' => null,
            ],
            [
                'id' => '2',
                'owner_id' => '14',
                'is_pending' => 0,
                'type' => 'Workflow:Approve',
                'created' => '1529998652',
                'data' => null,
            ],
        ], $results);
    }

    public function testDelete()
    {
        $this->getGateway()->delete(self::EXISTING_NOTIFICATION_ID);

        $this->assertEmpty($this->loadNotification(self::EXISTING_NOTIFICATION_ID));
    }

    /**
     * Return a ready to test DoctrineStorage gateway.
     *
     * @return \Ibexa\Core\Persistence\Legacy\Notification\Gateway\DoctrineDatabase
     */
    protected function getGateway(): DoctrineDatabase
    {
        return new DoctrineDatabase(
            $this->getDatabaseConnection()
        );
    }

    private function loadNotification(int $id): array
    {
        $data = $this->connection
            ->executeQuery('SELECT * FROM eznotification WHERE id = :id', ['id' => $id])
            ->fetch(FetchMode::ASSOCIATIVE);

        return is_array($data) ? $data : [];
    }
}

class_alias(DoctrineDatabaseTest::class, 'eZ\Publish\Core\Persistence\Legacy\Tests\Notification\Gateway\DoctrineDatabaseTest');
