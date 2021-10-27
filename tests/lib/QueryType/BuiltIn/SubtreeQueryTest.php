<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Core\QueryType\BuiltIn;

use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\Core\Repository\Values\Content\LocationQuery;
use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\ContentTypeIdentifier;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\Location\Depth;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\LogicalAnd;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\Operator;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\Subtree;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\Visibility;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause\Location\Priority;
use Ibexa\Core\MVC\ConfigResolverInterface;
use Ibexa\Core\QueryType\BuiltIn\SortClausesFactoryInterface;
use Ibexa\Core\QueryType\BuiltIn\SubtreeQueryType;
use Ibexa\Core\QueryType\QueryType;
use Ibexa\Core\Repository\Values\Content\Location;

final class SubtreeQueryTest extends AbstractQueryTypeTest
{
    private const EXAMPLE_LOCATION_ID = 54;
    private const EXAMPLE_LOCATION_PATH_STRING = '/1/2/54/';
    private const EXAMPLE_LOCATION_DEPTH = 3;

    public function dataProviderForGetQuery(): iterable
    {
        $location = new Location([
            'id' => self::EXAMPLE_LOCATION_ID,
            'depth' => self::EXAMPLE_LOCATION_DEPTH,
            'pathString' => self::EXAMPLE_LOCATION_PATH_STRING,
        ]);

        yield 'basic' => [
            [
                'location' => $location,
            ],
            new LocationQuery([
                'filter' => new LogicalAnd([
                    new Subtree(self::EXAMPLE_LOCATION_PATH_STRING),
                    new Visibility(Visibility::VISIBLE),
                    new Subtree(self::ROOT_LOCATION_PATH_STRING),
                ]),
            ]),
        ];

        yield 'filter by relative depth' => [
            [
                'location' => $location,
                'depth' => 2,
            ],
            new LocationQuery([
                'filter' => new LogicalAnd([
                    new LogicalAnd([
                        new Subtree(self::EXAMPLE_LOCATION_PATH_STRING),
                        new Depth(Operator::LTE, self::EXAMPLE_LOCATION_DEPTH + 2),
                    ]),
                    new Visibility(Visibility::VISIBLE),
                    new Subtree(self::ROOT_LOCATION_PATH_STRING),
                ]),
            ]),
        ];

        yield 'filter by visibility' => [
            [
                'location' => $location,
                'filter' => [
                    'visible_only' => false,
                ],
            ],
            new LocationQuery([
                'filter' => new LogicalAnd([
                    new Subtree(self::EXAMPLE_LOCATION_PATH_STRING),
                    new Subtree(self::ROOT_LOCATION_PATH_STRING),
                ]),
            ]),
        ];

        yield 'filter by content type' => [
            [
                'location' => $location,
                'filter' => [
                    'content_type' => [
                        'article',
                        'blog_post',
                        'folder',
                    ],
                ],
            ],
            new LocationQuery([
                'filter' => new LogicalAnd([
                    new Subtree(self::EXAMPLE_LOCATION_PATH_STRING),
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
                'location' => $location,
                'filter' => [
                    'siteaccess_aware' => false,
                ],
            ],
            new LocationQuery([
                'filter' => new LogicalAnd([
                    new Subtree(self::EXAMPLE_LOCATION_PATH_STRING),
                    new Visibility(Visibility::VISIBLE),
                ]),
            ]),
        ];

        yield 'limit and offset' => [
            [
                'location' => $location,
                'limit' => 10,
                'offset' => 100,
            ],
            new LocationQuery([
                'filter' => new LogicalAnd([
                    new Subtree(self::EXAMPLE_LOCATION_PATH_STRING),
                    new Visibility(Visibility::VISIBLE),
                    new Subtree(self::ROOT_LOCATION_PATH_STRING),
                ]),
                'limit' => 10,
                'offset' => 100,
            ]),
        ];

        yield 'basic sort' => [
            [
                'location' => $location,
                'sort' => new Priority(Query::SORT_ASC),
            ],
            new LocationQuery([
                'filter' => new LogicalAnd([
                    new Subtree(self::EXAMPLE_LOCATION_PATH_STRING),
                    new Visibility(Visibility::VISIBLE),
                    new Subtree(self::ROOT_LOCATION_PATH_STRING),
                ]),
                'sortClauses' => [
                    new Priority(Query::SORT_ASC),
                ],
            ]),
        ];
    }

    protected function createQueryType(
        Repository $repository,
        ConfigResolverInterface $configResolver,
        SortClausesFactoryInterface $sortClausesFactory
    ): QueryType {
        return new SubtreeQueryType($repository, $configResolver, $sortClausesFactory);
    }

    protected function getExpectedName(): string
    {
        return 'Subtree';
    }

    protected function getExpectedSupportedParameters(): array
    {
        return ['filter', 'offset', 'limit', 'sort', 'location', 'content', 'depth'];
    }
}

class_alias(SubtreeQueryTest::class, 'eZ\Publish\Core\QueryType\BuiltIn\Tests\SubtreeQueryTest');
