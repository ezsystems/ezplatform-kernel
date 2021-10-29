<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Values\URL;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;

class URL extends ValueObject
{
    /**
     * The unique id of the URL.
     *
     * @var int
     */
    protected $id;

    /**
     * URL itself e.g. "http://ez.no".
     *
     * @var string
     */
    protected $url;

    /**
     * Is URL valid ?
     *
     * @var bool
     */
    protected $isValid;

    /**
     * Date of last check.
     *
     * @var \DateTimeInterface
     */
    protected $lastChecked;

    /**
     * Creation date.
     *
     * @var \DateTimeInterface
     */
    protected $created;

    /**
     * Modified date.
     *
     * @var \DateTimeInterface
     */
    protected $modified;
}

class_alias(URL::class, 'eZ\Publish\API\Repository\Values\URL\URL');
