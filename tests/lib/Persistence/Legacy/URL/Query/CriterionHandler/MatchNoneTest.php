<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Core\Persistence\Legacy\URL\Query\CriterionHandler;

use Doctrine\DBAL\Query\QueryBuilder;
use Ibexa\Contracts\Core\Repository\Values\URL\Query\Criterion;
use Ibexa\Contracts\Core\Repository\Values\URL\Query\Criterion\MatchNone;
use Ibexa\Core\Persistence\Legacy\URL\Query\CriteriaConverter;
use Ibexa\Core\Persistence\Legacy\URL\Query\CriterionHandler\MatchNone as MatchNoneHandler;

class MatchNoneTest extends CriterionHandlerTest
{
    /**
     * {@inheritdoc}
     */
    public function testAccept()
    {
        $handler = new MatchNoneHandler();

        $this->assertHandlerAcceptsCriterion($handler, MatchNone::class);
        $this->assertHandlerRejectsCriterion($handler, Criterion::class);
    }

    /**
     * {@inheritdoc}
     */
    public function testHandle()
    {
        $criterion = new MatchNone();
        $expected = '1 = 0';

        $query = $this->createMock(QueryBuilder::class);
        $converter = $this->createMock(CriteriaConverter::class);

        $handler = new MatchNoneHandler();
        $actual = $handler->handle($converter, $query, $criterion);

        $this->assertEquals($expected, $actual);
    }
}

class_alias(MatchNoneTest::class, 'eZ\Publish\Core\Persistence\Legacy\Tests\URL\Query\CriterionHandler\MatchNoneTest');
