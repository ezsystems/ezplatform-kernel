<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\FieldType\ImageAsset;

use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Strategy\ContentThumbnail\Field\FieldTypeBasedThumbnailStrategy;
use Ibexa\Contracts\Core\Repository\Strategy\ContentThumbnail\ThumbnailStrategy as ContentThumbnailStrategy;
use Ibexa\Contracts\Core\Repository\Values\Content\Field;
use Ibexa\Contracts\Core\Repository\Values\Content\Thumbnail;
use Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo;

class ImageAssetThumbnailStrategy implements FieldTypeBasedThumbnailStrategy
{
    /** @var string */
    private $fieldTypeIdentifier;

    /** @var \Ibexa\Contracts\Core\Repository\ContentService */
    private $contentService;

    /** @var \Ibexa\Contracts\Core\Repository\Strategy\ContentThumbnail\ThumbnailStrategy */
    private $thumbnailStrategy;

    public function __construct(
        string $fieldTypeIdentifier,
        ContentThumbnailStrategy $thumbnailStrategy,
        ContentService $contentService
    ) {
        $this->fieldTypeIdentifier = $fieldTypeIdentifier;
        $this->contentService = $contentService;
        $this->thumbnailStrategy = $thumbnailStrategy;
    }

    public function getFieldTypeIdentifier(): string
    {
        return $this->fieldTypeIdentifier;
    }

    public function getThumbnail(Field $field, ?VersionInfo $versionInfo = null): ?Thumbnail
    {
        try {
            $content = $this->contentService->loadContent(
                (int) $field->value->destinationContentId,
                null,
                $versionInfo ? $versionInfo->versionNo : null
            );
        } catch (NotFoundException $e) {
            return null;
        }

        return $this->thumbnailStrategy->getThumbnail(
            $content->getContentType(),
            $content->getFields(),
            $content->versionInfo
        );
    }
}

class_alias(ImageAssetThumbnailStrategy::class, 'eZ\Publish\Core\FieldType\ImageAsset\ImageAssetThumbnailStrategy');
