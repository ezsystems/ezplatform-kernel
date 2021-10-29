<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Contracts\Core\Persistence\Content\Type;

use Ibexa\Contracts\Core\Persistence\ValueObject;

class DeleteByParamsStruct extends ValueObject
{
    /**
     * @var int
     */
    public $modifierId;

    /**
     * @var int
     */
    public $status;
}

class_alias(DeleteByParamsStruct::class, 'eZ\Publish\SPI\Persistence\Content\Type\DeleteByParamsStruct');
