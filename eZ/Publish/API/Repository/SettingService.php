<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace eZ\Publish\API\Repository;

use eZ\Publish\API\Repository\Values\Setting\Setting;
use eZ\Publish\API\Repository\Values\Setting\SettingCreateStruct;
use eZ\Publish\API\Repository\Values\Setting\SettingUpdateStruct;

interface SettingService
{
    public function loadSetting(string $group, string $identifier): Setting;

    public function updateSetting(Setting $setting, SettingUpdateStruct $settingUpdateStruct): Setting;

    public function createSetting(SettingCreateStruct $settingCreateStruct): Setting;

    public function deleteSetting(Setting $setting): void;

    public function newSettingCreateStruct(array $properties = []): SettingCreateStruct;

    public function newSettingUpdateStruct(array $properties = []): SettingUpdateStruct;
}
