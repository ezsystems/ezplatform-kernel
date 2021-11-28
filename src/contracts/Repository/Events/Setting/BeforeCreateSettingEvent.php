<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Events\Setting;

use Ibexa\Contracts\Core\Repository\Event\BeforeEvent;
use Ibexa\Contracts\Core\Repository\Values\Setting\Setting;
use Ibexa\Contracts\Core\Repository\Values\Setting\SettingCreateStruct;
use UnexpectedValueException;

final class BeforeCreateSettingEvent extends BeforeEvent
{
    /** @var \Ibexa\Contracts\Core\Repository\Values\Setting\SettingCreateStruct */
    private $settingCreateStruct;

    /** @var \Ibexa\Contracts\Core\Repository\Values\Setting\Setting|null */
    private $setting;

    public function __construct(SettingCreateStruct $settingCreateStruct)
    {
        $this->settingCreateStruct = $settingCreateStruct;
    }

    public function getSettingCreateStruct(): SettingCreateStruct
    {
        return $this->settingCreateStruct;
    }

    public function getSetting(): Setting
    {
        if (!$this->hasSetting()) {
            throw new UnexpectedValueException(sprintf('Return value is not set or not of type %s. Check hasSetting() or set it using setSetting() before you call the getter.', Setting::class));
        }

        return $this->setting;
    }

    public function setSetting(?Setting $setting): void
    {
        $this->setting = $setting;
    }

    public function hasSetting(): bool
    {
        return $this->setting instanceof Setting;
    }
}

class_alias(BeforeCreateSettingEvent::class, 'eZ\Publish\API\Repository\Events\Setting\BeforeCreateSettingEvent');
