<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Events\Section;

use Ibexa\Contracts\Core\Repository\Event\BeforeEvent;
use Ibexa\Contracts\Core\Repository\Values\Content\Section;
use Ibexa\Contracts\Core\Repository\Values\Content\SectionUpdateStruct;
use UnexpectedValueException;

final class BeforeUpdateSectionEvent extends BeforeEvent
{
    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Section */
    private $section;

    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\SectionUpdateStruct */
    private $sectionUpdateStruct;

    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Section|null */
    private $updatedSection;

    public function __construct(Section $section, SectionUpdateStruct $sectionUpdateStruct)
    {
        $this->section = $section;
        $this->sectionUpdateStruct = $sectionUpdateStruct;
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
        if (!$this->hasUpdatedSection()) {
            throw new UnexpectedValueException(sprintf('Return value is not set or not of type %s. Check hasUpdatedSection() or set it using setUpdatedSection() before you call the getter.', Section::class));
        }

        return $this->updatedSection;
    }

    public function setUpdatedSection(?Section $updatedSection): void
    {
        $this->updatedSection = $updatedSection;
    }

    public function hasUpdatedSection(): bool
    {
        return $this->updatedSection instanceof Section;
    }
}

class_alias(BeforeUpdateSectionEvent::class, 'eZ\Publish\API\Repository\Events\Section\BeforeUpdateSectionEvent');
