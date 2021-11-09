<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository;

use Ibexa\Contracts\Core\Repository\Values\Setting\Setting;
use Ibexa\Contracts\Core\Repository\Values\Setting\SettingCreateStruct;
use Ibexa\Contracts\Core\Repository\Values\Setting\SettingUpdateStruct;

interface SettingService
{
    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException If a setting with the given group and identifier could not be found
     */
    public function loadSetting(string $group, string $identifier): Setting;

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException If the current user is not allowed to update a setting
     */
    public function updateSetting(Setting $setting, SettingUpdateStruct $settingUpdateStruct): Setting;

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException If a setting with the given group and identifier already exists
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException If the current user is not allowed to create a setting
     */
    public function createSetting(SettingCreateStruct $settingCreateStruct): Setting;

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException If the setting has already been removed
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException If the current user is not allowed to remove a setting
     */
    public function deleteSetting(Setting $setting): void;

    public function newSettingCreateStruct(array $properties = []): SettingCreateStruct;

    public function newSettingUpdateStruct(array $properties = []): SettingUpdateStruct;
}

class_alias(SettingService::class, 'eZ\Publish\API\Repository\SettingService');
