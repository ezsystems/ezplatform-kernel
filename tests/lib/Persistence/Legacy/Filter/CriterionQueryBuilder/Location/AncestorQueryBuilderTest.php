<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Core\Persistence\Legacy\Filter\CriterionQueryBuilder\Location;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\Ancestor;
use Ibexa\Core\Persistence\Legacy\Filter\CriterionQueryBuilder\Location\AncestorQueryBuilder;
use Ibexa\Tests\Core\Persistence\Legacy\Filter\BaseCriterionVisitorQueryBuilderTestCase;

class AncestorQueryBuilderTest extends BaseCriterionVisitorQueryBuilderTestCase
{
    protected function getCriterionQueryBuilders(): iterable
    {
        return [new AncestorQueryBuilder()];
    }

    public function getFilteringCriteriaQueryData(): iterable
    {
        yield 'Ancestor=/1/2/' => [
            new Ancestor('/1/2/'),
            'location.node_id IN (:dcValue1)',
            ['dcValue1' => [1, 2]],
        ];

        yield 'Ancestor IN (/1/2/, /1/4/10/' => [
            new Ancestor(['/1/2/', '/1/4/10/']),
            'location.node_id IN (:dcValue1)',
            ['dcValue1' => [1, 2, 4, 10]],
        ];
    }
}

class_alias(AncestorQueryBuilderTest::class, 'eZ\Publish\Core\Persistence\Legacy\Tests\Filter\CriterionQueryBuilder\Location\AncestorQueryBuilderTest');
