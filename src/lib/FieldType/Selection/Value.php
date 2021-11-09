<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\FieldType\Selection;

use Ibexa\Core\FieldType\Value as BaseValue;

/**
 * Value for Selection field type.
 */
class Value extends BaseValue
{
    /**
     * Selection content.
     *
     * @var int[]
     */
    public $selection;

    /**
     * Construct a new Value object and initialize it $selection.
     *
     * @param int[] $selection
     */
    public function __construct(array $selection = [])
    {
        $this->selection = $selection;
    }

    public function __toString()
    {
        return implode(',', $this->selection);
    }
}

class_alias(Value::class, 'eZ\Publish\Core\FieldType\Selection\Value');
