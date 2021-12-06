<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Events\ContentType;

use Ibexa\Contracts\Core\Repository\Event\BeforeEvent;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeDraft;

final class BeforePublishContentTypeDraftEvent extends BeforeEvent
{
    /** @var \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeDraft */
    private $contentTypeDraft;

    public function __construct(ContentTypeDraft $contentTypeDraft)
    {
        $this->contentTypeDraft = $contentTypeDraft;
    }

    public function getContentTypeDraft(): ContentTypeDraft
    {
        return $this->contentTypeDraft;
    }
}

class_alias(BeforePublishContentTypeDraftEvent::class, 'eZ\Publish\API\Repository\Events\ContentType\BeforePublishContentTypeDraftEvent');
