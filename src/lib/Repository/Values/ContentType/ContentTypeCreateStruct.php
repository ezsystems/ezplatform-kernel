<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\Repository\Values\ContentType;

use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeCreateStruct as APIContentTypeCreateStruct;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinitionCreateStruct;

/**
 * this class is used for creating content types.
 *
 * @property \Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinitionCreateStruct[] $fieldDefinitions the collection of field definitions
 *
 * @internal Meant for internal use by Repository, type hint against API instead.
 */
class ContentTypeCreateStruct extends APIContentTypeCreateStruct
{
    /**
     * Holds the collection of field definitions.
     *
     * @var \Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinitionCreateStruct[]
     */
    public $fieldDefinitions = [];

    /**
     * Adds a new field definition.
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinitionCreateStruct $fieldDef
     */
    public function addFieldDefinition(FieldDefinitionCreateStruct $fieldDef): void
    {
        $this->fieldDefinitions[] = $fieldDef;
    }
}

class_alias(ContentTypeCreateStruct::class, 'eZ\Publish\Core\Repository\Values\ContentType\ContentTypeCreateStruct');
