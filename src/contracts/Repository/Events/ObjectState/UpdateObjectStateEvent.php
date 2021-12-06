<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Events\ObjectState;

use Ibexa\Contracts\Core\Repository\Event\AfterEvent;
use Ibexa\Contracts\Core\Repository\Values\ObjectState\ObjectState;
use Ibexa\Contracts\Core\Repository\Values\ObjectState\ObjectStateUpdateStruct;

final class UpdateObjectStateEvent extends AfterEvent
{
    /** @var \Ibexa\Contracts\Core\Repository\Values\ObjectState\ObjectState */
    private $updatedObjectState;

    /** @var \Ibexa\Contracts\Core\Repository\Values\ObjectState\ObjectState */
    private $objectState;

    /** @var \Ibexa\Contracts\Core\Repository\Values\ObjectState\ObjectStateUpdateStruct */
    private $objectStateUpdateStruct;

    public function __construct(
        ObjectState $updatedObjectState,
        ObjectState $objectState,
        ObjectStateUpdateStruct $objectStateUpdateStruct
    ) {
        $this->updatedObjectState = $updatedObjectState;
        $this->objectState = $objectState;
        $this->objectStateUpdateStruct = $objectStateUpdateStruct;
    }

    public function getUpdatedObjectState(): ObjectState
    {
        return $this->updatedObjectState;
    }

    public function getObjectState(): ObjectState
    {
        return $this->objectState;
    }

    public function getObjectStateUpdateStruct(): ObjectStateUpdateStruct
    {
        return $this->objectStateUpdateStruct;
    }
}

class_alias(UpdateObjectStateEvent::class, 'eZ\Publish\API\Repository\Events\ObjectState\UpdateObjectStateEvent');
