<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Contracts\Core\Search\FieldType;

use Ibexa\Contracts\Core\Search\FieldType;

/**
 * Identifier document field.
 */
class IdentifierField extends FieldType
{
    /**
     * The type name of the facet. Has to be handled by the solr schema.
     *
     * @var string
     */
    protected $type = 'ez_id';

    /**
     * Indicates that value will not be normalized.
     *
     * @var bool
     */
    protected $raw = false;
}

class_alias(IdentifierField::class, 'eZ\Publish\SPI\Search\FieldType\IdentifierField');
