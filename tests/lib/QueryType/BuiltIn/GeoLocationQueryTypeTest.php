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
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\LogicalAnd;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\MapLocationDistance;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\Operator;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\Subtree;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\Visibility;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause\ContentName;
use Ibexa\Core\MVC\ConfigResolverInterface;
use Ibexa\Core\QueryType\BuiltIn\GeoLocationQueryType;
use Ibexa\Core\QueryType\BuiltIn\SortClausesFactoryInterface;
use Ibexa\Core\QueryType\QueryType;

final class GeoLocationQueryTypeTest extends AbstractQueryTypeTest
{
    private const EXAMPLE_FIELD = 'location';
    private const EXAMPLE_DISTANCE = 40.0;
    private const EXAMPLE_LATITUDE = 50.06314;
    private const EXAMPLE_LONGITUDE = 19.929843;

    public function dataProviderForGetQuery(): iterable
    {
        $parameters = [
            'field' => self::EXAMPLE_FIELD,
            'distance' => self::EXAMPLE_DISTANCE,
            'latitude' => self::EXAMPLE_LATITUDE,
            'longitude' => self::EXAMPLE_LONGITUDE,
        ];

        $criterion = new MapLocationDistance(
            self::EXAMPLE_FIELD,
            Operator::LTE,
            self::EXAMPLE_DISTANCE,
            self::EXAMPLE_LATITUDE,
            self::EXAMPLE_LONGITUDE
        );

        yield 'basic' => [
            $parameters,
            new Query([
                'filter' => new LogicalAnd([
                    $criterion,
                    new Visibility(Visibility::VISIBLE),
                    new Subtree(self::ROOT_LOCATION_PATH_STRING),
                ]),
            ]),
        ];

        yield 'filter by visibility' => [
            $parameters + [
                'filter' => [
                    'visible_only' => false,
                ],
            ],
            new Query([
                'filter' => new LogicalAnd([
                    $criterion,
                    new Subtree(self::ROOT_LOCATION_PATH_STRING),
                ]),
            ]),
        ];

        yield 'filter by content type' => [
            $parameters + [
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
                    $criterion,
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
            $parameters + [
                'filter' => [
                    'siteaccess_aware' => false,
                ],
            ],
            new Query([
                'filter' => new LogicalAnd([
                    $criterion,
                    new Visibility(Visibility::VISIBLE),
                ]),
            ]),
        ];

        yield 'limit and offset' => [
            $parameters + [
                'limit' => 10,
                'offset' => 100,
            ],
            new Query([
                'filter' => new LogicalAnd([
                    $criterion,
                    new Visibility(Visibility::VISIBLE),
                    new Subtree(self::ROOT_LOCATION_PATH_STRING),
                ]),
                'limit' => 10,
                'offset' => 100,
            ]),
        ];

        yield 'sort' => [
            $parameters + [
                'sort' => new ContentName(Query::SORT_ASC),
            ],
            new Query([
                'filter' => new LogicalAnd([
                    $criterion,
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
        return new GeoLocationQueryType($repository, $configResolver, $sortClausesFactory);
    }

    protected function getExpectedName(): string
    {
        return 'GeoLocation';
    }

    protected function getExpectedSupportedParameters(): array
    {
        return ['filter', 'offset', 'limit', 'sort', 'field', 'distance', 'latitude', 'longitude', 'operator'];
    }
}

class_alias(GeoLocationQueryTypeTest::class, 'eZ\Publish\Core\QueryType\BuiltIn\Tests\GeoLocationQueryTypeTest');
