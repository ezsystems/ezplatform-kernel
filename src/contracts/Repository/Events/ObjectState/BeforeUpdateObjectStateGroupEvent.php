<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Events\ObjectState;

use Ibexa\Contracts\Core\Repository\Event\BeforeEvent;
use Ibexa\Contracts\Core\Repository\Values\ObjectState\ObjectStateGroup;
use Ibexa\Contracts\Core\Repository\Values\ObjectState\ObjectStateGroupUpdateStruct;
use UnexpectedValueException;

final class BeforeUpdateObjectStateGroupEvent extends BeforeEvent
{
    /** @var \Ibexa\Contracts\Core\Repository\Values\ObjectState\ObjectStateGroup */
    private $objectStateGroup;

    /** @var \Ibexa\Contracts\Core\Repository\Values\ObjectState\ObjectStateGroupUpdateStruct */
    private $objectStateGroupUpdateStruct;

    /** @var \Ibexa\Contracts\Core\Repository\Values\ObjectState\ObjectStateGroup|null */
    private $updatedObjectStateGroup;

    public function __construct(ObjectStateGroup $objectStateGroup, ObjectStateGroupUpdateStruct $objectStateGroupUpdateStruct)
    {
        $this->objectStateGroup = $objectStateGroup;
        $this->objectStateGroupUpdateStruct = $objectStateGroupUpdateStruct;
    }

    public function getObjectStateGroup(): ObjectStateGroup
    {
        return $this->objectStateGroup;
    }

    public function getObjectStateGroupUpdateStruct(): ObjectStateGroupUpdateStruct
    {
        return $this->objectStateGroupUpdateStruct;
    }

    public function getUpdatedObjectStateGroup(): ObjectStateGroup
    {
        if (!$this->hasUpdatedObjectStateGroup()) {
            throw new UnexpectedValueException(sprintf('Return value is not set or not of type %s. Check hasUpdatedObjectStateGroup() or set it using setUpdatedObjectStateGroup() before you call the getter.', ObjectStateGroup::class));
        }

        return $this->updatedObjectStateGroup;
    }

    public function setUpdatedObjectStateGroup(?ObjectStateGroup $updatedObjectStateGroup): void
    {
        $this->updatedObjectStateGroup = $updatedObjectStateGroup;
    }

    public function hasUpdatedObjectStateGroup(): bool
    {
        return $this->updatedObjectStateGroup instanceof ObjectStateGroup;
    }
}

class_alias(BeforeUpdateObjectStateGroupEvent::class, 'eZ\Publish\API\Repository\Events\ObjectState\BeforeUpdateObjectStateGroupEvent');
