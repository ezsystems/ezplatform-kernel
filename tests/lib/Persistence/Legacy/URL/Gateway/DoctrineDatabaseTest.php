<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Core\Persistence\Legacy\URL\Gateway;

use Ibexa\Contracts\Core\Repository\Values\URL\Query\Criterion\MatchAll as MatchAllCriterion;
use Ibexa\Core\Persistence\Legacy\URL\Gateway\DoctrineDatabase;
use Ibexa\Core\Persistence\Legacy\URL\Query\CriteriaConverter;
use Ibexa\Core\Persistence\Legacy\URL\Query\CriterionHandler\MatchAll;
use Ibexa\Tests\Core\Persistence\Legacy\TestCase;

/**
 * @covers \Ibexa\Core\Persistence\Legacy\URL\Gateway\DoctrineDatabase
 */
class DoctrineDatabaseTest extends TestCase
{
    /**
     * Database gateway to test.
     *
     * @var \Ibexa\Core\Persistence\Legacy\URL\Gateway\DoctrineDatabase
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

    public function testLoadUrlData(): void
    {
        $row = $this->gateway->loadUrlData(23);

        self::assertEquals(
            $this->fixtureData[0],
            $row[0]
        );
    }

    public function testLoadUrlDataByUrl(): void
    {
        $rows = $this->gateway->loadUrlDataByUrl('https://doc.ez.no/display/USER/');

        self::assertEquals(
            $this->fixtureData[0],
            $rows[0]
        );
    }

    public function testFind(): void
    {
        $criterion = new MatchAllCriterion();
        $results = $this->gateway->find($criterion, 0, 10);

        self::assertEquals(
            [
                'count' => count($this->fixtureData),
                'rows' => $this->fixtureData,
            ],
            $results
        );
    }

    public function testFindWithDisabledCounting(): void
    {
        $criterion = new MatchAllCriterion();
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

class_alias(DoctrineDatabaseTest::class, 'eZ\Publish\Core\Persistence\Legacy\Tests\URL\Gateway\DoctrineDatabaseTest');
