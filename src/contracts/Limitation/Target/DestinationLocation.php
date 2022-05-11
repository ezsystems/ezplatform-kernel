<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Limitation\Target;

use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\SPI\Limitation\Target;
use eZ\Publish\SPI\Persistence\ValueObject;

/**
 * Location Limitation target.
 */
final class DestinationLocation extends ValueObject implements Target
{
    /** @var int */
    private $id;

    /** @var \eZ\Publish\API\Repository\Values\Content\ContentInfo */
    private $targetContentInfo;

    public function __construct(int $id, ContentInfo $targetContentInfo, array $properties = [])
    {
        $this->id = $id;
        $this->targetContentInfo = $targetContentInfo;

        parent::__construct($properties);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTargetContentInfo(): ContentInfo
    {
        return $this->targetContentInfo;
    }
}
