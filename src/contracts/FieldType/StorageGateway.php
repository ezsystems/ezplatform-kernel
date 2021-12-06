<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Contracts\Core\FieldType;

/**
 * Base class for FieldType external storage gateways.
 */
abstract class StorageGateway
{
    /**
     * Get sequence name bound to database table and column.
     *
     * @param string $table
     * @param string $column
     *
     * @return string
     */
    protected function getSequenceName($table, $column)
    {
        return sprintf('%s_%s_seq', $table, $column);
    }
}

class_alias(StorageGateway::class, 'eZ\Publish\SPI\FieldType\StorageGateway');
