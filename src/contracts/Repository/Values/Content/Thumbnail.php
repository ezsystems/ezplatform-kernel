<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Values\Content;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;

/**
 * @property-read string $resource
 * @property-read int|null $width
 * @property-read int|null $height
 * @property-read string|null $mimeType
 */
class Thumbnail extends ValueObject
{
    /**
     * Can be target URL or Base64 data (or anything else).
     *
     * @var string
     */
    protected $resource;

    /** @var int|null */
    protected $width;

    /** @var int|null */
    protected $height;

    /** @var string|null */
    protected $mimeType;
}

class_alias(Thumbnail::class, 'eZ\Publish\API\Repository\Values\Content\Thumbnail');
