<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Events\Content;

use Ibexa\Contracts\Core\Repository\Event\AfterEvent;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Contracts\Core\Repository\Values\Content\Relation;
use Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo;

final class AddRelationEvent extends AfterEvent
{
    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Relation */
    private $relation;

    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo */
    private $sourceVersion;

    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo */
    private $destinationContent;

    public function __construct(
        Relation $relation,
        VersionInfo $sourceVersion,
        ContentInfo $destinationContent
    ) {
        $this->relation = $relation;
        $this->sourceVersion = $sourceVersion;
        $this->destinationContent = $destinationContent;
    }

    public function getRelation(): Relation
    {
        return $this->relation;
    }

    public function getSourceVersion(): VersionInfo
    {
        return $this->sourceVersion;
    }

    public function getDestinationContent(): ContentInfo
    {
        return $this->destinationContent;
    }
}

class_alias(AddRelationEvent::class, 'eZ\Publish\API\Repository\Events\Content\AddRelationEvent');
