<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Contracts\Core\Persistence\Content\Relation;

use Ibexa\Contracts\Core\Persistence\ValueObject;

/**
 * CreateStruct representing a relation between content.
 */
class CreateStruct extends ValueObject
{
    /**
     * Source Content ID.
     *
     * @var mixed
     */
    public $sourceContentId;

    /**
     * Source Content Version number.
     *
     * @var int
     */
    public $sourceContentVersionNo;

    /**
     * Source Content Type Field Definition Id.
     *
     * @var mixed
     */
    public $sourceFieldDefinitionId;

    /**
     * Destination Content ID.
     *
     * @var mixed
     */
    public $destinationContentId;

    /**
     * Type bitmask.
     *
     * @see \Ibexa\Contracts\Core\Repository\Values\Content\Relation::COMMON,
     *      \Ibexa\Contracts\Core\Repository\Values\Content\Relation::EMBED,
     *      \Ibexa\Contracts\Core\Repository\Values\Content\Relation::LINK,
     *      \Ibexa\Contracts\Core\Repository\Values\Content\Relation::FIELD
     *
     * @var int
     */
    public $type;
}

class_alias(CreateStruct::class, 'eZ\Publish\SPI\Persistence\Content\Relation\CreateStruct');
