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
    private $gateway;

    /** @var array[] */
    private $fixtureData;

    protected function setUp(): void
    {
        parent::setUp();

        $fixtureLocation = __DIR__ . '/_fixtures/urls.php';
        $this->fixtureData = (require $fixtureLocation)['ezurl'];
        $this->insertDatabaseFixture($fixtureLocation);
        $this->initGateway();
    }

    /**
     * @covers \eZ\Publish\Core\Persistence\Legacy\URL\Gateway\DoctrineDatabase::loadUrlData
     */
    public function testLoadUrlData(): void
    {
        $row = $this->gateway->loadUrlData(23);

        self::assertEquals(
            $this->fixtureData[0],
            $row[0]
        );
    }

    /**
     * @covers \eZ\Publish\Core\Persistence\Legacy\URL\Gateway\DoctrineDatabase::loadUrlDataByUrl
     */
    public function testLoadUrlDataByUrl(): void
    {
        $rows = $this->gateway->loadUrlDataByUrl('https://doc.ez.no/display/USER/');

        self::assertEquals(
            $this->fixtureData[0],
            $rows[0]
        );
    }

    /**
     * @covers \eZ\Publish\Core\Persistence\Legacy\URL\Gateway\DoctrineDatabase::find
     */
    public function testFind(): void
    {
        $criterion = new \eZ\Publish\API\Repository\Values\URL\Query\Criterion\MatchAll();
        $results = $this->gateway->find($criterion, 0, 10);

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
    public function testFindWithDisabledCounting(): void
    {
        $criterion = new \eZ\Publish\API\Repository\Values\URL\Query\Criterion\MatchAll();
        $results = $this->gateway->find($criterion, 0, 10, [], false);

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
    protected function initGateway(): DoctrineDatabase
    {
        if (!isset($this->gateway)) {
            $criteriaConverter = new CriteriaConverter([new MatchAll()]);
            $this->gateway = new DoctrineDatabase($this->getDatabaseConnection(), $criteriaConverter);
        }

        return $this->gateway;
    }
}
