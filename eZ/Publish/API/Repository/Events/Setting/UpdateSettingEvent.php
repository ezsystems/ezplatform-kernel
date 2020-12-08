<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace eZ\Publish\API\Repository\Events\Setting;

use eZ\Publish\API\Repository\Values\Setting\Setting;
use eZ\Publish\API\Repository\Values\Setting\SettingUpdateStruct;
use eZ\Publish\SPI\Repository\Event\BeforeEvent;

final class UpdateSettingEvent extends BeforeEvent
{
    /** @var \eZ\Publish\API\Repository\Values\Setting\Setting */
    private $updatedSetting;

    /** @var \eZ\Publish\API\Repository\Values\Setting\Setting */
    private $setting;

    /** @var \eZ\Publish\API\Repository\Values\Setting\SettingUpdateStruct */
    private $settingUpdateStruct;

    public function __construct(
        Setting $updatedSetting,
        Setting $setting,
        SettingUpdateStruct $settingUpdateStruct
    ) {
        $this->updatedSetting = $updatedSetting;
        $this->setting = $setting;
        $this->settingUpdateStruct = $settingUpdateStruct;
    }

    public function getUpdatedSetting(): Setting
    {
        return $this->updatedSetting;
    }

    public function getSetting(): Setting
    {
        return $this->setting;
    }

    public function getSettingUpdateStruct(): SettingUpdateStruct
    {
        return $this->settingUpdateStruct;
    }
}
