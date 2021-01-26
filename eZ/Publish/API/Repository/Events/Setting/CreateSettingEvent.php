<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace eZ\Publish\API\Repository\Events\Setting;

use eZ\Publish\API\Repository\Values\Setting\Setting;
use eZ\Publish\API\Repository\Values\Setting\SettingCreateStruct;
use eZ\Publish\SPI\Repository\Event\AfterEvent;

final class CreateSettingEvent extends AfterEvent
{
    /** @var \eZ\Publish\API\Repository\Values\Setting\Setting */
    private $setting;

    /** @var \eZ\Publish\API\Repository\Values\Setting\SettingCreateStruct */
    private $settingCreateStruct;

    public function __construct(
        Setting $setting,
        SettingCreateStruct $settingCreateStruct
    ) {
        $this->setting = $setting;
        $this->settingCreateStruct = $settingCreateStruct;
    }

    public function getSetting(): Setting
    {
        return $this->setting;
    }

    public function getSettingCreateStruct(): SettingCreateStruct
    {
        return $this->settingCreateStruct;
    }
}
