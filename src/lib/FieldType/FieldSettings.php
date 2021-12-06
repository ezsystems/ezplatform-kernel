<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\FieldType;

use ArrayObject;
use Ibexa\Contracts\Core\Repository\Exceptions\PropertyNotFoundException;
use Ibexa\Contracts\Core\Repository\Exceptions\PropertyReadOnlyException;

/**
 * Container for field type specific properties.
 *
 * @internal
 */
class FieldSettings extends ArrayObject
{
    /**
     * Only allows existing indexes to be updated.
     *
     * This is so that only settings specified by a field type can be set.
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\PropertyReadOnlyException On non existing indexes
     *
     * @param string|int $index
     * @param mixed $value
     */
    public function offsetSet($index, $value): void
    {
        if (!parent::offsetExists($index)) {
            throw new PropertyReadOnlyException($index, __CLASS__);
        }

        parent::offsetSet($index, $value);
    }

    /**
     * Returns value from internal array, identified by $index.
     *
     * @param string $index
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\PropertyNotFoundException If $index is not found
     *
     * @return mixed
     */
    #[\ReturnTypeWillChange]
    public function offsetGet($index)
    {
        if (!parent::offsetExists($index)) {
            throw new PropertyNotFoundException($index, __CLASS__);
        }

        return parent::offsetGet($index);
    }
}

class_alias(FieldSettings::class, 'eZ\Publish\Core\FieldType\FieldSettings');
