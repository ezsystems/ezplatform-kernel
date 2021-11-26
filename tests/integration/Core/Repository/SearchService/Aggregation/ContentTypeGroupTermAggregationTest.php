<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Core\Repository\SearchService\Aggregation;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation\ContentTypeGroupTermAggregation;
use Ibexa\Tests\Integration\Core\Repository\SearchService\Aggregation\DataSetBuilder\TermAggregationDataSetBuilder;

final class ContentTypeGroupTermAggregationTest extends AbstractAggregationTest
{
    public function dataProviderForTestFindContentWithAggregation(): iterable
    {
        $aggregation = new ContentTypeGroupTermAggregation('content_type_group');

        $builder = new TermAggregationDataSetBuilder($aggregation);
        $builder->setExpectedEntries([
            'Content' => 8,
            'Users' => 8,
            'Setup' => 2,
        ]);

        $builder->setEntryMapper([
            $this->getRepository()->getContentTypeService(),
            'loadContentTypeGroupByIdentifier',
        ]);

        yield $builder->build();
    }
}

class_alias(ContentTypeGroupTermAggregationTest::class, 'eZ\Publish\API\Repository\Tests\SearchService\Aggregation\ContentTypeGroupTermAggregationTest');
