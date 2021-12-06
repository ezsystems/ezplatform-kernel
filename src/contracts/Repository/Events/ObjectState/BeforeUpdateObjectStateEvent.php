<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Events\ObjectState;

use Ibexa\Contracts\Core\Repository\Event\BeforeEvent;
use Ibexa\Contracts\Core\Repository\Values\ObjectState\ObjectState;
use Ibexa\Contracts\Core\Repository\Values\ObjectState\ObjectStateUpdateStruct;
use UnexpectedValueException;

final class BeforeUpdateObjectStateEvent extends BeforeEvent
{
    /** @var \Ibexa\Contracts\Core\Repository\Values\ObjectState\ObjectState */
    private $objectState;

    /** @var \Ibexa\Contracts\Core\Repository\Values\ObjectState\ObjectStateUpdateStruct */
    private $objectStateUpdateStruct;

    /** @var \Ibexa\Contracts\Core\Repository\Values\ObjectState\ObjectState|null */
    private $updatedObjectState;

    public function __construct(ObjectState $objectState, ObjectStateUpdateStruct $objectStateUpdateStruct)
    {
        $this->objectState = $objectState;
        $this->objectStateUpdateStruct = $objectStateUpdateStruct;
    }

    public function getObjectState(): ObjectState
    {
        return $this->objectState;
    }

    public function getObjectStateUpdateStruct(): ObjectStateUpdateStruct
    {
        return $this->objectStateUpdateStruct;
    }

    public function getUpdatedObjectState(): ObjectState
    {
        if (!$this->hasUpdatedObjectState()) {
            throw new UnexpectedValueException(sprintf('Return value is not set or not of type %s. Check hasUpdatedObjectState() or set it using setUpdatedObjectState() before you call the getter.', ObjectState::class));
        }

        return $this->updatedObjectState;
    }

    public function setUpdatedObjectState(?ObjectState $updatedObjectState): void
    {
        $this->updatedObjectState = $updatedObjectState;
    }

    public function hasUpdatedObjectState(): bool
    {
        return $this->updatedObjectState instanceof ObjectState;
    }
}

class_alias(BeforeUpdateObjectStateEvent::class, 'eZ\Publish\API\Repository\Events\ObjectState\BeforeUpdateObjectStateEvent');
