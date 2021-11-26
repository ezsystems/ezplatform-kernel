<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Core\Persistence\Legacy\URL\Query\CriterionHandler;

use Doctrine\DBAL\Query\QueryBuilder;
use Ibexa\Contracts\Core\Repository\Values\URL\Query\Criterion;
use Ibexa\Contracts\Core\Repository\Values\URL\Query\Criterion\LogicalNot;
use Ibexa\Core\Persistence\Legacy\URL\Query\CriteriaConverter;
use Ibexa\Core\Persistence\Legacy\URL\Query\CriterionHandler\LogicalNot as LogicalNotHandler;

class LogicalNotTest extends CriterionHandlerTest
{
    /**
     * {@inheritdoc}
     */
    public function testAccept()
    {
        $handler = new LogicalNotHandler();

        $this->assertHandlerAcceptsCriterion($handler, LogicalNot::class);
        $this->assertHandlerRejectsCriterion($handler, Criterion::class);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotImplementedException
     */
    public function testHandle(): void
    {
        $foo = $this->createMock(Criterion::class);
        $fooExpr = 'FOO';
        $expected = 'NOT (FOO)';

        $queryBuilder = $this->createMock(QueryBuilder::class);

        $converter = $this->createMock(CriteriaConverter::class);
        $converter
            ->expects($this->at(0))
            ->method('convertCriteria')
            ->with($queryBuilder, $foo)
            ->willReturn($fooExpr);

        $handler = new LogicalNotHandler();
        $actual = $handler->handle(
            $converter,
            $queryBuilder,
            new LogicalNot($foo)
        );

        $this->assertEquals($expected, $actual);
    }
}

class_alias(LogicalNotTest::class, 'eZ\Publish\Core\Persistence\Legacy\Tests\URL\Query\CriterionHandler\LogicalNotTest');
