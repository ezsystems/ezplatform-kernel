<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\FieldType\ImageAsset;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\Values\Content\Field;
use eZ\Publish\Core\FieldType\ImageAsset;

final class ContentImageAssetMapperStrategy implements ImageAssetMapperStrategyInterface
{
    /* @var \eZ\Publish\Core\FieldType\ImageAsset\AssetMapper */
    private $assetMapper;

    /** @var \eZ\Publish\API\Repository\ContentService */
    private $contentService;

    public function __construct(
        ImageAsset\AssetMapper $assetMapper,
        ContentService $contentService
    ) {
        $this->assetMapper = $assetMapper;
        $this->contentService = $contentService;
    }

    public function canProcess(ImageAsset\Value $value): bool
    {
        return $value->source === null;
    }

    /**
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException|
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    public function process(ImageAsset\Value $value): Field
    {
        $assetField = $this->assetMapper->getAssetField(
            $this->contentService->loadContent($value->destinationContentId)
        );

        if (empty($assetField->value->alternativeText)) {
            $assetField->value->alternativeText = $value->alternativeText;
        }

        return $assetField;
    }
}
