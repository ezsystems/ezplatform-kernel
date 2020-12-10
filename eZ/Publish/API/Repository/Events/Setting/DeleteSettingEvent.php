<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace eZ\Publish\API\Repository\Events\Setting;

use eZ\Publish\API\Repository\Values\Setting\Setting;
use eZ\Publish\SPI\Repository\Event\AfterEvent;

final class DeleteSettingEvent extends AfterEvent
{
    /** @var \eZ\Publish\API\Repository\Values\Setting\Setting */
    private $setting;

    public function __construct(Setting $setting)
    {
        $this->setting = $setting;
    }

    public function getSetting(): Setting
    {
        return $this->setting;
    }
}
