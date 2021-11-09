<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Core\Persistence\Legacy\URL\Query\CriterionHandler;

use Doctrine\DBAL\Query\QueryBuilder;
use Ibexa\Contracts\Core\Repository\Values\URL\Query\Criterion;
use Ibexa\Contracts\Core\Repository\Values\URL\Query\Criterion\MatchAll;
use Ibexa\Core\Persistence\Legacy\URL\Query\CriteriaConverter;
use Ibexa\Core\Persistence\Legacy\URL\Query\CriterionHandler\MatchAll as MatchAllHandler;

class MatchAllTest extends CriterionHandlerTest
{
    /**
     * {@inheritdoc}
     */
    public function testAccept()
    {
        $handler = new MatchAllHandler();

        $this->assertHandlerAcceptsCriterion($handler, MatchAll::class);
        $this->assertHandlerRejectsCriterion($handler, Criterion::class);
    }

    /**
     * {@inheritdoc}
     */
    public function testHandle()
    {
        $criterion = new MatchAll();
        $expected = '1 = 1';

        $queryBuilder = $this->createMock(QueryBuilder::class);
        $converter = $this->createMock(CriteriaConverter::class);

        $handler = new MatchAllHandler();
        $actual = $handler->handle($converter, $queryBuilder, $criterion);

        $this->assertEquals($expected, $actual);
    }
}

class_alias(MatchAllTest::class, 'eZ\Publish\Core\Persistence\Legacy\Tests\URL\Query\CriterionHandler\MatchAllTest');
