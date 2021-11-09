<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation;

final class RawStatsAggregation extends AbstractStatsAggregation implements RawAggregation
{
    /** @var string */
    private $fieldName;

    public function __construct(string $name, string $fieldName)
    {
        parent::__construct($name);

        $this->fieldName = $fieldName;
    }

    public function getFieldName(): string
    {
        return $this->fieldName;
    }
}

class_alias(RawStatsAggregation::class, 'eZ\Publish\API\Repository\Values\Content\Query\Aggregation\RawStatsAggregation');
