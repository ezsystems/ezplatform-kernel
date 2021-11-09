<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Contracts\Core\Persistence\Content\Type\Group;

use Ibexa\Contracts\Core\Persistence\ValueObject;

class UpdateStruct extends ValueObject
{
    /**
     * Primary key.
     *
     * @var mixed
     */
    public $id;

    /**
     * Name.
     *
     * @since 5.0
     *
     * @var string[]
     */
    public $name = [];

    /**
     * Description.
     *
     * @since 5.0
     *
     * @var string[]
     */
    public $description = [];

    /**
     * Readable string identifier of a group.
     *
     * @var string
     */
    public $identifier;

    /**
     * Modified date (timestamp).
     *
     * @var int
     */
    public $modified;

    /**
     * Modifier user id.
     *
     * @var mixed
     */
    public $modifierId;

    /**
     * @var bool
     */
    public $isSystem = false;
}

class_alias(UpdateStruct::class, 'eZ\Publish\SPI\Persistence\Content\Type\Group\UpdateStruct');
