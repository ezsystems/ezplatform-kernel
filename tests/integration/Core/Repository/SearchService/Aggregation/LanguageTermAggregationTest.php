<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Core\Repository\SearchService\Aggregation;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation\LanguageTermAggregation;
use Ibexa\Tests\Integration\Core\Repository\SearchService\Aggregation\DataSetBuilder\TermAggregationDataSetBuilder;

final class LanguageTermAggregationTest extends AbstractAggregationTest
{
    public function dataProviderForTestFindContentWithAggregation(): iterable
    {
        $aggregation = new LanguageTermAggregation('language');

        $builder = new TermAggregationDataSetBuilder($aggregation);
        $builder->setExpectedEntries([
            'eng-US' => 16,
            'eng-GB' => 2,
        ]);

        $builder->setEntryMapper([
            $this->getRepository()->getContentLanguageService(),
            'loadLanguage',
        ]);

        yield $builder->build();
    }
}

class_alias(LanguageTermAggregationTest::class, 'eZ\Publish\API\Repository\Tests\SearchService\Aggregation\LanguageTermAggregationTest');
