<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace eZ\Publish\Core\Persistence\Legacy\Content\Type\Update\Handler;

use eZ\Publish\Core\Persistence\Legacy\Content\Type\Gateway;
use eZ\Publish\Core\Persistence\Legacy\Content\Type\Update\Handler;
use eZ\Publish\SPI\Persistence\Content\Type;

/**
 * Doctrine database based type update handler.
 *
 * @internal For internal use by Repository
 */
final class DoctrineDatabase extends Handler
{
    /** @var \eZ\Publish\Core\Persistence\Legacy\Content\Type\Gateway */
    protected $contentTypeGateway;

    public function __construct(Gateway $contentTypeGateway)
    {
        $this->contentTypeGateway = $contentTypeGateway;
    }

    public function updateContentObjects(Type $fromType, Type $toType): void
    {
        // Do nothing, content objects are no longer updated
    }

    public function deleteOldType(Type $fromType): void
    {
        $this->contentTypeGateway->delete($fromType->id, $fromType->status);
    }

    public function publishNewType(Type $toType, int $newStatus): void
    {
        $this->contentTypeGateway->publishTypeAndFields(
            $toType->id,
            $toType->status,
            $newStatus
        );
    }
}
