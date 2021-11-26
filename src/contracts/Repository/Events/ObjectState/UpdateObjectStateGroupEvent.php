<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Events\ObjectState;

use Ibexa\Contracts\Core\Repository\Event\AfterEvent;
use Ibexa\Contracts\Core\Repository\Values\ObjectState\ObjectStateGroup;
use Ibexa\Contracts\Core\Repository\Values\ObjectState\ObjectStateGroupUpdateStruct;

final class UpdateObjectStateGroupEvent extends AfterEvent
{
    /** @var \Ibexa\Contracts\Core\Repository\Values\ObjectState\ObjectStateGroup */
    private $updatedObjectStateGroup;

    /** @var \Ibexa\Contracts\Core\Repository\Values\ObjectState\ObjectStateGroup */
    private $objectStateGroup;

    /** @var \Ibexa\Contracts\Core\Repository\Values\ObjectState\ObjectStateGroupUpdateStruct */
    private $objectStateGroupUpdateStruct;

    public function __construct(
        ObjectStateGroup $updatedObjectStateGroup,
        ObjectStateGroup $objectStateGroup,
        ObjectStateGroupUpdateStruct $objectStateGroupUpdateStruct
    ) {
        $this->updatedObjectStateGroup = $updatedObjectStateGroup;
        $this->objectStateGroup = $objectStateGroup;
        $this->objectStateGroupUpdateStruct = $objectStateGroupUpdateStruct;
    }

    public function getUpdatedObjectStateGroup(): ObjectStateGroup
    {
        return $this->updatedObjectStateGroup;
    }

    public function getObjectStateGroup(): ObjectStateGroup
    {
        return $this->objectStateGroup;
    }

    public function getObjectStateGroupUpdateStruct(): ObjectStateGroupUpdateStruct
    {
        return $this->objectStateGroupUpdateStruct;
    }
}

class_alias(UpdateObjectStateGroupEvent::class, 'eZ\Publish\API\Repository\Events\ObjectState\UpdateObjectStateGroupEvent');
