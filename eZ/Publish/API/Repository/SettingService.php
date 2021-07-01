<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace eZ\Publish\API\Repository;

use eZ\Publish\API\Repository\Values\Setting\Setting;
use eZ\Publish\API\Repository\Values\Setting\SettingCreateStruct;
use eZ\Publish\API\Repository\Values\Setting\SettingUpdateStruct;

interface SettingService
{
    /**
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException If setting with the given group and identifier could not be found
     */
    public function loadSetting(string $group, string $identifier): Setting;

    /**
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException If the current user user is not allowed to update a setting
     */
    public function updateSetting(Setting $setting, SettingUpdateStruct $settingUpdateStruct): Setting;

    /**
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException If setting with the given group and identifier already exists
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException If the current user user is not allowed to create a setting
     */
    public function createSetting(SettingCreateStruct $settingCreateStruct): Setting;

    /**
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException If setting has already been removed
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException If the current user user is not allowed to remove a setting
     */
    public function deleteSetting(Setting $setting): void;

    public function newSettingCreateStruct(array $properties = []): SettingCreateStruct;

    public function newSettingUpdateStruct(array $properties = []): SettingUpdateStruct;
}
