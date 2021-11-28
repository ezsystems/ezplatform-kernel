<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Events\ContentType;

use Ibexa\Contracts\Core\Repository\Event\BeforeEvent;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeGroup;

final class BeforeDeleteContentTypeGroupEvent extends BeforeEvent
{
    /** @var \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeGroup */
    private $contentTypeGroup;

    public function __construct(ContentTypeGroup $contentTypeGroup)
    {
        $this->contentTypeGroup = $contentTypeGroup;
    }

    public function getContentTypeGroup(): ContentTypeGroup
    {
        return $this->contentTypeGroup;
    }
}

class_alias(BeforeDeleteContentTypeGroupEvent::class, 'eZ\Publish\API\Repository\Events\ContentType\BeforeDeleteContentTypeGroupEvent');
