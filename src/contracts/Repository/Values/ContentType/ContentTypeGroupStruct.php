<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Values\ContentType;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;

abstract class ContentTypeGroupStruct extends ValueObject
{
    /**
     * Readable and unique string identifier of a group.
     *
     * @var string
     */
    public $identifier;

    /**
     * @var bool
     */
    public $isSystem = false;
}

class_alias(ContentTypeGroupStruct::class, 'eZ\Publish\API\Repository\Values\ContentType\ContentTypeGroupStruct');
