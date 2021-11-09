<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Contracts\Core\Persistence\Content\Language;

use Ibexa\Contracts\Core\Persistence\ValueObject;

/**
 * Struct containing accessible properties when creating Language entities.
 */
class CreateStruct extends ValueObject
{
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

class_alias(CreateStruct::class, 'eZ\Publish\SPI\Persistence\Content\Language\CreateStruct');
