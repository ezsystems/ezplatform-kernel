<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\FieldType;

use eZ\Publish\SPI\FieldType\FieldStorage;
use eZ\Publish\SPI\Persistence\Content\Field;
use eZ\Publish\SPI\Persistence\Content\VersionInfo;

interface DefaultDataFieldStorage extends FieldStorage
{
    /**
     * Populates <code>$field</code> value property with default data based on the external data.
     *
     * <code>$field->value</code> is a {@see \eZ\Publish\SPI\Persistence\Content\FieldValue} object.
     * This value holds the data as a {@see \eZ\Publish\Core\FieldType\Value} based object, according to
     * the field type (e.g. for <code>TextLine</code>, it will be a {@see \eZ\Publish\Core\FieldType\TextLine\Value} object).
     */
    public function getDefaultFieldData(VersionInfo $versionInfo, Field $field): void;
}
