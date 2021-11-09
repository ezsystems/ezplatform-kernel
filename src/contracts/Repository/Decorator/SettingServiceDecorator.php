<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Decorator;

use Ibexa\Contracts\Core\Repository\SettingService;
use Ibexa\Contracts\Core\Repository\Values\Setting\Setting;
use Ibexa\Contracts\Core\Repository\Values\Setting\SettingCreateStruct;
use Ibexa\Contracts\Core\Repository\Values\Setting\SettingUpdateStruct;

abstract class SettingServiceDecorator implements SettingService
{
    /** @var \Ibexa\Contracts\Core\Repository\SettingService */
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

class_alias(SettingServiceDecorator::class, 'eZ\Publish\SPI\Repository\Decorator\SettingServiceDecorator');
