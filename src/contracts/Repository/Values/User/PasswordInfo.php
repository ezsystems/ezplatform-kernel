<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Values\User;

use DateTime;
use DateTimeImmutable;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;

final class PasswordInfo extends ValueObject
{
    /** @var \DateTimeImmutable|null */
    private $expirationDate;

    /** @var \DateTimeImmutable|null */
    private $expirationWarningDate;

    public function __construct(?DateTimeImmutable $expirationDate = null, ?DateTimeImmutable $expirationWarningDate = null)
    {
        $this->expirationDate = $expirationDate;
        $this->expirationWarningDate = $expirationWarningDate;
    }

    public function isPasswordExpired(): bool
    {
        if ($this->expirationDate === null) {
            return false;
        }

        return $this->expirationDate < new DateTime();
    }

    public function hasExpirationDate(): bool
    {
        return $this->expirationDate !== null;
    }

    public function getExpirationDate(): ?DateTimeImmutable
    {
        return $this->expirationDate;
    }

    public function hasExpirationWarningDate(): bool
    {
        return $this->expirationWarningDate !== null;
    }

    public function getExpirationWarningDate(): ?DateTimeImmutable
    {
        return $this->expirationWarningDate;
    }
}

class_alias(PasswordInfo::class, 'eZ\Publish\API\Repository\Values\User\PasswordInfo');
