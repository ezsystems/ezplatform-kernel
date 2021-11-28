<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Events\ContentType;

use Ibexa\Contracts\Core\Repository\Event\AfterEvent;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeDraft;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeUpdateStruct;

final class UpdateContentTypeDraftEvent extends AfterEvent
{
    /** @var \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeDraft */
    private $contentTypeDraft;

    /** @var \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeUpdateStruct */
    private $contentTypeUpdateStruct;

    public function __construct(
        ContentTypeDraft $contentTypeDraft,
        ContentTypeUpdateStruct $contentTypeUpdateStruct
    ) {
        $this->contentTypeDraft = $contentTypeDraft;
        $this->contentTypeUpdateStruct = $contentTypeUpdateStruct;
    }

    public function getContentTypeDraft(): ContentTypeDraft
    {
        return $this->contentTypeDraft;
    }

    public function getContentTypeUpdateStruct(): ContentTypeUpdateStruct
    {
        return $this->contentTypeUpdateStruct;
    }
}

class_alias(UpdateContentTypeDraftEvent::class, 'eZ\Publish\API\Repository\Events\ContentType\UpdateContentTypeDraftEvent');
