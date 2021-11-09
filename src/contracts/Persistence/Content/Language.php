<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Contracts\Core\Persistence\Content;

use Ibexa\Contracts\Core\Persistence\ValueObject;

/**
 * Struct containing accessible properties on Language entities.
 */
class Language extends ValueObject
{
    /**
     * Language ID.
     *
     * @var mixed
     */
    public $id;

    /**
     * Language Code (eg: eng-GB).
     *
     * @var string
     */
    public $languageCode;

    /**
     * Human readable language name.
     *
     * @var string
     */
    public $name;

    /**
     * Indicates if language is enabled or not.
     *
     * @var bool
     */
    public $isEnabled = true;
}

class_alias(Language::class, 'eZ\Publish\SPI\Persistence\Content\Language');
