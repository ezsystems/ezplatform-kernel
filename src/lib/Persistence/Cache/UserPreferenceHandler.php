<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\Persistence\Cache;

use Ibexa\Contracts\Core\Persistence\UserPreference\Handler;
use Ibexa\Contracts\Core\Persistence\UserPreference\UserPreference;
use Ibexa\Contracts\Core\Persistence\UserPreference\UserPreferenceSetStruct;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException as APINotFoundException;
use Ibexa\Core\Base\Exceptions\NotFoundException;

class UserPreferenceHandler extends AbstractInMemoryPersistenceHandler implements Handler
{
    private const USER_PREFERENCE_IDENTIFIER = 'user_preference';
    private const USER_PREFERENCE_WITH_SUFFIX_IDENTIFIER = 'user_preference_with_suffix';

    /**
     * Constant used for storing not found results for getUserPreferenceByUserIdAndName().
     */
    private const NOT_FOUND = 'NotFoundException';

    /**
     * {@inheritdoc}
     */
    public function setUserPreference(UserPreferenceSetStruct $setStruct): UserPreference
    {
        $this->logger->logCall(__METHOD__, [
            'setStruct' => $setStruct,
        ]);

        $this->cache->deleteItems([
            $this->cacheIdentifierGenerator->generateKey(
                self::USER_PREFERENCE_WITH_SUFFIX_IDENTIFIER,
                [$setStruct->userId, $setStruct->name],
                true
            ),
        ]);

        return $this->persistenceHandler->userPreferenceHandler()->setUserPreference($setStruct);
    }

    /**
     * {@inheritdoc}
     */
    public function countUserPreferences(int $userId): int
    {
        $this->logger->logCall(__METHOD__, [
            'userId' => $userId,
        ]);

        return $this->persistenceHandler->userPreferenceHandler()->countUserPreferences($userId);
    }

    /**
     * {@inheritdoc}
     *
     * Needs to store NotFoundExceptions as UserPreference feature heavily uses this in valid lookups.
     */
    public function getUserPreferenceByUserIdAndName(int $userId, string $name): UserPreference
    {
        $userPreference = $this->getCacheValue(
            $userId,
            $this->cacheIdentifierGenerator->generateKey(self::USER_PREFERENCE_IDENTIFIER, [], true) . '-',
            function ($userId) use ($name) {
                try {
                    return $this->persistenceHandler->userPreferenceHandler()->getUserPreferenceByUserIdAndName(
                        $userId,
                        $name
                    );
                } catch (APINotFoundException $e) {
                    return self::NOT_FOUND;
                }
            },
            static function () {
                return [];
            },
            function () use ($userId, $name) {
                return [
                    $this->cacheIdentifierGenerator->generateKey(
                        self::USER_PREFERENCE_WITH_SUFFIX_IDENTIFIER,
                        [$userId, $name],
                        true
                    ),
                ];
            },
            '-' . $name
        );

        if ($userPreference === self::NOT_FOUND) {
            throw new NotFoundException('User Preference', $userId . ',' . $name);
        }

        return $userPreference;
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserPreferences(int $userId, int $offset, int $limit): array
    {
        $this->logger->logCall(__METHOD__, [
            'userId' => $userId,
            'offset' => $offset,
            'limit' => $limit,
        ]);

        return $this->persistenceHandler->userPreferenceHandler()->loadUserPreferences($userId, $offset, $limit);
    }
}

class_alias(UserPreferenceHandler::class, 'eZ\Publish\Core\Persistence\Cache\UserPreferenceHandler');
