<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\Repository;

use Exception;
use Ibexa\Contracts\Core\Persistence\UserPreference\Handler as UserPreferenceHandler;
use Ibexa\Contracts\Core\Persistence\UserPreference\UserPreference;
use Ibexa\Contracts\Core\Persistence\UserPreference\UserPreferenceSetStruct;
use Ibexa\Contracts\Core\Repository\Repository as RepositoryInterface;
use Ibexa\Contracts\Core\Repository\UserPreferenceService as UserPreferenceServiceInterface;
use Ibexa\Contracts\Core\Repository\Values\UserPreference\UserPreference as APIUserPreference;
use Ibexa\Contracts\Core\Repository\Values\UserPreference\UserPreferenceList;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;

class UserPreferenceService implements UserPreferenceServiceInterface
{
    /** @var \Ibexa\Contracts\Core\Repository\Repository */
    private $repository;

    /** @var \Ibexa\Contracts\Core\Persistence\UserPreference\Handler */
    private $userPreferenceHandler;

    /**
     * @param \Ibexa\Contracts\Core\Repository\Repository $repository
     * @param \Ibexa\Contracts\Core\Persistence\UserPreference\Handler $userPreferenceHandler
     */
    public function __construct(RepositoryInterface $repository, UserPreferenceHandler $userPreferenceHandler)
    {
        $this->repository = $repository;
        $this->userPreferenceHandler = $userPreferenceHandler;
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserPreferences(int $offset = 0, int $limit = 25): UserPreferenceList
    {
        $currentUserId = $this->getCurrentUserId();

        $list = new UserPreferenceList();

        $list->totalCount = $this->userPreferenceHandler->countUserPreferences($currentUserId);
        if ($list->totalCount > 0) {
            $list->items = array_map(function (UserPreference $spiUserPreference) {
                return $this->buildDomainObject($spiUserPreference);
            }, $this->userPreferenceHandler->loadUserPreferences($currentUserId, $offset, $limit));
        }

        return $list;
    }

    /**
     * {@inheritdoc}
     */
    public function setUserPreference(array $userPreferenceSetStructs): void
    {
        $spiSetStructs = [];
        foreach ($userPreferenceSetStructs as $key => $userPreferenceSetStruct) {
            if (empty($userPreferenceSetStruct->name)) {
                throw new InvalidArgumentException('name', $userPreferenceSetStruct->name . ' at index ' . $key);
            }

            $value = $userPreferenceSetStruct->value;

            if (is_object($value) && !method_exists($value, '__toString')) {
                throw new InvalidArgumentException('value', 'Cannot convert value to string at index ' . $key);
            }

            try {
                $value = (string)$userPreferenceSetStruct->value;
            } catch (\Exception $exception) {
                throw new InvalidArgumentException('value', 'Cannot convert value to string at index ' . $key);
            }

            $spiSetStruct = new UserPreferenceSetStruct();
            $spiSetStruct->userId = $this->getCurrentUserId();
            $spiSetStruct->name = $userPreferenceSetStruct->name;
            $spiSetStruct->value = $value;

            $spiSetStructs[] = $spiSetStruct;
        }

        $this->repository->beginTransaction();
        try {
            foreach ($spiSetStructs as $spiSetStruct) {
                $this->userPreferenceHandler->setUserPreference($spiSetStruct);
            }
            $this->repository->commit();
        } catch (Exception $e) {
            $this->repository->rollback();
            throw $e;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getUserPreference(string $userPreferenceName): APIUserPreference
    {
        $currentUserId = $this->getCurrentUserId();

        $userPreference = $this->userPreferenceHandler->getUserPreferenceByUserIdAndName(
            $currentUserId,
            $userPreferenceName
        );

        return $this->buildDomainObject($userPreference);
    }

    /**
     * {@inheritdoc}
     */
    public function getUserPreferenceCount(): int
    {
        return $this->userPreferenceHandler->countUserPreferences(
            $this->getCurrentUserId()
        );
    }

    /**
     * Builds UserPreference domain object from ValueObject returned by Persistence API.
     *
     * @param \Ibexa\Contracts\Core\Persistence\UserPreference\UserPreference $spiUserPreference
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\UserPreference\UserPreference
     */
    protected function buildDomainObject(UserPreference $spiUserPreference): APIUserPreference
    {
        return new APIUserPreference([
            'name' => $spiUserPreference->name,
            'value' => $spiUserPreference->value,
        ]);
    }

    private function getCurrentUserId(): int
    {
        return $this->repository
            ->getPermissionResolver()
            ->getCurrentUserReference()
            ->getUserId();
    }
}

class_alias(UserPreferenceService::class, 'eZ\Publish\Core\Repository\UserPreferenceService');
