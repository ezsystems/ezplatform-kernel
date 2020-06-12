<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace eZ\Publish\Core\Repository\User;

use eZ\Publish\API\Repository\Values\User\User;
use eZ\Publish\Core\Base\Exceptions\InvalidArgumentException;

/**
 * @internal
 */
final class PasswordHashService implements PasswordHashServiceInterface
{
    /** @var int */
    private $hashType;

    public function __construct(int $hashType = User::DEFAULT_PASSWORD_HASH)
    {
        $this->hashType = $hashType;
    }

    public function getDefaultHashType(): int
    {
        return $this->hashType;
    }

    public function createPasswordHash(string $password, ?int $hashType = null): string
    {
        $hashType = $hashType ?? $this->hashType;

        switch ($hashType) {
            case User::PASSWORD_HASH_BCRYPT:
                return password_hash($password, PASSWORD_BCRYPT);

            case User::PASSWORD_HASH_PHP_DEFAULT:
                return password_hash($password, PASSWORD_DEFAULT);

            case User::PASSWORD_HASH_ARGON2I:
                if (!defined('PASSWORD_ARGON2I')) {
                    throw new InvalidArgumentException('hashType', "Password hash algorithm 'PASSWORD_ARGON2I' is not compiled into PHP");
                }

                return password_hash($password, PASSWORD_ARGON2I);

            case User::PASSWORD_HASH_ARGON2ID:
                if (!defined('PASSWORD_ARGON2ID')) {
                    throw new InvalidArgumentException('hashType', "Password hash algorithm 'PASSWORD_ARGON2ID' is not compiled into PHP");
                }

                return password_hash($password, PASSWORD_ARGON2ID);

            default:
                throw new InvalidArgumentException('hashType', "Password hash type '$hashType' is not recognized");
        }
    }

    public function isValidPassword(string $plainPassword, string $passwordHash, ?int $hashType = null): bool
    {
        if (in_array($hashType, User::PHP_PASSWORD_HASH_ALGORITHMS, true)) {
            // Let php's password functionality do it's magic
            return password_verify($plainPassword, $passwordHash);
        }

        // Randomize login time to protect against timing attacks
        usleep(random_int(0, 30000));

        return $passwordHash === $this->createPasswordHash($plainPassword, $hashType);
    }
}
