<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Values\Content\Trash;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;

class TrashItemDeleteResult extends ValueObject
{
    /** @var int */
    public $trashItemId;

    /** @var int */
    public $contentId;

    /**
     * Flag indicating content was removed.
     *
     * @var bool
     */
    public $contentRemoved = false;
}

class_alias(TrashItemDeleteResult::class, 'eZ\Publish\API\Repository\Values\Content\Trash\TrashItemDeleteResult');
