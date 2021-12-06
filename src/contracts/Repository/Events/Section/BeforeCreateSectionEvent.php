<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Events\Section;

use Ibexa\Contracts\Core\Repository\Event\BeforeEvent;
use Ibexa\Contracts\Core\Repository\Values\Content\Section;
use Ibexa\Contracts\Core\Repository\Values\Content\SectionCreateStruct;
use UnexpectedValueException;

final class BeforeCreateSectionEvent extends BeforeEvent
{
    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\SectionCreateStruct */
    private $sectionCreateStruct;

    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Section|null */
    private $section;

    public function __construct(SectionCreateStruct $sectionCreateStruct)
    {
        $this->sectionCreateStruct = $sectionCreateStruct;
    }

    public function getSectionCreateStruct(): SectionCreateStruct
    {
        return $this->sectionCreateStruct;
    }

    public function getSection(): Section
    {
        if (!$this->hasSection()) {
            throw new UnexpectedValueException(sprintf('Return value is not set or not of type %s. Check hasSection() or set it using setSection() before you call the getter.', Section::class));
        }

        return $this->section;
    }

    public function setSection(?Section $section): void
    {
        $this->section = $section;
    }

    public function hasSection(): bool
    {
        return $this->section instanceof Section;
    }
}

class_alias(BeforeCreateSectionEvent::class, 'eZ\Publish\API\Repository\Events\Section\BeforeCreateSectionEvent');
