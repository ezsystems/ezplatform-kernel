<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace eZ\Publish\API\Repository\Events\ContentType;

use eZ\Publish\API\Repository\Values\ContentType\ContentTypeGroup;
use eZ\Publish\SPI\Repository\Event\BeforeEvent;

final class BeforeDeleteContentTypeGroupEvent extends BeforeEvent
{
    /** @var \eZ\Publish\API\Repository\Values\ContentType\ContentTypeGroup */
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
