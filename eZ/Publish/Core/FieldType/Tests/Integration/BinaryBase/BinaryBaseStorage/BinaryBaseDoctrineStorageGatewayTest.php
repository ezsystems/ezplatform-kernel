<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Integration\BinaryBase\BinaryBaseStorage;

use eZ\Publish\Core\FieldType\BinaryFile\BinaryFileStorage\Gateway\DoctrineStorage;
use eZ\Publish\Core\FieldType\Tests\Integration\BinaryBase\BinaryBaseStorage\BinaryBaseStorageGatewayTest;
use eZ\Publish\Core\FieldType\BinaryBase\BinaryBaseStorage\Gateway as BinaryBaseStorageGateway;

final class BinaryBaseDoctrineStorageGatewayTest extends BinaryBaseStorageGatewayTest
{
    protected function getGateway(): BinaryBaseStorageGateway
    {
        return new DoctrineStorage($this->getDatabaseConnection());
    }
}
