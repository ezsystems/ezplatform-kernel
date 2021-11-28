<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Events\Setting;

use Ibexa\Contracts\Core\Repository\Event\AfterEvent;
use Ibexa\Contracts\Core\Repository\Values\Setting\Setting;
use Ibexa\Contracts\Core\Repository\Values\Setting\SettingCreateStruct;

final class CreateSettingEvent extends AfterEvent
{
    /** @var \Ibexa\Contracts\Core\Repository\Values\Setting\Setting */
    private $setting;

    /** @var \Ibexa\Contracts\Core\Repository\Values\Setting\SettingCreateStruct */
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

class_alias(CreateSettingEvent::class, 'eZ\Publish\API\Repository\Events\Setting\CreateSettingEvent');
