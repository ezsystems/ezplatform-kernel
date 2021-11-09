<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\Persistence\Legacy\UserPreference;

use Ibexa\Contracts\Core\Persistence\UserPreference\UserPreferenceSetStruct;

abstract class Gateway
{
    /**
     * Store UserPreference ValueObject in persistent storage.
     *
     * @param \Ibexa\Contracts\Core\Persistence\UserPreference\UserPreferenceSetStruct $userPreference
     *
     * @return int
     */
    abstract public function setUserPreference(UserPreferenceSetStruct $userPreference): int;

    /**
     * Get UserPreference by its user ID and name.
     *
     * @param int $userId
     * @param string $name
     *
     * @return array
     */
    abstract public function getUserPreferenceByUserIdAndName(int $userId, string $name): array;

    /**
     * @param int $userId
     *
     * @return int
     */
    abstract public function countUserPreferences(int $userId): int;

    /**
     * @param int $userId
     * @param int $offset
     * @param int $limit
     *
     * @return array
     */
    abstract public function loadUserPreferences(int $userId, int $offset = 0, int $limit = -1): array;
}

class_alias(Gateway::class, 'eZ\Publish\Core\Persistence\Legacy\UserPreference\Gateway');
