<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\FieldType\RelationList;

use Ibexa\Core\FieldType\Value as BaseValue;

/**
 * Value for RelationList field type.
 */
class Value extends BaseValue
{
    /**
     * Related content id's.
     *
     * @var mixed[]
     */
    public $destinationContentIds;

    /**
     * Construct a new Value object and initialize it $text.
     *
     * @param mixed[] $destinationContentIds
     */
    public function __construct(array $destinationContentIds = [])
    {
        $this->destinationContentIds = $destinationContentIds;
    }

    public function __toString()
    {
        return implode(',', $this->destinationContentIds);
    }
}

class_alias(Value::class, 'eZ\Publish\Core\FieldType\RelationList\Value');
