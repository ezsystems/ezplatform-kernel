<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Events\Content;

use Ibexa\Contracts\Core\Repository\Event\AfterEvent;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo;

final class DeleteRelationEvent extends AfterEvent
{
    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo */
    private $sourceVersion;

    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo */
    private $destinationContent;

    public function __construct(
        VersionInfo $sourceVersion,
        ContentInfo $destinationContent
    ) {
        $this->sourceVersion = $sourceVersion;
        $this->destinationContent = $destinationContent;
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

class_alias(DeleteRelationEvent::class, 'eZ\Publish\API\Repository\Events\Content\DeleteRelationEvent');
