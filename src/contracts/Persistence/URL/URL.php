<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Contracts\Core\Persistence\URL;

use Ibexa\Contracts\Core\Persistence\ValueObject;

class URL extends ValueObject
{
    /**
     * ID of the URL.
     *
     * @var int
     */
    public $id;

    /**
     * URL address.
     *
     * @var string
     */
    public $url;

    /**
     * MD5 checksum of original URL.
     *
     * @var string
     */
    public $originalUrlMd5;

    /**
     * Is URL valid?
     *
     * @var bool
     */
    public $isValid;

    /**
     * Date of last check (timestamp).
     *
     * @var int
     */
    public $lastChecked;

    /**
     * Creation date (timestamp).
     *
     * @var int
     */
    public $created;

    /**
     * Modified date (timestamp).
     *
     * @var int
     */
    public $modified;
}

class_alias(URL::class, 'eZ\Publish\SPI\Persistence\URL\URL');
