<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace eZ\Publish\API\Repository\Events\ContentType;

use eZ\Publish\API\Repository\Values\ContentType\ContentTypeDraft;
use eZ\Publish\SPI\Repository\Event\BeforeEvent;
use UnexpectedValueException;

final class BeforeRemoveContentTypeTranslationEvent extends BeforeEvent
{
    /** @var \eZ\Publish\API\Repository\Values\ContentType\ContentTypeDraft */
    private $contentTypeDraft;

    /** @var string */
    private $languageCode;

    /** @var \eZ\Publish\API\Repository\Values\ContentType\ContentTypeDraft|null */
    private $newContentTypeDraft;

    public function __construct(ContentTypeDraft $contentTypeDraft, string $languageCode)
    {
        $this->contentTypeDraft = $contentTypeDraft;
        $this->languageCode = $languageCode;
    }

    public function getContentTypeDraft(): ContentTypeDraft
    {
        return $this->contentTypeDraft;
    }

    public function getLanguageCode(): string
    {
        return $this->languageCode;
    }

    public function getNewContentTypeDraft(): ContentTypeDraft
    {
        if (!$this->hasNewContentTypeDraft()) {
            throw new UnexpectedValueException(sprintf('Return value is not set or not of type %s. Check hasNewContentTypeDraft() or set it using setNewContentTypeDraft() before you call the getter.', ContentTypeDraft::class));
        }

        return $this->newContentTypeDraft;
    }

    public function setNewContentTypeDraft(?ContentTypeDraft $newContentTypeDraft): void
    {
        $this->newContentTypeDraft = $newContentTypeDraft;
    }

    public function hasNewContentTypeDraft(): bool
    {
        return $this->newContentTypeDraft instanceof ContentTypeDraft;
    }
}
