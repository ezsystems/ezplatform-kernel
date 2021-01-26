<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace eZ\Publish\Core\Repository\Tests\User;

use eZ\Publish\API\Repository\Values\User\User;
use eZ\Publish\Core\Repository\User\PasswordHashService;
use PHPUnit\Framework\TestCase;

final class PasswordHashServiceTest extends TestCase
{
    private const NON_EXISTING_PASSWORD_HASH = PHP_INT_MAX;

    /** @var \eZ\Publish\Core\Repository\User\PasswordHashService */
    private $passwordHashService;

    protected function setUp(): void
    {
        $this->passwordHashService = new PasswordHashService();
    }

    public function testGetSupportedHashTypes(): void
    {
        $this->assertEquals(
            [
                User::PASSWORD_HASH_BCRYPT,
                User::PASSWORD_HASH_PHP_DEFAULT,
            ],
            $this->passwordHashService->getSupportedHashTypes()
        );
    }

    public function testIsHashTypeSupported(): void
    {
        $this->assertTrue($this->passwordHashService->isHashTypeSupported(User::DEFAULT_PASSWORD_HASH));
        $this->assertFalse($this->passwordHashService->isHashTypeSupported(self::NON_EXISTING_PASSWORD_HASH));
    }
}
