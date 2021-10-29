<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation;

final class ObjectStateTermAggregation extends AbstractTermAggregation
{
    /** @var string */
    private $objectStateGroupIdentifier;

    public function __construct(
        string $name,
        string $objectStateGroupIdentifier
    ) {
        parent::__construct($name);

        $this->objectStateGroupIdentifier = $objectStateGroupIdentifier;
    }

    public function getObjectStateGroupIdentifier(): string
    {
        return $this->objectStateGroupIdentifier;
    }
}

class_alias(ObjectStateTermAggregation::class, 'eZ\Publish\API\Repository\Values\Content\Query\Aggregation\ObjectStateTermAggregation');
