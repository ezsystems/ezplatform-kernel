<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\Repository\SiteAccessAware;

use Ibexa\Contracts\Core\Repository\Decorator\SettingServiceDecorator;

class SettingService extends SettingServiceDecorator
{
}

class_alias(SettingService::class, 'eZ\Publish\Core\Repository\SiteAccessAware\SettingService');
