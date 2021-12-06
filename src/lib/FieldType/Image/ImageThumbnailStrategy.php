<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\FieldType\Image;

use Ibexa\Contracts\Core\Repository\Strategy\ContentThumbnail\Field\FieldTypeBasedThumbnailStrategy;
use Ibexa\Contracts\Core\Repository\Values\Content\Field;
use Ibexa\Contracts\Core\Repository\Values\Content\Thumbnail;
use Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo as APIVersionInfo;
use Ibexa\Contracts\Core\Variation\VariationHandler;
use Ibexa\Core\Repository\Values\Content\VersionInfo;

class ImageThumbnailStrategy implements FieldTypeBasedThumbnailStrategy
{
    /** @var string */
    private $fieldTypeIdentifier;

    /** @var \Ibexa\Contracts\Core\Variation\VariationHandler */
    private $variationHandler;

    /** @var string */
    private $variationName;

    public function __construct(
        string $fieldTypeIdentifier,
        VariationHandler $variationHandler,
        string $variationName
    ) {
        $this->fieldTypeIdentifier = $fieldTypeIdentifier;
        $this->variationHandler = $variationHandler;
        $this->variationName = $variationName;
    }

    public function getFieldTypeIdentifier(): string
    {
        return $this->fieldTypeIdentifier;
    }

    public function getThumbnail(Field $field, ?APIVersionInfo $versionInfo = null): ?Thumbnail
    {
        /** @var \Ibexa\Contracts\Core\Variation\Values\ImageVariation $variation */
        $variation = $this->variationHandler->getVariation(
            $field,
            $versionInfo ?? new VersionInfo(),
            $this->variationName
        );

        return new Thumbnail([
            'resource' => $variation->uri,
            'width' => $variation->width,
            'height' => $variation->height,
            'mimeType' => $variation->mimeType,
        ]);
    }
}

class_alias(ImageThumbnailStrategy::class, 'eZ\Publish\Core\FieldType\Image\ImageThumbnailStrategy');
