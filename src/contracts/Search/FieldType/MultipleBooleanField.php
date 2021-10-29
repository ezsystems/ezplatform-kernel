<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Contracts\Core\Search\FieldType;

use Ibexa\Contracts\Core\Search\FieldType;

/**
 * Boolean document field.
 */
class MultipleBooleanField extends FieldType
{
    /**
     * The type name of the facet. Has to be handled by the solr schema.
     *
     * @var string
     */
    protected $type = 'ez_mboolean';
}

class_alias(MultipleBooleanField::class, 'eZ\Publish\SPI\Search\FieldType\MultipleBooleanField');
