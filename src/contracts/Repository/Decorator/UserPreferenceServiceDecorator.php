<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Decorator;

use Ibexa\Contracts\Core\Repository\UserPreferenceService;
use Ibexa\Contracts\Core\Repository\Values\UserPreference\UserPreference;
use Ibexa\Contracts\Core\Repository\Values\UserPreference\UserPreferenceList;

abstract class UserPreferenceServiceDecorator implements UserPreferenceService
{
    /** @var \Ibexa\Contracts\Core\Repository\UserPreferenceService */
    protected $innerService;

    public function __construct(UserPreferenceService $innerService)
    {
        $this->innerService = $innerService;
    }

    public function setUserPreference(array $userPreferenceSetStructs): void
    {
        $this->innerService->setUserPreference($userPreferenceSetStructs);
    }

    public function getUserPreference(string $userPreferenceName): UserPreference
    {
        return $this->innerService->getUserPreference($userPreferenceName);
    }

    public function loadUserPreferences(
        int $offset = 0,
        int $limit = 25
    ): UserPreferenceList {
        return $this->innerService->loadUserPreferences($offset, $limit);
    }

    public function getUserPreferenceCount(): int
    {
        return $this->innerService->getUserPreferenceCount();
    }
}

class_alias(UserPreferenceServiceDecorator::class, 'eZ\Publish\SPI\Repository\Decorator\UserPreferenceServiceDecorator');
