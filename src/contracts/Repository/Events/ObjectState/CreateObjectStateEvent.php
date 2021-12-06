<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Events\ObjectState;

use Ibexa\Contracts\Core\Repository\Event\AfterEvent;
use Ibexa\Contracts\Core\Repository\Values\ObjectState\ObjectState;
use Ibexa\Contracts\Core\Repository\Values\ObjectState\ObjectStateCreateStruct;
use Ibexa\Contracts\Core\Repository\Values\ObjectState\ObjectStateGroup;

final class CreateObjectStateEvent extends AfterEvent
{
    /** @var \Ibexa\Contracts\Core\Repository\Values\ObjectState\ObjectState */
    private $objectState;

    /** @var \Ibexa\Contracts\Core\Repository\Values\ObjectState\ObjectStateGroup */
    private $objectStateGroup;

    /** @var \Ibexa\Contracts\Core\Repository\Values\ObjectState\ObjectStateCreateStruct */
    private $objectStateCreateStruct;

    public function __construct(
        ObjectState $objectState,
        ObjectStateGroup $objectStateGroup,
        ObjectStateCreateStruct $objectStateCreateStruct
    ) {
        $this->objectState = $objectState;
        $this->objectStateGroup = $objectStateGroup;
        $this->objectStateCreateStruct = $objectStateCreateStruct;
    }

    public function getObjectState(): ObjectState
    {
        return $this->objectState;
    }

    public function getObjectStateGroup(): ObjectStateGroup
    {
        return $this->objectStateGroup;
    }

    public function getObjectStateCreateStruct(): ObjectStateCreateStruct
    {
        return $this->objectStateCreateStruct;
    }
}

class_alias(CreateObjectStateEvent::class, 'eZ\Publish\API\Repository\Events\ObjectState\CreateObjectStateEvent');
