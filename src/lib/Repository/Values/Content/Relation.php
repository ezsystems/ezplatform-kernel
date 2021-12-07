<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\Repository\Values\Content;

use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo as APIContentInfo;
use Ibexa\Contracts\Core\Repository\Values\Content\Relation as APIRelation;

/**
 * Class representing a relation between content.
 *
 * @property-read mixed $id the internal id of the relation
 * @property-read string $sourceFieldDefinitionIdentifier the field definition identifier of the field where this relation is anchored if the relation is of type EMBED, LINK, or ATTRIBUTE
 * @property-read \Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo $sourceContentInfo - calls {@link getSourceContentInfo()}
 * @property-read \Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo $destinationContentInfo - calls {@link getDestinationContentInfo()}
 * @property-read int $type The relation type bitmask containing one or more of Relation::COMMON, Relation::EMBED, Relation::LINK, Relation::FIELD
 *
 * @internal Meant for internal use by Repository, type hint against API object instead.
 */
class Relation extends APIRelation
{
    /**
     * the content of the source content of the relation.
     *
     * @var \Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo
     */
    protected $sourceContentInfo;

    /**
     * the content of the destination content of the relation.
     *
     * @var \Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo
     */
    protected $destinationContentInfo;

    /**
     * the content of the source content of the relation.
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo
     */
    public function getSourceContentInfo(): APIContentInfo
    {
        return $this->sourceContentInfo;
    }

    /**
     * the content of the destination content of the relation.
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo
     */
    public function getDestinationContentInfo(): APIContentInfo
    {
        return $this->destinationContentInfo;
    }
}

class_alias(Relation::class, 'eZ\Publish\Core\Repository\Values\Content\Relation');
