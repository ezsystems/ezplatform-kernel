<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Values\Content;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;

/**
 * Base struct for content create/update structs.
 *
 * @property \Ibexa\Contracts\Core\Repository\Values\Content\Field[] $fields
 */
abstract class ContentStruct extends ValueObject
{
    /**
     * Adds a field to the field collection.
     *
     * This method could also be implemented by a magic setter so that
     * $fields[$fieldDefIdentifier][$language] = $value or without language $fields[$fieldDefIdentifier] = $value
     * is an equivalent call.
     *
     * @param string $fieldDefIdentifier the identifier of the field definition
     * @param mixed $value Either a plain value which is understandable by the corresponding
     *                     field type or an instance of a Value class provided by the field type
     * @param string|null $language If not given on a translatable field the initial language is used
     */
    abstract public function setField(string $fieldDefIdentifier, $value, ?string $language = null): void;
}

class_alias(ContentStruct::class, 'eZ\Publish\API\Repository\Values\Content\ContentStruct');
