<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Events\Trash;

use Ibexa\Contracts\Core\Repository\Event\BeforeEvent;
use Ibexa\Contracts\Core\Repository\Values\Content\Trash\TrashItemDeleteResult;
use Ibexa\Contracts\Core\Repository\Values\Content\TrashItem;
use UnexpectedValueException;

final class BeforeDeleteTrashItemEvent extends BeforeEvent
{
    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\TrashItem */
    private $trashItem;

    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Trash\TrashItemDeleteResult|null */
    private $result;

    public function __construct(TrashItem $trashItem)
    {
        $this->trashItem = $trashItem;
    }

    public function getTrashItem(): TrashItem
    {
        return $this->trashItem;
    }

    public function getResult(): TrashItemDeleteResult
    {
        if (!$this->hasResult()) {
            throw new UnexpectedValueException(sprintf('Return value is not set or not of type %s. Check hasResult() or set it using setResult() before you call the getter.', TrashItemDeleteResult::class));
        }

        return $this->result;
    }

    public function setResult(?TrashItemDeleteResult $result): void
    {
        $this->result = $result;
    }

    public function hasResult(): bool
    {
        return $this->result instanceof TrashItemDeleteResult;
    }
}

class_alias(BeforeDeleteTrashItemEvent::class, 'eZ\Publish\API\Repository\Events\Trash\BeforeDeleteTrashItemEvent');
