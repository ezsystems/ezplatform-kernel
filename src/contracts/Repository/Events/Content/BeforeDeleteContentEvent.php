<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Events\Content;

use Ibexa\Contracts\Core\Repository\Event\BeforeEvent;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use UnexpectedValueException;

final class BeforeDeleteContentEvent extends BeforeEvent
{
    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo */
    private $contentInfo;

    /** @var array|null */
    private $locations;

    public function __construct(ContentInfo $contentInfo)
    {
        $this->contentInfo = $contentInfo;
    }

    public function getContentInfo(): ContentInfo
    {
        return $this->contentInfo;
    }

    public function getLocations(): array
    {
        if (!$this->hasLocations()) {
            throw new UnexpectedValueException('If you use stopPropagation(), you must set the event return value to be an array using setLocations()');
        }

        return $this->locations;
    }

    public function setLocations(?array $locations): void
    {
        $this->locations = $locations;
    }

    public function hasLocations(): bool
    {
        return is_array($this->locations);
    }
}

class_alias(BeforeDeleteContentEvent::class, 'eZ\Publish\API\Repository\Events\Content\BeforeDeleteContentEvent');
