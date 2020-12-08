<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace eZ\Publish\Core\Persistence\Legacy\Tests\Setting\Gateway;

use Doctrine\DBAL\ParameterType;
use eZ\Publish\Core\Persistence\Legacy\Setting\Gateway;
use eZ\Publish\Core\Persistence\Legacy\Tests\TestCase;
use eZ\Publish\Core\Persistence\Legacy\Setting\Gateway\DoctrineDatabase;

class DoctrineDatabaseTest extends TestCase
{
    /** @var \eZ\Publish\Core\Persistence\Legacy\Setting\Gateway\DoctrineDatabase */
    protected $databaseGateway;

    protected function setUp(): void
    {
        parent::setUp();

        $this->insertDatabaseFixture(
            __DIR__ . '/../../_fixtures/settings.php'
        );
    }

    /**
     * @covers \eZ\Publish\Core\Persistence\Legacy\Setting\Gateway\DoctrineDatabase::insertSetting
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function testInsertSetting()
    {
        $gateway = $this->getDatabaseGateway();

        $value = json_encode(['new_value' => 1234]);
        $gateway->insertSetting(
            'new_group',
            'new_identifier',
            $value
        );
        $query = $this->getDatabaseConnection()->createQueryBuilder();

        $this->assertQueryResult(
            [
                [
                    'group' => 'new_group',
                    'identifier' => 'new_identifier',
                    'value' => $value,
                ],
            ],
            $query
                ->select('group', 'identifier', 'value')
                ->from('ibexa_setting')
                ->where(
                    $query->expr()->eq(
                        'group',
                        $query->createPositionalParameter('new_group', ParameterType::STRING)
                    ),
                    $query->expr()->eq(
                        'identifier',
                        $query->createPositionalParameter('new_identifier', ParameterType::STRING)
                    )
                )
        );
    }

    /**
     * @covers \eZ\Publish\Core\Persistence\Legacy\Setting\Gateway\DoctrineDatabase::updateSetting
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function testUpdateSetting()
    {
        $gateway = $this->getDatabaseGateway();

        $value = json_encode(['no_longer' => 'string_value']);
        $gateway->updateSetting(
            'test_group',
            'another_identifier',
            $value
        );
        $query = $this->getDatabaseConnection()->createQueryBuilder();

        $this->assertQueryResult(
            [
                [
                    'group' => 'test_group',
                    'identifier' => 'another_identifier',
                    'value' => $value,
                ],
            ],
            $query
                ->select('group', 'identifier', 'value')
                ->from('ibexa_setting')
                ->where(
                    $query->expr()->eq(
                        'group',
                        $query->createPositionalParameter('test_group', ParameterType::STRING)
                    ),
                    $query->expr()->eq(
                        'identifier',
                        $query->createPositionalParameter('another_identifier', ParameterType::STRING)
                    )
                )
        );
    }

    /**
     * @covers \eZ\Publish\Core\Persistence\Legacy\Setting\Gateway\DoctrineDatabase::loadSettingById
     *
     * @throws \Doctrine\DBAL\Exception
     * @throws \Doctrine\DBAL\DBALException
     */
    public function testLoadSettingById()
    {
        $gateway = $this->getDatabaseGateway();

        $result = $gateway->loadSettingById(4);

        $this->assertEquals(
            [
                [
                    'group' => 'another_group',
                    'identifier' => 'some_identifier',
                    'value' => json_encode(true),
                ],
            ],
            $result
        );
    }

    /**
     * @covers \eZ\Publish\Core\Persistence\Legacy\Setting\Gateway\DoctrineDatabase::deleteSetting
     *
     * @throws \Doctrine\DBAL\Exception
     * @throws \Doctrine\DBAL\DBALException
     */
    public function testDeleteSetting()
    {
        $gateway = $this->getDatabaseGateway();

        $query = $this->getDatabaseConnection()->createQueryBuilder();
        $this->assertQueryResult(
            [
                [
                    'count' => 5,
                ],
            ],
            $query
                ->select('COUNT(*) AS count')
                ->from('ibexa_setting')
        );

        $gateway->deleteSetting(
            'another_group',
            'other_identifier'
        );

        $query = $this->getDatabaseConnection()->createQueryBuilder();
        $this->assertQueryResult(
            [
                [
                    'count' => 4,
                ],
            ],
            $query
                ->select('COUNT(*) AS count')
                ->from('ibexa_setting')
        );

        $query = $this->getDatabaseConnection()->createQueryBuilder();
        $this->assertQueryResult(
            [
                [
                    'count' => 0,
                ],
            ],
            $query
                ->select('COUNT(*) AS count')
                ->from('ibexa_setting')
                ->where(
                    $query->expr()->eq(
                        $query->expr()->eq(
                            'group',
                            $query->createPositionalParameter('another_group', ParameterType::STRING)
                        ),
                        $query->expr()->eq(
                            'identifier',
                            $query->createPositionalParameter('other_identifier', ParameterType::STRING)
                        )
                    )
                )
        );
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    protected function getDatabaseGateway(): Gateway
    {
        if (!isset($this->databaseGateway)) {
            $this->databaseGateway = new DoctrineDatabase($this->getDatabaseConnection());
        }

        return $this->databaseGateway;
    }
}
