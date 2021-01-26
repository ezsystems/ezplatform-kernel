<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace eZ\Publish\API\Repository\Values\URL;

use eZ\Publish\API\Repository\Values\ValueObject;

/**
 * Structure used to update URL data.
 */
class URLUpdateStruct extends ValueObject
{
    /**
     * URL itself e.g. "http://ez.no".
     *
     * @var string|null
     */
    public $url;

    /**
     * Is URL valid ?
     *
     * @var bool|null
     */
    public $isValid;

    /**
     * Modified date.
     *
     * @var \DateTimeInterface|null
     */
    public $lastChecked;
}
