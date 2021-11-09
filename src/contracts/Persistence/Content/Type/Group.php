<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Contracts\Core\Persistence\Content\Type;

use Ibexa\Contracts\Core\Persistence\ValueObject;

class Group extends ValueObject
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
     * Legacy note: Maps to existing name property
     *
     * @var string
     */
    public $identifier;

    /**
     * Created date (timestamp).
     *
     * @var int
     */
    public $created;

    /**
     * Modified date (timestamp).
     *
     * @var int
     */
    public $modified;

    /**
     * Creator user id.
     *
     * @var mixed
     */
    public $creatorId;

    /**
     * Modifier user id.
     *
     * @var mixed
     */
    public $modifierId;

    /**
     * The flag indicates if ContentTypeGroup is system specific.
     *
     * @var bool
     */
    public $isSystem;
}

class_alias(Group::class, 'eZ\Publish\SPI\Persistence\Content\Type\Group');
