<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Events\Trash;

use Ibexa\Contracts\Core\Repository\Event\AfterEvent;
use Ibexa\Contracts\Core\Repository\Values\Content\Trash\TrashItemDeleteResultList;

final class EmptyTrashEvent extends AfterEvent
{
    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Trash\TrashItemDeleteResultList */
    private $resultList;

    public function __construct(TrashItemDeleteResultList $resultList)
    {
        $this->resultList = $resultList;
    }

    public function getResultList(): TrashItemDeleteResultList
    {
        return $this->resultList;
    }
}

class_alias(EmptyTrashEvent::class, 'eZ\Publish\API\Repository\Events\Trash\EmptyTrashEvent');
