<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Events\Setting;

use Ibexa\Contracts\Core\Repository\Event\AfterEvent;
use Ibexa\Contracts\Core\Repository\Values\Setting\Setting;

final class DeleteSettingEvent extends AfterEvent
{
    /** @var \Ibexa\Contracts\Core\Repository\Values\Setting\Setting */
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

class_alias(DeleteSettingEvent::class, 'eZ\Publish\API\Repository\Events\Setting\DeleteSettingEvent');
