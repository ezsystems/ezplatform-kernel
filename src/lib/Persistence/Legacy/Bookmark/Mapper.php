<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\Persistence\Legacy\Bookmark;

use Ibexa\Contracts\Core\Persistence\Bookmark\Bookmark;
use Ibexa\Contracts\Core\Persistence\Bookmark\CreateStruct;

/**
 * Bookmark mapper.
 */
class Mapper
{
    /**
     * Creates a Bookmark from $createStruct.
     *
     * @param \Ibexa\Contracts\Core\Persistence\Bookmark\CreateStruct $createStruct
     *
     * @return \Ibexa\Contracts\Core\Persistence\Bookmark\Bookmark
     */
    public function createBookmarkFromCreateStruct(CreateStruct $createStruct): Bookmark
    {
        $bookmark = new Bookmark();
        $bookmark->name = $createStruct->name;
        $bookmark->locationId = $createStruct->locationId;
        $bookmark->userId = $createStruct->userId;

        return $bookmark;
    }

    /**
     * Extracts Bookmark objects from $rows.
     *
     * @param array $rows
     *
     * @return \Ibexa\Contracts\Core\Persistence\Bookmark\Bookmark[]
     */
    public function extractBookmarksFromRows(array $rows): array
    {
        $bookmarks = [];
        foreach ($rows as $row) {
            $bookmarks[] = $this->extractBookmarkFromRow($row);
        }

        return $bookmarks;
    }

    /**
     * Extract Bookmark object from $row.
     *
     * @param array $row
     *
     * @return \Ibexa\Contracts\Core\Persistence\Bookmark\Bookmark
     */
    private function extractBookmarkFromRow(array $row): Bookmark
    {
        $bookmark = new Bookmark();
        $bookmark->id = (int)$row['id'];
        $bookmark->name = $row['name'];
        $bookmark->userId = (int)$row['user_id'];
        $bookmark->locationId = (int)$row['node_id'];

        return $bookmark;
    }
}

class_alias(Mapper::class, 'eZ\Publish\Core\Persistence\Legacy\Bookmark\Mapper');
