<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Core\Persistence\Cache;

use Ibexa\Contracts\Core\Persistence\UserPreference\Handler as SPIUserPreferenceHandler;
use Ibexa\Contracts\Core\Persistence\UserPreference\UserPreference as SPIUserPreference;
use Ibexa\Contracts\Core\Persistence\UserPreference\UserPreferenceSetStruct;

/**
 * Test case for Persistence\Cache\UserPreferenceHandler.
 */
class UserPreferenceHandlerTest extends AbstractInMemoryCacheHandlerTest
{
    /**
     * {@inheritdoc}
     */
    public function getHandlerMethodName(): string
    {
        return 'userPreferenceHandler';
    }

    /**
     * {@inheritdoc}
     */
    public function getHandlerClassName(): string
    {
        return SPIUserPreferenceHandler::class;
    }

    /**
     * {@inheritdoc}
     */
    public function providerForUnCachedMethods(): array
    {
        $userId = 7;
        $name = 'setting';
        $userPreferenceCount = 10;

        // string $method, array $arguments, array? $tagGeneratingArguments, array? $keyGeneratingArguments, array? $tags, array? $key, ?mixed $returnValue
        return [
            [
                'setUserPreference',
                [
                    new UserPreferenceSetStruct([
                        'userId' => $userId,
                        'name' => $name,
                    ]),
                ],
                null,
                [
                    ['user_preference_with_suffix', [$userId, $name], true],
                ],
                null,
                [
                    'ibx-up-' . $userId . '-' . $name,
                ],
                new SPIUserPreference(),
            ],
            [
                'loadUserPreferences', [$userId, 0, 25], null, null, null, null, [],
            ],
            [
                'countUserPreferences',
                [
                    $userId,
                ],
                null,
                null,
                null,
                null,
                $userPreferenceCount,
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function providerForCachedLoadMethodsHit(): array
    {
        $userId = 7;
        $name = 'setting';

        // string $method, array $arguments, string $key, array? $tagGeneratingArguments, array? $tagGeneratingResults, array? $keyGeneratingArguments, array? $keyGeneratingResults, mixed? $data, bool $multi
        return [
            [
                'getUserPreferenceByUserIdAndName',
                [
                    $userId,
                    $name,
                ],
                'ibx-up-' . $userId . '-' . $name,
                null,
                null,
                [['user_preference', [], true]],
                ['ibx-up'],
                new SPIUserPreference(['userId' => $userId, 'name' => $name]),
            ],
        ];
    }

    public function providerForCachedLoadMethodsMiss(): array
    {
        $userId = 7;
        $name = 'setting';

        // string $method, array $arguments, string $key, array? $tagGeneratingArguments, array? $tagGeneratingResults, array? $keyGeneratingArguments, array? $keyGeneratingResults, mixed? $data, bool $multi
        return [
            [
                'getUserPreferenceByUserIdAndName',
                [
                    $userId,
                    $name,
                ],
                'ibx-up-' . $userId . '-' . $name,
                null,
                null,
                [['user_preference', [], true]],
                ['ibx-up'],
                new SPIUserPreference(['userId' => $userId, 'name' => $name]),
            ],
        ];
    }
}

class_alias(UserPreferenceHandlerTest::class, 'eZ\Publish\Core\Persistence\Cache\Tests\UserPreferenceHandlerTest');
