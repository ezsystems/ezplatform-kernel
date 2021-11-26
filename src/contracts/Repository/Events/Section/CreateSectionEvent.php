<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Events\Section;

use Ibexa\Contracts\Core\Repository\Event\AfterEvent;
use Ibexa\Contracts\Core\Repository\Values\Content\Section;
use Ibexa\Contracts\Core\Repository\Values\Content\SectionCreateStruct;

final class CreateSectionEvent extends AfterEvent
{
    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\SectionCreateStruct */
    private $sectionCreateStruct;

    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Section */
    private $section;

    public function __construct(
        Section $section,
        SectionCreateStruct $sectionCreateStruct
    ) {
        $this->sectionCreateStruct = $sectionCreateStruct;
        $this->section = $section;
    }

    public function getSectionCreateStruct(): SectionCreateStruct
    {
        return $this->sectionCreateStruct;
    }

    public function getSection(): Section
    {
        return $this->section;
    }
}

class_alias(CreateSectionEvent::class, 'eZ\Publish\API\Repository\Events\Section\CreateSectionEvent');
