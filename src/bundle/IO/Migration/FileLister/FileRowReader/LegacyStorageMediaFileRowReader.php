<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Bundle\IO\Migration\FileLister\FileRowReader;

final class LegacyStorageMediaFileRowReader extends LegacyStorageFileRowReader
{
    /**
     * Returns the table name to store data in.
     *
     * @return string
     */
    protected function getStorageTable()
    {
        return 'ezmedia';
    }
}

class_alias(LegacyStorageMediaFileRowReader::class, 'eZ\Bundle\EzPublishIOBundle\Migration\FileLister\FileRowReader\LegacyStorageMediaFileRowReader');
