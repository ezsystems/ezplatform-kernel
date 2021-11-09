<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Core\Persistence\Cache;

use Ibexa\Contracts\Core\Persistence\Bookmark\Bookmark;
use Ibexa\Contracts\Core\Persistence\Bookmark\CreateStruct;
use Ibexa\Contracts\Core\Persistence\Bookmark\Handler as SPIBookmarkHandler;
use Ibexa\Contracts\Core\Persistence\Content\Location;
use Ibexa\Contracts\Core\Persistence\Content\Location\Handler as SPILocationHandler;

/**
 * Test case for Persistence\Cache\BookmarkHandler.
 */
class BookmarkHandlerTest extends AbstractCacheHandlerTest
{
    public function getHandlerMethodName(): string
    {
        return 'bookmarkHandler';
    }

    public function getHandlerClassName(): string
    {
        return SPIBookmarkHandler::class;
    }

    public function providerForUnCachedMethods(): array
    {
        // string $method, array $arguments, array? $tagGeneratingArguments, array? $keyGeneratingArguments, array? tags, array? $tags, string? $key, mixed? $returnValue
        return [
            ['create', [new CreateStruct()], null, null, null, null, new Bookmark()],
            ['delete', [1], [['bookmark', [1], false]], null, ['b-1']],
            ['loadUserBookmarks', [3, 2, 1], null, null, null, null, []],
            ['countUserBookmarks', [3], null, null, null, null, 1],
            ['locationSwapped', [1, 2], null, null, null, null],
        ];
    }

    public function providerForCachedLoadMethodsHit(): array
    {
        $bookmark = new Bookmark([
            'id' => 1,
            'locationId' => 43,
            'userId' => 3,
        ]);

        $calls = [['locationHandler', SPILocationHandler::class, 'load', new Location(['pathString' => '/1/2/43/'])]];

        // string $method, array $arguments, string $key, array? $tagGeneratingArguments, array? $tagGeneratingResults, array? $keyGeneratingArguments, array? $keyGeneratingResults, mixed? $data
        return [
            [
                'loadByUserIdAndLocationId',
                [3, [43]],
                'ibx-b-3-43',
                null,
                null,
                [['bookmark', [3], true]],
                ['ibx-b-3'],
                [43 => $bookmark],
                true,
                $calls,
            ],
        ];
    }

    public function providerForCachedLoadMethodsMiss(): array
    {
        $bookmark = new Bookmark([
            'id' => 1,
            'locationId' => 43,
            'userId' => 3,
        ]);

        $calls = [['locationHandler', SPILocationHandler::class, 'load', new Location(['pathString' => '/1/2/43/'])]];

        // string $method, array $arguments, string $key, array? $tagGeneratingArguments, array? $tagGeneratingResults, array? $keyGeneratingArguments, array? $keyGeneratingResults, mixed? $data
        return [
            [
                'loadByUserIdAndLocationId',
                [3, [43]],
                'ibx-b-3-43',
                [
                    ['bookmark', [1], false],
                    ['location', [43], false],
                    ['user', [3], false],
                    ['location_path', [2], false],
                    ['location_path', [43], false],
                ],
                ['b-1', 'l-43', 'u-3', 'lp-2', 'lp-43'],
                [
                    ['bookmark', [3], true],
                ],
                ['ibx-b-3'],
                [43 => $bookmark],
                true,
                $calls,
            ],
        ];
    }
}

class_alias(BookmarkHandlerTest::class, 'eZ\Publish\Core\Persistence\Cache\Tests\BookmarkHandlerTest');
