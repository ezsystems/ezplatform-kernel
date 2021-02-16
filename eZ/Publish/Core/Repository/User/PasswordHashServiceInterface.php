<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace eZ\Publish\Core\Repository\User;

/**
 * @internal
 */
interface PasswordHashServiceInterface
{
    public function getDefaultHashType(): int;

    /**
     * @throws \eZ\Publish\Core\Repository\User\Exception\UnsupportedPasswordHashType
     */
    public function createPasswordHash(string $password, ?int $hashType = null): string;

    public function isValidPassword(string $plainPassword, string $passwordHash, ?int $hashType = null): bool;
}
