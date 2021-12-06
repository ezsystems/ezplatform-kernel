<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\Repository\Values;

/**
 * @internal Meant for internal use by Repository, type hint against API object instead.
 */
trait MultiLanguageTrait
{
    /**
     * Main language.
     *
     * @var string
     */
    protected $mainLanguageCode;

    /**
     * Prioritized languages provided by user when retrieving object using API.
     *
     * @var string[]
     */
    protected $prioritizedLanguages = [];
}

class_alias(MultiLanguageTrait::class, 'eZ\Publish\Core\Repository\Values\MultiLanguageTrait');
