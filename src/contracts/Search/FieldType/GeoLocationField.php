<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Contracts\Core\Search\FieldType;

use Ibexa\Contracts\Core\Search\FieldType;

/**
 * GeoLocation document field.
 */
class GeoLocationField extends FieldType
{
    /**
     * The type name of the facet. Has to be handled by the solr schema.
     *
     * @var string
     */
    protected $type = 'ez_geolocation';
}

class_alias(GeoLocationField::class, 'eZ\Publish\SPI\Search\FieldType\GeoLocationField');
