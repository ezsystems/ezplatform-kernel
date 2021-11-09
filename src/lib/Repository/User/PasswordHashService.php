<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\Repository\User;

use Ibexa\Contracts\Core\Repository\Values\User\User;
use Ibexa\Core\Repository\User\Exception\UnsupportedPasswordHashType;

/**
 * @internal
 */
final class PasswordHashService implements PasswordHashServiceInterface
{
    /** @var int */
    private $defaultHashType;

    public function __construct(int $hashType = User::DEFAULT_PASSWORD_HASH)
    {
        $this->defaultHashType = $hashType;
    }

    public function getSupportedHashTypes(): array
    {
        return User::SUPPORTED_PASSWORD_HASHES;
    }

    public function isHashTypeSupported(int $hashType): bool
    {
        return in_array($hashType, $this->getSupportedHashTypes(), true);
    }

    public function getDefaultHashType(): int
    {
        return $this->defaultHashType;
    }

    /**
     * @throws \Ibexa\Core\Repository\User\Exception\UnsupportedPasswordHashType
     */
    public function createPasswordHash(string $password, ?int $hashType = null): string
    {
        $hashType = $hashType ?? $this->defaultHashType;

        switch ($hashType) {
            case User::PASSWORD_HASH_BCRYPT:
                return password_hash($password, PASSWORD_BCRYPT);

            case User::PASSWORD_HASH_PHP_DEFAULT:
                return password_hash($password, PASSWORD_DEFAULT);

            default:
                throw new UnsupportedPasswordHashType($hashType);
        }
    }

    public function isValidPassword(string $plainPassword, string $passwordHash, ?int $hashType = null): bool
    {
        if ($hashType === User::PASSWORD_HASH_BCRYPT || $hashType === User::PASSWORD_HASH_PHP_DEFAULT) {
            // In case of bcrypt let php's password functionality do it's magic
            return password_verify($plainPassword, $passwordHash);
        }

        // Randomize login time to protect against timing attacks
        usleep(random_int(0, 30000));

        return $passwordHash === $this->createPasswordHash($plainPassword, $hashType);
    }
}

class_alias(PasswordHashService::class, 'eZ\Publish\Core\Repository\User\PasswordHashService');
