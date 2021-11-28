<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Core\Persistence\Cache;

use Ibexa\Contracts\Core\Persistence\Setting\Handler as SettingHandler;
use Ibexa\Contracts\Core\Persistence\Setting\Setting;

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
        // string $method, array $arguments, array? $tagGeneratingArguments, array? $keyGeneratingArguments, array? $tags, array? $key, ?mixed $returnValue
        return [
            [
                'create',
                ['group_a1', 'identifier_b2', 'value_c3'],
                null,
                null,
                null,
                null,
                new Setting(),
            ],
            [
                'update',
                ['group_a1', 'identifier_b2', 'update_value_c3'],
                [['setting', ['group_a1', 'identifier_b2'], true]],
                null,
                ['ibx-set-group_a1-identifier_b2'],
                null,
                new Setting(),
            ],
            [
                'delete',
                ['group_a1', 'identifier_b2'],
                [['setting', ['group_a1', 'identifier_b2'], true]],
                null,
                ['ibx-set-group_a1-identifier_b2'],
            ],
        ];
    }

    public function providerForCachedLoadMethodsHit(): array
    {
        $object = new Setting(['group' => 'group_a1', 'identifier' => 'identifier_b2']);

        // string $method, array $arguments, string $key, array? $tagGeneratingArguments, array? $tagGeneratingResults, array? $keyGeneratingArguments, array? $keyGeneratingResults, mixed? $data, bool $multi
        return [
            [
                'load',
                ['group_a1', 'identifier_b2'],
                'ibx-set-group_a1-identifier_b2',
                [['setting', ['group_a1', 'identifier_b2'], true]],
                ['ibx-set-group_a1-identifier_b2'],
                null,
                null,
                $object,
            ],
        ];
    }

    public function providerForCachedLoadMethodsMiss(): array
    {
        $object = new Setting(['group' => 'group_a1', 'identifier' => 'identifier_b2']);

        // string $method, array $arguments, string $key, array? $tagGeneratingArguments, array? $tagGeneratingResults, array? $keyGeneratingArguments, array? $keyGeneratingResults, mixed? $data, bool $multi
        return [
            [
                'load',
                ['group_a1', 'identifier_b2'],
                'ibx-set-group_a1-identifier_b2',
                [
                    ['setting', ['group_a1', 'identifier_b2'], true],
                    ['setting', ['group_a1', 'identifier_b2'], true],
                ],
                ['ibx-set-group_a1-identifier_b2', 'ibx-set-group_a1-identifier_b2'],
                null,
                null,
                $object,
            ],
        ];
    }
}

class_alias(SettingHandlerTest::class, 'eZ\Publish\Core\Persistence\Cache\Tests\SettingHandlerTest');
