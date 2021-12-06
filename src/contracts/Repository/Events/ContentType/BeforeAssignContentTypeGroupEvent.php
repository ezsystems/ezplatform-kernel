<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Events\ContentType;

use Ibexa\Contracts\Core\Repository\Event\BeforeEvent;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeGroup;

final class BeforeAssignContentTypeGroupEvent extends BeforeEvent
{
    /** @var \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType */
    private $contentType;

    /** @var \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeGroup */
    private $contentTypeGroup;

    public function __construct(ContentType $contentType, ContentTypeGroup $contentTypeGroup)
    {
        $this->contentType = $contentType;
        $this->contentTypeGroup = $contentTypeGroup;
    }

    public function getContentType(): ContentType
    {
        return $this->contentType;
    }

    public function getContentTypeGroup(): ContentTypeGroup
    {
        return $this->contentTypeGroup;
    }
}

class_alias(BeforeAssignContentTypeGroupEvent::class, 'eZ\Publish\API\Repository\Events\ContentType\BeforeAssignContentTypeGroupEvent');
