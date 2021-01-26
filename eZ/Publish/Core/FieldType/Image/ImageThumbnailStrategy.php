<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace eZ\Publish\Core\FieldType\Image;

use eZ\Publish\API\Repository\Values\Content\Field;
use eZ\Publish\API\Repository\Values\Content\Thumbnail;
use eZ\Publish\Core\Repository\Values\Content\VersionInfo;
use eZ\Publish\API\Repository\Values\Content\VersionInfo as APIVersionInfo;
use eZ\Publish\SPI\Repository\Strategy\ContentThumbnail\Field\FieldTypeBasedThumbnailStrategy;
use eZ\Publish\SPI\Variation\VariationHandler;

class ImageThumbnailStrategy implements FieldTypeBasedThumbnailStrategy
{
    /** @var string */
    private $fieldTypeIdentifier;

    /** @var \eZ\Publish\SPI\Variation\VariationHandler */
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
        /** @var \eZ\Publish\SPI\Variation\Values\ImageVariation $variation */
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
