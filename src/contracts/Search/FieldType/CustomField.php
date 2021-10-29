<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Contracts\Core\Search\FieldType;

use Ibexa\Contracts\Core\Search\FieldType;

/**
 * Custom document field.
 */
class CustomField extends FieldType
{
    /**
     * The type name of the facet. Has to be handled by the solr schema.
     *
     * @var string
     */
    public $type;
}

class_alias(CustomField::class, 'eZ\Publish\SPI\Search\FieldType\CustomField');
