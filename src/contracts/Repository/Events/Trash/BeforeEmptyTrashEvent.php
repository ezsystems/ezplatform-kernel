<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Events\Trash;

use Ibexa\Contracts\Core\Repository\Event\BeforeEvent;
use Ibexa\Contracts\Core\Repository\Values\Content\Trash\TrashItemDeleteResultList;
use UnexpectedValueException;

final class BeforeEmptyTrashEvent extends BeforeEvent
{
    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Trash\TrashItemDeleteResultList|null */
    private $resultList;

    public function __construct()
    {
    }

    public function getResultList(): TrashItemDeleteResultList
    {
        if (!$this->hasResultList()) {
            throw new UnexpectedValueException(sprintf('Return value is not set or not of type %s. Check hasResultList() or set it using setResultList() before you call the getter.', TrashItemDeleteResultList::class));
        }

        return $this->resultList;
    }

    public function setResultList(?TrashItemDeleteResultList $resultList): void
    {
        $this->resultList = $resultList;
    }

    public function hasResultList(): bool
    {
        return $this->resultList instanceof TrashItemDeleteResultList;
    }
}

class_alias(BeforeEmptyTrashEvent::class, 'eZ\Publish\API\Repository\Events\Trash\BeforeEmptyTrashEvent');
