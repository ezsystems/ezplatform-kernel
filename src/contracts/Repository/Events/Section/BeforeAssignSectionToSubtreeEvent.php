<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Events\Section;

use Ibexa\Contracts\Core\Repository\Event\BeforeEvent;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\Repository\Values\Content\Section;

final class BeforeAssignSectionToSubtreeEvent extends BeforeEvent
{
    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Location */
    private $location;

    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Section */
    private $section;

    public function __construct(Location $location, Section $section)
    {
        $this->location = $location;
        $this->section = $section;
    }

    public function getLocation(): Location
    {
        return $this->location;
    }

    public function getSection(): Section
    {
        return $this->section;
    }
}

class_alias(BeforeAssignSectionToSubtreeEvent::class, 'eZ\Publish\API\Repository\Events\Section\BeforeAssignSectionToSubtreeEvent');
