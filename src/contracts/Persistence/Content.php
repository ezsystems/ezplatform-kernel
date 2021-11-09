<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Contracts\Core\Persistence;

/**
 * Content value object, bound to a version.
 * This object aggregates the following:
 *  - Version metadata
 *  - Content metadata
 *  - Fields.
 */
class Content extends ValueObject
{
    /**
     * VersionInfo object for this content's version.
     *
     * @var \Ibexa\Contracts\Core\Persistence\Content\VersionInfo
     */
    public $versionInfo;

    /**
     * Field objects for this content.
     *
     * @var \Ibexa\Contracts\Core\Persistence\Content\Field[]
     */
    public $fields;
}

class_alias(Content::class, 'eZ\Publish\SPI\Persistence\Content');
