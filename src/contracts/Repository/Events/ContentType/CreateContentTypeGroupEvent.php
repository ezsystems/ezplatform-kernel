<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Events\ContentType;

use Ibexa\Contracts\Core\Repository\Event\AfterEvent;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeGroup;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeGroupCreateStruct;

final class CreateContentTypeGroupEvent extends AfterEvent
{
    /** @var \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeGroup */
    private $contentTypeGroup;

    /** @var \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeGroupCreateStruct */
    private $contentTypeGroupCreateStruct;

    public function __construct(
        ContentTypeGroup $contentTypeGroup,
        ContentTypeGroupCreateStruct $contentTypeGroupCreateStruct
    ) {
        $this->contentTypeGroup = $contentTypeGroup;
        $this->contentTypeGroupCreateStruct = $contentTypeGroupCreateStruct;
    }

    public function getReturnValue(): ContentTypeGroup
    {
        return $this->contentTypeGroup;
    }

    public function getContentTypeGroupCreateStruct(): ContentTypeGroupCreateStruct
    {
        return $this->contentTypeGroupCreateStruct;
    }
}

class_alias(CreateContentTypeGroupEvent::class, 'eZ\Publish\API\Repository\Events\ContentType\CreateContentTypeGroupEvent');
