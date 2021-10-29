<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation;

interface FieldAggregation
{
    public function getContentTypeIdentifier(): string;

    public function getFieldDefinitionIdentifier(): string;
}

class_alias(FieldAggregation::class, 'eZ\Publish\API\Repository\Values\Content\Query\Aggregation\FieldAggregation');
