<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Core\Persistence\Legacy\User\Role\Gateway;

use Doctrine\DBAL\ParameterType;
use Ibexa\Contracts\Core\Persistence\User\Role;
use Ibexa\Core\Persistence\Legacy\User\Role\Gateway\DoctrineDatabase;
use Ibexa\Tests\Core\Persistence\Legacy\TestCase;

/**
 * @covers \Ibexa\Core\Persistence\Legacy\User\Role\Gateway\DoctrineDatabase
 */
class DoctrineDatabaseTest extends TestCase
{
    /**
     * Database gateway to test.
     *
     * @var \Ibexa\Core\Persistence\Legacy\User\Role\Gateway\DoctrineDatabase
     */
    protected $databaseGateway;

    /**
     * Inserts DB fixture.
     *
     * @throws \Exception
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->insertDatabaseFixture(
            __DIR__ . '/../../_fixtures/roles.php'
        );
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    public function testCreateRole(): void
    {
        $gateway = $this->getDatabaseGateway();

        $spiRole = new Role([
            'identifier' => 'new_role',
            'status' => Role::STATUS_DRAFT,
        ]);
        $gateway->createRole($spiRole);
        $query = $this->getDatabaseConnection()->createQueryBuilder();

        $this->assertQueryResult(
            [
                [
                    'id' => '6',
                    'name' => 'new_role',
                    'version' => -1,
                ],
            ],
            $query
                ->select('id', 'name', 'version')
                ->from('ezrole')
                ->where(
                    $query->expr()->eq(
                        'name',
                        $query->createPositionalParameter('new_role', ParameterType::STRING)
                    )
                )
        );
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    public function testLoadRoleAssignment(): void
    {
        $gateway = $this->getDatabaseGateway();

        $this->assertEquals(
            [
                [
                    'contentobject_id' => '12',
                    'id' => '25',
                    'limit_identifier' => '',
                    'limit_value' => '',
                    'role_id' => '2',
                ],
            ],
            $gateway->loadRoleAssignment(25)
        );
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    public function testLoadRoleAssignmentsByGroupId(): void
    {
        $gateway = $this->getDatabaseGateway();

        $this->assertEquals(
            [
                [
                    'contentobject_id' => '11',
                    'id' => '28',
                    'limit_identifier' => '',
                    'limit_value' => '',
                    'role_id' => '1',
                ],
                [
                    'contentobject_id' => '11',
                    'id' => '34',
                    'limit_identifier' => '',
                    'limit_value' => '',
                    'role_id' => '5',
                ],
                [
                    'contentobject_id' => '11',
                    'id' => '40',
                    'limit_identifier' => 'Section',
                    'limit_value' => '3',
                    'role_id' => '4',
                ],
            ],
            $gateway->loadRoleAssignmentsByGroupId(11)
        );
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    public function testLoadRoleAssignmentsByRoleId(): void
    {
        $gateway = $this->getDatabaseGateway();

        $this->assertEquals(
            [
                [
                    'contentobject_id' => '11',
                    'id' => '28',
                    'limit_identifier' => '',
                    'limit_value' => '',
                    'role_id' => '1',
                ],
                [
                    'contentobject_id' => '42',
                    'id' => '31',
                    'limit_identifier' => '',
                    'limit_value' => '',
                    'role_id' => '1',
                ],
                [
                    'contentobject_id' => '59',
                    'id' => '37',
                    'limit_identifier' => '',
                    'limit_value' => '',
                    'role_id' => '1',
                ],
            ],
            $gateway->loadRoleAssignmentsByRoleId(1)
        );
    }

    /**
     * Returns a ready to test DoctrineDatabase gateway.
     *
     * @return \Ibexa\Core\Persistence\Legacy\User\Role\Gateway\DoctrineDatabase
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    protected function getDatabaseGateway(): DoctrineDatabase
    {
        if (!isset($this->databaseGateway)) {
            $this->databaseGateway = new DoctrineDatabase(
                $this->getDatabaseConnection()
            );
        }

        return $this->databaseGateway;
    }
}

class_alias(DoctrineDatabaseTest::class, 'eZ\Publish\Core\Persistence\Legacy\Tests\User\Role\Gateway\DoctrineDatabaseTest');
