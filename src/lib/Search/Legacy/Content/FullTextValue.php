<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\Search\Legacy\Content;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;

/**
 * Represents full text searchable value of Content object field which can be indexed by the legacy search engine.
 */
class FullTextValue extends ValueObject
{
    /**
     * Content object field Id.
     *
     * @var int
     */
    public $id;

    /**
     * Content object field definition id.
     *
     * @var int
     */
    public $fieldDefinitionId;

    /**
     * Content object field identifier.
     *
     * @var string
     */
    public $fieldDefinitionIdentifier;

    /** @var string */
    public $languageCode;

    /**
     * Searchable value.
     *
     * @var string
     */
    public $value;

    /**
     * Is value from main language and always available.
     *
     * @var bool
     */
    public $isMainAndAlwaysAvailable;

    /**
     * Array of rules to be used when transforming the value.
     *
     * @var array
     */
    public $transformationRules;

    /**
     * Flag whether the value should be split by non-words.
     *
     * @var bool
     */
    public $splitFlag;
}

class_alias(FullTextValue::class, 'eZ\Publish\Core\Search\Legacy\Content\FullTextValue');
