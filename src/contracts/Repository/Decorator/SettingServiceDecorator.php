<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace eZ\Publish\SPI\Repository\Decorator;

use eZ\Publish\API\Repository\SettingService;
use eZ\Publish\API\Repository\Values\Setting\Setting;
use eZ\Publish\API\Repository\Values\Setting\SettingCreateStruct;
use eZ\Publish\API\Repository\Values\Setting\SettingUpdateStruct;

abstract class SettingServiceDecorator implements SettingService
{
    /** @var \eZ\Publish\API\Repository\SettingService */
    protected $innerService;

    public function __construct(
        SettingService $innerService
    ) {
        $this->innerService = $innerService;
    }

    public function loadSetting(string $group, string $identifier): Setting
    {
        return $this->innerService->loadSetting($group, $identifier);
    }

    public function updateSetting(Setting $setting, SettingUpdateStruct $settingUpdateStruct): Setting
    {
        return $this->innerService->updateSetting($setting, $settingUpdateStruct);
    }

    public function createSetting(SettingCreateStruct $settingCreateStruct): Setting
    {
        return $this->innerService->createSetting($settingCreateStruct);
    }

    public function deleteSetting(Setting $setting): void
    {
        $this->innerService->deleteSetting($setting);
    }

    public function newSettingCreateStruct(array $properties = []): SettingCreateStruct
    {
        return $this->innerService->newSettingCreateStruct($properties);
    }

    public function newSettingUpdateStruct(array $properties = []): SettingUpdateStruct
    {
        return $this->innerService->newSettingUpdateStruct($properties);
    }
}
