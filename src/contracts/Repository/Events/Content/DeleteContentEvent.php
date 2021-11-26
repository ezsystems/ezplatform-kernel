<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Events\Content;

use Ibexa\Contracts\Core\Repository\Event\AfterEvent;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;

final class DeleteContentEvent extends AfterEvent
{
    /** @var array */
    private $locations;

    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo */
    private $contentInfo;

    public function __construct(
        array $locations,
        ContentInfo $contentInfo
    ) {
        $this->locations = $locations;
        $this->contentInfo = $contentInfo;
    }

    public function getLocations(): array
    {
        return $this->locations;
    }

    public function getContentInfo(): ContentInfo
    {
        return $this->contentInfo;
    }
}

class_alias(DeleteContentEvent::class, 'eZ\Publish\API\Repository\Events\Content\DeleteContentEvent');
