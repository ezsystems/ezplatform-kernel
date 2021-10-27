<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Core\Repository\SearchService\Aggregation;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation\RawTermAggregation;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\AggregationResult\TermAggregationResult;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\AggregationResult\TermAggregationResultEntry;

final class RawTermAggregationTest extends AbstractAggregationTest
{
    public function dataProviderForTestFindContentWithAggregation(): iterable
    {
        yield [
            new RawTermAggregation(
                'raw_term',
                'content_section_identifier_id'
            ),
            new TermAggregationResult('raw_term', [
                new TermAggregationResultEntry('users', 8),
                new TermAggregationResultEntry('media', 4),
                new TermAggregationResultEntry('design', 2),
                new TermAggregationResultEntry('setup', 2),
                new TermAggregationResultEntry('standard', 2),
            ]),
        ];
    }
}

class_alias(RawTermAggregationTest::class, 'eZ\Publish\API\Repository\Tests\SearchService\Aggregation\RawTermAggregationTest');
