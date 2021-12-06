<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Core\QueryType\BuiltIn;

use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\ContentTypeIdentifier;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\FieldRelation;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\LogicalAnd;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\Operator;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\Subtree;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\Visibility;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause\ContentName;
use Ibexa\Core\MVC\ConfigResolverInterface;
use Ibexa\Core\QueryType\BuiltIn\RelatedToContentQueryType;
use Ibexa\Core\QueryType\BuiltIn\SortClausesFactoryInterface;
use Ibexa\Core\QueryType\QueryType;

final class RelatedToContentQueryTypeTest extends AbstractQueryTypeTest
{
    private const EXAMPLE_CONTENT_ID = 52;
    private const EXAMPLE_FIELD = 'related';

    public function dataProviderForGetQuery(): iterable
    {
        yield 'basic' => [
            [
                'content' => self::EXAMPLE_CONTENT_ID,
                'field' => self::EXAMPLE_FIELD,
            ],
            new Query([
                'filter' => new LogicalAnd([
                    new FieldRelation(self::EXAMPLE_FIELD, Operator::CONTAINS, self::EXAMPLE_CONTENT_ID),
                    new Visibility(Visibility::VISIBLE),
                    new Subtree(self::ROOT_LOCATION_PATH_STRING),
                ]),
            ]),
        ];

        yield 'filter by visibility' => [
            [
                'content' => self::EXAMPLE_CONTENT_ID,
                'field' => self::EXAMPLE_FIELD,
                'filter' => [
                    'visible_only' => false,
                ],
            ],
            new Query([
                'filter' => new LogicalAnd([
                    new FieldRelation(self::EXAMPLE_FIELD, Operator::CONTAINS, self::EXAMPLE_CONTENT_ID),
                    new Subtree(self::ROOT_LOCATION_PATH_STRING),
                ]),
            ]),
        ];

        yield 'filter by content type' => [
            [
                'content' => self::EXAMPLE_CONTENT_ID,
                'field' => self::EXAMPLE_FIELD,
                'filter' => [
                    'content_type' => [
                        'article',
                        'blog_post',
                        'folder',
                    ],
                ],
            ],
            new Query([
                'filter' => new LogicalAnd([
                    new FieldRelation(self::EXAMPLE_FIELD, Operator::CONTAINS, self::EXAMPLE_CONTENT_ID),
                    new Visibility(Visibility::VISIBLE),
                    new ContentTypeIdentifier([
                        'article',
                        'blog_post',
                        'folder',
                    ]),
                    new Subtree(self::ROOT_LOCATION_PATH_STRING),
                ]),
            ]),
        ];

        yield 'filter by siteaccess' => [
            [
                'content' => self::EXAMPLE_CONTENT_ID,
                'field' => self::EXAMPLE_FIELD,
                'filter' => [
                    'siteaccess_aware' => false,
                ],
            ],
            new Query([
                'filter' => new LogicalAnd([
                    new FieldRelation(self::EXAMPLE_FIELD, Operator::CONTAINS, self::EXAMPLE_CONTENT_ID),
                    new Visibility(Visibility::VISIBLE),
                ]),
            ]),
        ];

        yield 'limit and offset' => [
            [
                'content' => self::EXAMPLE_CONTENT_ID,
                'field' => self::EXAMPLE_FIELD,
                'limit' => 10,
                'offset' => 100,
            ],
            new Query([
                'filter' => new LogicalAnd([
                    new FieldRelation(self::EXAMPLE_FIELD, Operator::CONTAINS, self::EXAMPLE_CONTENT_ID),
                    new Visibility(Visibility::VISIBLE),
                    new Subtree(self::ROOT_LOCATION_PATH_STRING),
                ]),
                'limit' => 10,
                'offset' => 100,
            ]),
        ];

        yield 'basic sort' => [
            [
                'content' => self::EXAMPLE_CONTENT_ID,
                'field' => self::EXAMPLE_FIELD,
                'sort' => new ContentName(Query::SORT_ASC),
            ],
            new Query([
                'filter' => new LogicalAnd([
                    new FieldRelation(self::EXAMPLE_FIELD, Operator::CONTAINS, self::EXAMPLE_CONTENT_ID),
                    new Visibility(Visibility::VISIBLE),
                    new Subtree(self::ROOT_LOCATION_PATH_STRING),
                ]),
                'sortClauses' => [
                    new ContentName(Query::SORT_ASC),
                ],
            ]),
        ];
    }

    protected function createQueryType(
        Repository $repository,
        ConfigResolverInterface $configResolver,
        SortClausesFactoryInterface $sortClausesFactory
    ): QueryType {
        return new RelatedToContentQueryType($repository, $configResolver, $sortClausesFactory);
    }

    protected function getExpectedName(): string
    {
        return 'RelatedToContent';
    }

    protected function getExpectedSupportedParameters(): array
    {
        return ['filter', 'offset', 'limit', 'sort', 'content', 'field'];
    }
}

class_alias(RelatedToContentQueryTypeTest::class, 'eZ\Publish\Core\QueryType\BuiltIn\Tests\RelatedToContentQueryTypeTest');
