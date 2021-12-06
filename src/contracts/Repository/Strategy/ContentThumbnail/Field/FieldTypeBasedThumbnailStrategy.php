<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Strategy\ContentThumbnail\Field;

interface FieldTypeBasedThumbnailStrategy extends ThumbnailStrategy
{
    public function getFieldTypeIdentifier(): string;
}

class_alias(FieldTypeBasedThumbnailStrategy::class, 'eZ\Publish\SPI\Repository\Strategy\ContentThumbnail\Field\FieldTypeBasedThumbnailStrategy');
