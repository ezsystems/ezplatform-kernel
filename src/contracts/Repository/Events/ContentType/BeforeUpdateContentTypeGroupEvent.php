<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Events\ContentType;

use Ibexa\Contracts\Core\Repository\Event\BeforeEvent;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeGroup;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeGroupUpdateStruct;

final class BeforeUpdateContentTypeGroupEvent extends BeforeEvent
{
    /** @var \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeGroup */
    private $contentTypeGroup;

    /** @var \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeGroupUpdateStruct */
    private $contentTypeGroupUpdateStruct;

    public function __construct(ContentTypeGroup $contentTypeGroup, ContentTypeGroupUpdateStruct $contentTypeGroupUpdateStruct)
    {
        $this->contentTypeGroup = $contentTypeGroup;
        $this->contentTypeGroupUpdateStruct = $contentTypeGroupUpdateStruct;
    }

    public function getContentTypeGroup(): ContentTypeGroup
    {
        return $this->contentTypeGroup;
    }

    public function getContentTypeGroupUpdateStruct(): ContentTypeGroupUpdateStruct
    {
        return $this->contentTypeGroupUpdateStruct;
    }
}

class_alias(BeforeUpdateContentTypeGroupEvent::class, 'eZ\Publish\API\Repository\Events\ContentType\BeforeUpdateContentTypeGroupEvent');
