<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Core\Repository\SearchService\Aggregation\Field;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation\Field\AuthorTermAggregation;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\AggregationResult\TermAggregationResult;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\AggregationResult\TermAggregationResultEntry;
use Ibexa\Core\FieldType\Author\Author;
use Ibexa\Core\FieldType\Author\Value as AuthorValue;
use Ibexa\Tests\Integration\Core\Repository\SearchService\Aggregation\AbstractAggregationTest;
use Ibexa\Tests\Integration\Core\Repository\SearchService\Aggregation\FixtureGenerator\FieldAggregationFixtureGenerator;

final class AuthorTermAggregationTest extends AbstractAggregationTest
{
    public function dataProviderForTestFindContentWithAggregation(): iterable
    {
        yield [
            new AuthorTermAggregation('author_term', 'content_type', 'author'),
            new TermAggregationResult(
                'author_term',
                [
                    new TermAggregationResultEntry(
                        new Author([
                            'name' => 'Boba Fett',
                            'email' => 'boba.fett@example.com',
                        ]),
                        2
                    ),
                    new TermAggregationResultEntry(
                        new Author([
                            'name' => 'Leia Organa',
                            'email' => 'leia.organa@example.com',
                        ]),
                        2
                    ),
                    new TermAggregationResultEntry(
                        new Author([
                            'name' => 'Luke Skywalker',
                            'email' => 'luke.skywalker@example.com',
                        ]),
                        2
                    ),
                    new TermAggregationResultEntry(
                        new Author([
                            'name' => 'Anakin Skywalker',
                            'email' => 'anakin.skywalker@example.com',
                        ]),
                        1
                    ),
                ]
            ),
        ];
    }

    protected function createFixturesForAggregation(Aggregation $aggregation): void
    {
        $generator = new FieldAggregationFixtureGenerator($this->getRepository());
        $generator->setContentTypeIdentifier('content_type');
        $generator->setFieldDefinitionIdentifier('author');
        $generator->setFieldTypeIdentifier('ezauthor');
        $generator->setValues([
            new AuthorValue([
                new Author([
                    'name' => 'Boba Fett',
                    'email' => 'boba.fett@example.com',
                ]),
                new Author([
                    'name' => 'Luke Skywalker',
                    'email' => 'luke.skywalker@example.com',
                ]),
            ]),
            new AuthorValue([
                new Author([
                    'name' => 'Anakin Skywalker',
                    'email' => 'anakin.skywalker@example.com',
                ]),
            ]),
            new AuthorValue([
                new Author([
                    'name' => 'Boba Fett',
                    'email' => 'boba.fett@example.com',
                ]),
            ]),
            new AuthorValue([
                new Author([
                    'name' => 'Luke Skywalker',
                    'email' => 'luke.skywalker@example.com',
                ]),
                new Author([
                    'name' => 'Leia Organa',
                    'email' => 'leia.organa@example.com',
                ]),
            ]),
            new AuthorValue([
                new Author([
                    'name' => 'Leia Organa',
                    'email' => 'leia.organa@example.com',
                ]),
            ]),
        ]);

        $generator->execute();

        $this->refreshSearch($this->getRepository());
    }
}

class_alias(AuthorTermAggregationTest::class, 'eZ\Publish\API\Repository\Tests\SearchService\Aggregation\Field\AuthorTermAggregationTest');
