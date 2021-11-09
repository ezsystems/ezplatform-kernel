<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\Search\Common;

use Ibexa\Contracts\Core\Search\Field;

/**
 * Maps raw field values to something search engine can understand.
 * This is used when indexing Content and matching Content fields.
 * Actual format of the returned value depends on the search engine
 * implementation, meaning engines should override common implementation
 * as needed, but the same input should be handled across engines.
 *
 * @see \Ibexa\Contracts\Core\Search\FieldType
 */
abstract class FieldValueMapper
{
    /**
     * Check if field can be mapped.
     *
     * @param \Ibexa\Contracts\Core\Search\Field $field
     *
     * @return bool
     */
    abstract public function canMap(Field $field);

    /**
     * Map field value to a proper search engine representation.
     *
     * @param \Ibexa\Contracts\Core\Search\Field $field
     *
     * @return mixed|null Returns null on empty value
     */
    abstract public function map(Field $field);
}

class_alias(FieldValueMapper::class, 'eZ\Publish\Core\Search\Common\FieldValueMapper');
