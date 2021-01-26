<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace eZ\Publish\API\Repository\Events\Setting;

use eZ\Publish\API\Repository\Values\Setting\Setting;
use eZ\Publish\API\Repository\Values\Setting\SettingUpdateStruct;
use eZ\Publish\SPI\Repository\Event\BeforeEvent;
use UnexpectedValueException;

final class BeforeUpdateSettingEvent extends BeforeEvent
{
    /** @var \eZ\Publish\API\Repository\Values\Setting\Setting */
    private $setting;

    /** @var \eZ\Publish\API\Repository\Values\Setting\SettingUpdateStruct */
    private $settingUpdateStruct;

    /** @var \eZ\Publish\API\Repository\Values\Setting\Setting|null */
    private $updatedSetting;

    public function __construct(Setting $setting, SettingUpdateStruct $settingUpdateStruct)
    {
        $this->setting = $setting;
        $this->settingUpdateStruct = $settingUpdateStruct;
    }

    public function getSetting(): Setting
    {
        return $this->setting;
    }

    public function getSettingUpdateStruct(): SettingUpdateStruct
    {
        return $this->settingUpdateStruct;
    }

    public function getUpdatedSetting(): Setting
    {
        if (!$this->hasUpdatedSetting()) {
            throw new UnexpectedValueException(sprintf('Return value is not set or not of type %s. Check hasUpdatedSetting() or set it using setUpdatedSetting() before you call the getter.', Setting::class));
        }

        return $this->updatedSetting;
    }

    public function setUpdatedSetting(?Setting $updatedSetting): void
    {
        $this->updatedSetting = $updatedSetting;
    }

    public function hasUpdatedSetting(): bool
    {
        return $this->updatedSetting instanceof Setting;
    }
}
