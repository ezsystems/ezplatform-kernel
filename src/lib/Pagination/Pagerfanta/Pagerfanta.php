<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\Pagination\Pagerfanta;

use Ibexa\Contracts\Core\Repository\Values\Content\Search\AggregationResultCollection;
use Pagerfanta\Pagerfanta as BasePagerfanta;

final class Pagerfanta extends BasePagerfanta
{
    public function __construct(SearchResultAdapter $adapter)
    {
        parent::__construct($adapter);
    }

    public function getAggregations(): AggregationResultCollection
    {
        return $this->getAdapter()->getAggregations();
    }

    public function getTime(): ?float
    {
        return $this->getAdapter()->getTime();
    }

    public function getTimedOut(): ?bool
    {
        return $this->getAdapter()->getTimedOut();
    }

    public function getMaxScore(): ?float
    {
        return $this->getAdapter()->getMaxScore();
    }
}

class_alias(Pagerfanta::class, 'eZ\Publish\Core\Pagination\Pagerfanta\Pagerfanta');
