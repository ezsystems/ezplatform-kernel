<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Events\ObjectState;

use Ibexa\Contracts\Core\Repository\Event\BeforeEvent;
use Ibexa\Contracts\Core\Repository\Values\ObjectState\ObjectStateGroup;

final class BeforeDeleteObjectStateGroupEvent extends BeforeEvent
{
    /** @var \Ibexa\Contracts\Core\Repository\Values\ObjectState\ObjectStateGroup */
    private $objectStateGroup;

    public function __construct(ObjectStateGroup $objectStateGroup)
    {
        $this->objectStateGroup = $objectStateGroup;
    }

    public function getObjectStateGroup(): ObjectStateGroup
    {
        return $this->objectStateGroup;
    }
}

class_alias(BeforeDeleteObjectStateGroupEvent::class, 'eZ\Publish\API\Repository\Events\ObjectState\BeforeDeleteObjectStateGroupEvent');
