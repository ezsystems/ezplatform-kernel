<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\SiteAccess;

/**
 * @internal
 */
interface ConfigProcessor
{
    public function processComplexSetting(string $setting): string;

    public function processSettingValue(string $value): string;
}

class_alias(ConfigProcessor::class, 'eZ\Publish\SPI\SiteAccess\ConfigProcessor');
