<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Events\ObjectState;

use Ibexa\Contracts\Core\Repository\Event\AfterEvent;
use Ibexa\Contracts\Core\Repository\Values\ObjectState\ObjectState;

final class SetPriorityOfObjectStateEvent extends AfterEvent
{
    /** @var \Ibexa\Contracts\Core\Repository\Values\ObjectState\ObjectState */
    private $objectState;

    private $priority;

    public function __construct(
        ObjectState $objectState,
        $priority
    ) {
        $this->objectState = $objectState;
        $this->priority = $priority;
    }

    public function getObjectState(): ObjectState
    {
        return $this->objectState;
    }

    public function getPriority()
    {
        return $this->priority;
    }
}

class_alias(SetPriorityOfObjectStateEvent::class, 'eZ\Publish\API\Repository\Events\ObjectState\SetPriorityOfObjectStateEvent');
