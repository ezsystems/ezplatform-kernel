<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\Operator\Specifications;

/**
 * A criterion that matches content based on if Field is empty.
 */
class IsFieldEmpty extends Criterion
{
    /**
     * @param string $fieldDefinitionIdentifier
     * @param bool $value
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function __construct(string $fieldDefinitionIdentifier, bool $value = true)
    {
        parent::__construct($fieldDefinitionIdentifier, null, $value);
    }

    public function getSpecifications(): array
    {
        return [
            new Specifications(Operator::EQ, Specifications::FORMAT_SINGLE, Specifications::TYPE_BOOLEAN),
        ];
    }
}

class_alias(IsFieldEmpty::class, 'eZ\Publish\API\Repository\Values\Content\Query\Criterion\IsFieldEmpty');
