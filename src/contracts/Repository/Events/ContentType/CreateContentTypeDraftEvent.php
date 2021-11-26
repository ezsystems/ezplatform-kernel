<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Events\ContentType;

use Ibexa\Contracts\Core\Repository\Event\AfterEvent;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeDraft;

final class CreateContentTypeDraftEvent extends AfterEvent
{
    /** @var \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeDraft */
    private $contentTypeDraft;

    /** @var \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType */
    private $contentType;

    public function __construct(
        ContentTypeDraft $contentTypeDraft,
        ContentType $contentType
    ) {
        $this->contentTypeDraft = $contentTypeDraft;
        $this->contentType = $contentType;
    }

    public function getContentTypeDraft(): ContentTypeDraft
    {
        return $this->contentTypeDraft;
    }

    public function getContentType(): ContentType
    {
        return $this->contentType;
    }
}

class_alias(CreateContentTypeDraftEvent::class, 'eZ\Publish\API\Repository\Events\ContentType\CreateContentTypeDraftEvent');
