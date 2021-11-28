<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\Persistence\Legacy\Filter\Handler;

use Ibexa\Contracts\Core\Persistence\Content\LocationWithContentInfo;
use Ibexa\Contracts\Core\Persistence\Filter\Location\Handler;
use Ibexa\Contracts\Core\Persistence\Filter\Location\LazyLocationListIterator;
use Ibexa\Contracts\Core\Repository\Values\Filter\Filter;
use Ibexa\Core\Persistence\Legacy\Content\Location\Mapper as LocationLegacyMapper;
use Ibexa\Core\Persistence\Legacy\Filter\Gateway\Content\GatewayDataMapper as ContentGatewayDataMapper;
use Ibexa\Core\Persistence\Legacy\Filter\Gateway\Gateway;

class LocationFilteringHandler implements Handler
{
    /** @var \Ibexa\Core\Persistence\Legacy\Filter\Gateway\Gateway */
    private $gateway;

    /** @var \Ibexa\Core\Persistence\Legacy\Content\Location\Mapper */
    private $locationMapper;

    /** @var \Ibexa\Core\Persistence\Legacy\Filter\Gateway\Content\GatewayDataMapper */
    private $contentGatewayDataMapper;

    public function __construct(
        Gateway $gateway,
        LocationLegacyMapper $locationMapper,
        ContentGatewayDataMapper $contentGatewayDataMapper
    ) {
        $this->gateway = $gateway;
        $this->locationMapper = $locationMapper;
        $this->contentGatewayDataMapper = $contentGatewayDataMapper;
    }

    public function find(Filter $filter): iterable
    {
        $count = $this->gateway->count($filter->getCriterion());

        // wrapped list before creating the actual API LocationList to pass totalCount
        // for paginated result a total count is not going to be a number of items in a current page
        $list = new LazyLocationListIterator($count);
        if ($count === 0) {
            return $list;
        }

        $list->prepareIterator(
            $this->gateway->find(
                $filter->getCriterion(),
                $filter->getSortClauses(),
                $filter->getLimit(),
                $filter->getOffset()
            ),
            // called on each iteration of the  iterator returned by find
            function (array $row): LocationWithContentInfo {
                return new LocationWithContentInfo(
                    $this->locationMapper->createLocationFromRow($row, 'location_'),
                    $this->contentGatewayDataMapper->mapContentMetadataToPersistenceContentInfo(
                        $row
                    )
                );
            }
        );

        return $list;
    }
}

class_alias(LocationFilteringHandler::class, 'eZ\Publish\Core\Persistence\Legacy\Filter\Handler\LocationFilteringHandler');
