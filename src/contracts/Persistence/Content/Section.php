<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Contracts\Core\Persistence\Content;

use Ibexa\Contracts\Core\Persistence\ValueObject;

class Section extends ValueObject
{
    /**
     * Id of the section.
     *
     * @var int
     */
    public $id;

    /**
     * Unique identifier of the section.
     *
     * @var string
     */
    public $identifier;

    /**
     * Name of the section.
     *
     * @var string
     */
    public $name;
}

class_alias(Section::class, 'eZ\Publish\SPI\Persistence\Content\Section');
