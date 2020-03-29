<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace eZ\Publish\Core\Persistence\Legacy\Tests\URL\Query\CriterionHandler;

use Doctrine\DBAL\Query\Expression\ExpressionBuilder;
use Doctrine\DBAL\Query\QueryBuilder;
use eZ\Publish\API\Repository\Values\URL\Query\Criterion;
use eZ\Publish\API\Repository\Values\URL\Query\Criterion\MatchNone;
use eZ\Publish\Core\Persistence\Legacy\URL\Query\CriteriaConverter;
use eZ\Publish\Core\Persistence\Legacy\URL\Query\CriterionHandler\MatchNone as MatchNoneHandler;

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

        $expressionBuilder = $this->createMock(ExpressionBuilder::class);
        $expressionBuilder
            ->expects($this->once())
            ->method('eq')
            ->with(1, 0)
            ->willReturn($expected);

        $query = $this->createMock(QueryBuilder::class);
        $query
            ->expects($this->once())
            ->method('expr')
            ->willReturn($expressionBuilder);

        $converter = $this->createMock(CriteriaConverter::class);

        $handler = new MatchNoneHandler();
        $actual = $handler->handle($converter, $query, $criterion);

        $this->assertEquals($expected, $actual);
    }
}
