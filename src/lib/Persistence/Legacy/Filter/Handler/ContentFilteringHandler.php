<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\Persistence\Legacy\Filter\Handler;

use Ibexa\Contracts\Core\Persistence\Content\ContentItem;
use Ibexa\Contracts\Core\Persistence\Filter\Content\Handler;
use Ibexa\Contracts\Core\Persistence\Filter\Content\LazyContentItemListIterator;
use Ibexa\Contracts\Core\Repository\Values\Filter\Filter;
use Ibexa\Core\Persistence\Legacy\Content\FieldHandler;
use Ibexa\Core\Persistence\Legacy\Filter\Gateway\Content\GatewayDataMapper;
use Ibexa\Core\Persistence\Legacy\Filter\Gateway\Gateway as FilteringGateway;

/**
 * @internal for internal use by Repository Storage abstraction
 */
final class ContentFilteringHandler implements Handler
{
    /** @var \Ibexa\Core\Persistence\Legacy\Filter\Gateway\Gateway */
    private $gateway;

    /** @var \Ibexa\Core\Persistence\Legacy\Filter\Gateway\Content\GatewayDataMapper */
    private $mapper;

    /** @var \Ibexa\Core\Persistence\Legacy\Content\FieldHandler */
    private $fieldHandler;

    public function __construct(
        FilteringGateway $gateway,
        GatewayDataMapper $mapper,
        FieldHandler $fieldHandler
    ) {
        $this->gateway = $gateway;
        $this->mapper = $mapper;
        $this->fieldHandler = $fieldHandler;
    }

    /**
     * @return \Ibexa\Contracts\Core\Persistence\Filter\Content\LazyContentItemListIterator
     */
    public function find(Filter $filter): iterable
    {
        $count = $this->gateway->count($filter->getCriterion());

        // wrapped list before creating the actual API ContentList to pass totalCount
        // for paginated result a total count is not going to be a number of items in a current page
        $list = new LazyContentItemListIterator($count);
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
            function (array $row): ContentItem {
                $contentItem = $this->mapper->mapRawDataToPersistenceContentItem($row);
                $this->fieldHandler->loadExternalFieldData($contentItem->getContent());

                return $contentItem;
            }
        );

        return $list;
    }
}

class_alias(ContentFilteringHandler::class, 'eZ\Publish\Core\Persistence\Legacy\Filter\Handler\ContentFilteringHandler');
