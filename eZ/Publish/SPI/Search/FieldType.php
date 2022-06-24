<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace eZ\Publish\SPI\Search;

use eZ\Publish\SPI\Persistence\ValueObject;

/**
 * Base class for document field definitions.
 *
 * @property-read string $type [deprecated] The type name of the facet, deprecated - use {@see \eZ\Publish\SPI\Search\FieldType::getType} instead.
 */
abstract class FieldType extends ValueObject
{
    /**
     * Name of the document field. Will be used to query this field.
     *
     * @var string
     */
    public $name;

    /**
     * The type name of the facet. Has to be handled by the solr schema.
     *
     * @var string
     */
    protected $type;

    /**
     * Whether highlighting should be performed for this field on result documents.
     *
     * @var bool
     */
    public $highlight = false;

    /**
     * The importance of that field (boost factor).
     *
     * @var int
     */
    public $boost = 1;

    /**
     * Whether the field supports multiple values.
     *
     * @var bool
     */
    public $multiValue = false;

    /**
     * Whether the field should be a part of the resulting document.
     *
     * @var bool
     */
    public $inResult = true;

    public function getType(): string
    {
        return $this->type;
    }
}
