<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Events\ObjectState;

use Ibexa\Contracts\Core\Repository\Event\BeforeEvent;
use Ibexa\Contracts\Core\Repository\Values\ObjectState\ObjectState;
use Ibexa\Contracts\Core\Repository\Values\ObjectState\ObjectStateCreateStruct;
use Ibexa\Contracts\Core\Repository\Values\ObjectState\ObjectStateGroup;
use UnexpectedValueException;

final class BeforeCreateObjectStateEvent extends BeforeEvent
{
    /** @var \Ibexa\Contracts\Core\Repository\Values\ObjectState\ObjectStateGroup */
    private $objectStateGroup;

    /** @var \Ibexa\Contracts\Core\Repository\Values\ObjectState\ObjectStateCreateStruct */
    private $objectStateCreateStruct;

    /** @var \Ibexa\Contracts\Core\Repository\Values\ObjectState\ObjectState|null */
    private $objectState;

    public function __construct(ObjectStateGroup $objectStateGroup, ObjectStateCreateStruct $objectStateCreateStruct)
    {
        $this->objectStateGroup = $objectStateGroup;
        $this->objectStateCreateStruct = $objectStateCreateStruct;
    }

    public function getObjectStateGroup(): ObjectStateGroup
    {
        return $this->objectStateGroup;
    }

    public function getObjectStateCreateStruct(): ObjectStateCreateStruct
    {
        return $this->objectStateCreateStruct;
    }

    public function getObjectState(): ObjectState
    {
        if (!$this->hasObjectState()) {
            throw new UnexpectedValueException(sprintf('Return value is not set or not of type %s. Check hasObjectState() or set it using setObjectState() before you call the getter.', ObjectState::class));
        }

        return $this->objectState;
    }

    public function setObjectState(?ObjectState $objectState): void
    {
        $this->objectState = $objectState;
    }

    public function hasObjectState(): bool
    {
        return $this->objectState instanceof ObjectState;
    }
}

class_alias(BeforeCreateObjectStateEvent::class, 'eZ\Publish\API\Repository\Events\ObjectState\BeforeCreateObjectStateEvent');
