<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace eZ\Publish\API\Repository\Events\ContentType;

use eZ\Publish\API\Repository\Values\ContentType\ContentType;
use eZ\Publish\API\Repository\Values\User\User;
use eZ\Publish\SPI\Repository\Event\BeforeEvent;
use UnexpectedValueException;

final class BeforeCopyContentTypeEvent extends BeforeEvent
{
    /** @var \eZ\Publish\API\Repository\Values\ContentType\ContentType */
    private $contentType;

    /** @var \eZ\Publish\API\Repository\Values\User\User */
    private $creator;

    /** @var \eZ\Publish\API\Repository\Values\ContentType\ContentType|null */
    private $contentTypeCopy;

    public function __construct(ContentType $contentType, ?User $creator = null)
    {
        $this->contentType = $contentType;
        $this->creator = $creator;
    }

    public function getContentType(): ContentType
    {
        return $this->contentType;
    }

    public function getCreator(): ?User
    {
        return $this->creator;
    }

    public function getContentTypeCopy(): ContentType
    {
        if (!$this->hasContentTypeCopy()) {
            throw new UnexpectedValueException(sprintf('Return value is not set or not of type %s. Check hasContentTypeCopy() or set it using setContentTypeCopy() before you call the getter.', ContentType::class));
        }

        return $this->contentTypeCopy;
    }

    public function setContentTypeCopy(?ContentType $contentTypeCopy): void
    {
        $this->contentTypeCopy = $contentTypeCopy;
    }

    public function hasContentTypeCopy(): bool
    {
        return $this->contentTypeCopy instanceof ContentType;
    }
}
