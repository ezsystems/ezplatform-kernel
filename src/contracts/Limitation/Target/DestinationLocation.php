<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Limitation\Target;

use eZ\Publish\SPI\Limitation\Target;
use eZ\Publish\SPI\Persistence\ValueObject;

/**
 * Location Limitation target.
 *
 * @property-read int $id
 * @property-read \eZ\Publish\API\Repository\Values\Content\ContentInfo $targetContentInfo
 */
class DestinationLocation extends ValueObject implements Target
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var \eZ\Publish\API\Repository\Values\Content\ContentInfo
     */
    protected $targetContentInfo;
}
