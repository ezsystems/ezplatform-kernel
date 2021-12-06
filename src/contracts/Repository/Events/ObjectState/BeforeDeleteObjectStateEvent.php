<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Events\ObjectState;

use Ibexa\Contracts\Core\Repository\Event\BeforeEvent;
use Ibexa\Contracts\Core\Repository\Values\ObjectState\ObjectState;

final class BeforeDeleteObjectStateEvent extends BeforeEvent
{
    /** @var \Ibexa\Contracts\Core\Repository\Values\ObjectState\ObjectState */
    private $objectState;

    public function __construct(ObjectState $objectState)
    {
        $this->objectState = $objectState;
    }

    public function getObjectState(): ObjectState
    {
        return $this->objectState;
    }
}

class_alias(BeforeDeleteObjectStateEvent::class, 'eZ\Publish\API\Repository\Events\ObjectState\BeforeDeleteObjectStateEvent');
