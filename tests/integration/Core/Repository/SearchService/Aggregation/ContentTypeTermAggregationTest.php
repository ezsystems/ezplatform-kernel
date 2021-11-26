<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Core\Repository\SearchService\Aggregation;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation\ContentTypeTermAggregation;
use Ibexa\Tests\Integration\Core\Repository\SearchService\Aggregation\DataSetBuilder\TermAggregationDataSetBuilder;

final class ContentTypeTermAggregationTest extends AbstractAggregationTest
{
    public function dataProviderForTestFindContentWithAggregation(): iterable
    {
        $aggregation = new ContentTypeTermAggregation('content_type');

        $builder = new TermAggregationDataSetBuilder($aggregation);
        $builder->setExpectedEntries([
            'folder' => 6,
            'user_group' => 6,
            'user' => 2,
            'common_ini_settings' => 1,
            'template_look' => 1,
            'feedback_form' => 1,
            'landing_page' => 1,
        ]);

        $builder->setEntryMapper([
            $this->getRepository()->getContentTypeService(),
            'loadContentTypeByIdentifier',
        ]);

        yield $builder->build();
    }
}

class_alias(ContentTypeTermAggregationTest::class, 'eZ\Publish\API\Repository\Tests\SearchService\Aggregation\ContentTypeTermAggregationTest');
