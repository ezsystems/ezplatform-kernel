<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace eZ\Publish\Core\Repository;

use eZ\Publish\API\Repository\SettingService as SettingServiceInterface;
use eZ\Publish\API\Repository\Values\Setting\Setting;
use eZ\Publish\API\Repository\Values\Setting\SettingCreateStruct;
use eZ\Publish\API\Repository\Values\Setting\SettingUpdateStruct;

final class SettingService implements SettingServiceInterface
{
    public function loadSetting(string $group, string $identifier): Setting
    {
        $setting = $this->buildDomainSettingObject(
            $this->settingHandler->load($group, $identifier)
        );

        return $setting;
    }

    public function updateSetting(Setting $setting, SettingUpdateStruct $settingUpdateStruct): Setting
    {
        // TODO: Implement updateSetting() method.
    }

    public function createSetting(SettingCreateStruct $settingCreateStruct): Setting
    {
        // TODO: Implement createSetting() method.
    }

    public function deleteSetting(Setting $setting): void
    {
        // TODO: Implement deleteSetting() method.
    }

    public function newSettingCreateStruct(): SettingCreateStruct
    {
        // TODO: Implement newSettingCreateStruct() method.
    }

    public function newSettingUpdateStruct(): SettingUpdateStruct
    {
        // TODO: Implement newSettingUpdateStruct() method.
    }

    private function buildDomainSettingObject(): Setting
    {

    }
}
