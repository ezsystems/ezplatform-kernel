<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Core\FieldType\User\UserStorage\Gateway;

use Ibexa\Core\FieldType\User\UserStorage\Gateway as UserStorageGateway;
use Ibexa\Core\FieldType\User\UserStorage\Gateway\DoctrineStorage;
use Ibexa\Tests\Integration\Core\User\UserStorage\UserStorageGatewayTest;

final class UserDoctrineStorageGatewayTest extends UserStorageGatewayTest
{
    protected function getGateway(): UserStorageGateway
    {
        return new DoctrineStorage($this->getDatabaseConnection());
    }
}
