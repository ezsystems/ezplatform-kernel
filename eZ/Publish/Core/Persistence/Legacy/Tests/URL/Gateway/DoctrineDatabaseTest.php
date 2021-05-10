<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace eZ\Publish\Core\Persistence\Legacy\Tests\URL\Gateway;

use eZ\Publish\Core\Persistence\Legacy\Tests\TestCase;
use eZ\Publish\Core\Persistence\Legacy\URL\Gateway\DoctrineDatabase;
use eZ\Publish\Core\Persistence\Legacy\URL\Query\CriteriaConverter;
use eZ\Publish\Core\Persistence\Legacy\URL\Query\CriterionHandler\MatchAll;

/**
 * @covers \eZ\Publish\Core\Persistence\Legacy\URL\Gateway\DoctrineDatabase
 */
class DoctrineDatabaseTest extends TestCase
{
    /**
     * Database gateway to test.
     *
     * @var \eZ\Publish\Core\Persistence\Legacy\URL\Gateway\DoctrineDatabase
     */
    protected $gateway;

    protected $fixtureData = [
        [
            'id' => 23,
            'created' => 1448832197,
            'is_valid' => 1,
            'last_checked' => 0,
            'modified' => 1448832197,
            'original_url_md5' => 'f76e41d421b2a72232264943026a6ee5',
            'url' => 'https://doc.ez.no/display/USER/',
        ],
        [
            'id' => 24,
            'created' => 1448832277,
            'is_valid' => 1,
            'last_checked' => 0,
            'modified' => 1505717756,
            'original_url_md5' => 'a00ab36edb35bb641cc027eb27410934',
            'url' => 'https://doc.ezplatform.com/en/latest/',
        ],
        [
            'id' => 25,
            'created' => 1448832412,
            'is_valid' => 1,
            'last_checked' => 0,
            'modified' => 1505717756,
            'original_url_md5' => '03c4188f5fdcb679192e25a7dad09c2d',
            'url' => 'https://doc.ezplatform.com/en/latest/tutorials/platform_beginner/building_a_bicycle_route_tracker_in_ez_platform/',
        ],
    ];

    /**
     * @covers \eZ\Publish\Core\Persistence\Legacy\URL\Gateway\DoctrineDatabase::loadUrlData
     */
    public function testLoadUrlData()
    {
        $this->insertDatabaseFixture(__DIR__ . '/_fixtures/urls.php');
        $gateway = $this->getGateway();

        $row = $gateway->loadUrlData(23);

        self::assertEquals(
            $this->fixtureData[0],
            $row[0]
        );
    }

    /**
     * @covers \eZ\Publish\Core\Persistence\Legacy\URL\Gateway\DoctrineDatabase::loadUrlDataByUrl
     */
    public function testLoadUrlDataByUrl()
    {
        $this->insertDatabaseFixture(__DIR__ . '/_fixtures/urls.php');
        $gateway = $this->getGateway();

        $rows = $gateway->loadUrlDataByUrl('https://doc.ez.no/display/USER/');

        self::assertEquals(
            $this->fixtureData[0],
            $rows[0]
        );
    }

    /**
     * @covers \eZ\Publish\Core\Persistence\Legacy\URL\Gateway\DoctrineDatabase::find
     */
    public function testFind()
    {
        $this->insertDatabaseFixture(__DIR__ . '/_fixtures/urls.php');
        $gateway = $this->getGateway();

        $criterion = new \eZ\Publish\API\Repository\Values\URL\Query\Criterion\MatchAll();
        $results = $gateway->find($criterion, 0, 10);

        self::assertEquals(
            [
                'count' => count($this->fixtureData),
                'rows' => $this->fixtureData,
            ],
            $results
        );
    }

    /**
     * @covers \eZ\Publish\Core\Persistence\Legacy\URL\Gateway\DoctrineDatabase::find
     */
    public function testFindWithDisabledCounting()
    {
        $this->insertDatabaseFixture(__DIR__ . '/_fixtures/urls.php');
        $gateway = $this->getGateway();

        $criterion = new \eZ\Publish\API\Repository\Values\URL\Query\Criterion\MatchAll();
        $results = $gateway->find($criterion, 0, 10, [], false);

        self::assertEquals(
            [
                'count' => null,
                'rows' => $this->fixtureData,
            ],
            $results
        );
    }

    /**
     * Return the DoctrineDatabase gateway to test.
     */
    protected function getGateway(): DoctrineDatabase
    {
        if (!isset($this->gateway)) {
            $criteriaConverter = new CriteriaConverter([new MatchAll()]);
            $this->gateway = new DoctrineDatabase($this->getDatabaseConnection(), $criteriaConverter);
        }

        return $this->gateway;
    }
}
