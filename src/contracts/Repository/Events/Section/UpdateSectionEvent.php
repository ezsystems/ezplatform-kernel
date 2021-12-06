<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Events\Section;

use Ibexa\Contracts\Core\Repository\Event\AfterEvent;
use Ibexa\Contracts\Core\Repository\Values\Content\Section;
use Ibexa\Contracts\Core\Repository\Values\Content\SectionUpdateStruct;

final class UpdateSectionEvent extends AfterEvent
{
    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Section */
    private $section;

    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\SectionUpdateStruct */
    private $sectionUpdateStruct;

    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Section */
    private $updatedSection;

    public function __construct(
        Section $updatedSection,
        Section $section,
        SectionUpdateStruct $sectionUpdateStruct
    ) {
        $this->section = $section;
        $this->sectionUpdateStruct = $sectionUpdateStruct;
        $this->updatedSection = $updatedSection;
    }

    public function getSection(): Section
    {
        return $this->section;
    }

    public function getSectionUpdateStruct(): SectionUpdateStruct
    {
        return $this->sectionUpdateStruct;
    }

    public function getUpdatedSection(): Section
    {
        return $this->updatedSection;
    }
}

class_alias(UpdateSectionEvent::class, 'eZ\Publish\API\Repository\Events\Section\UpdateSectionEvent');
