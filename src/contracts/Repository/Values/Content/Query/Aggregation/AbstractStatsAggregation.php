<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation;

abstract class AbstractStatsAggregation implements Aggregation
{
    /**
     * The name of the aggregation.
     *
     * @var string
     */
    protected $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }
}

class_alias(AbstractStatsAggregation::class, 'eZ\Publish\API\Repository\Values\Content\Query\Aggregation\AbstractStatsAggregation');
