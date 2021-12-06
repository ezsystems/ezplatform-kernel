<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Events\ContentType;

use Ibexa\Contracts\Core\Repository\Event\BeforeEvent;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeDraft;
use UnexpectedValueException;

final class BeforeCreateContentTypeDraftEvent extends BeforeEvent
{
    /** @var \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType */
    private $contentType;

    /** @var \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeDraft|null */
    private $contentTypeDraft;

    public function __construct(ContentType $contentType)
    {
        $this->contentType = $contentType;
    }

    public function getContentType(): ContentType
    {
        return $this->contentType;
    }

    public function getContentTypeDraft(): ContentTypeDraft
    {
        if (!$this->hasContentTypeDraft()) {
            throw new UnexpectedValueException(sprintf('Return value is not set or not of type %s. Check hasContentTypeDraft() or set it using setContentTypeDraft() before you call the getter.', ContentTypeDraft::class));
        }

        return $this->contentTypeDraft;
    }

    public function setContentTypeDraft(?ContentTypeDraft $contentTypeDraft): void
    {
        $this->contentTypeDraft = $contentTypeDraft;
    }

    public function hasContentTypeDraft(): bool
    {
        return $this->contentTypeDraft instanceof ContentTypeDraft;
    }
}

class_alias(BeforeCreateContentTypeDraftEvent::class, 'eZ\Publish\API\Repository\Events\ContentType\BeforeCreateContentTypeDraftEvent');
