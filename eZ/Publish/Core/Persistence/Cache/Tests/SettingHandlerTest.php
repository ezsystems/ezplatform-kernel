<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace eZ\Publish\Core\Persistence\Cache\Tests;

use eZ\Publish\SPI\Persistence\Setting\Setting;
use eZ\Publish\SPI\Persistence\Setting\Handler as SettingHandler;

/**
 * Test case for Persistence\Cache\SettingHandler.
 */
class SettingHandlerTest extends AbstractCacheHandlerTest
{
    public function getHandlerMethodName(): string
    {
        return 'settingHandler';
    }

    public function getHandlerClassName(): string
    {
        return SettingHandler::class;
    }

    public function providerForUnCachedMethods(): array
    {
        // string $method, array $arguments, array? $tags, string? $key
        return [
            ['create', ['group_a1', 'identifier_b2', 'value_c3']],
            ['update', ['group_a1', 'identifier_b2', 'update_value_c3'], ['ibexa-setting-group_a1-identifier_b2']],
            ['delete', ['group_a1', 'identifier_b2'], ['ibexa-setting-group_a1-identifier_b2']],
        ];
    }

    public function providerForCachedLoadMethods(): array
    {
        $object = new Setting(['group' => 'group_a1', 'identifier' => 'identifier_b2']);

        // string $method, array $arguments, string $key, mixed? $data
        return [
            ['load', ['group_a1', 'identifier_b2'], 'ibexa-setting-group_a1-identifier_b2', $object],
        ];
    }
}
