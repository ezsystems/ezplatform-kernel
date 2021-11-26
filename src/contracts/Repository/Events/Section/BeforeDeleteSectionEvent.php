<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Events\Section;

use Ibexa\Contracts\Core\Repository\Event\BeforeEvent;
use Ibexa\Contracts\Core\Repository\Values\Content\Section;

final class BeforeDeleteSectionEvent extends BeforeEvent
{
    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Section */
    private $section;

    public function __construct(Section $section)
    {
        $this->section = $section;
    }

    public function getSection(): Section
    {
        return $this->section;
    }
}

class_alias(BeforeDeleteSectionEvent::class, 'eZ\Publish\API\Repository\Events\Section\BeforeDeleteSectionEvent');
