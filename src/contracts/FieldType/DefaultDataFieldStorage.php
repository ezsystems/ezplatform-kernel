<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\FieldType;

use eZ\Publish\SPI\Persistence\Content\Field;
use eZ\Publish\SPI\Persistence\Content\VersionInfo;

interface DefaultDataFieldStorage
{
    /**
     * Populates $field value property with default data based on the external data.
     * $field->value is a {@link \eZ\Publish\SPI\Persistence\Content\FieldValue} object.
     * This value holds the data as a {@link \eZ\Publish\Core\FieldType\Value} based object, according to
     * the field type (e.g. for TextLine, it will be a {@link \eZ\Publish\Core\FieldType\TextLine\Value} object).
     */
    public function getDefaultFieldData(VersionInfo $versionInfo, Field $field): void;
}
