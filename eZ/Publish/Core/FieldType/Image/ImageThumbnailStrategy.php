<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace eZ\Publish\Core\FieldType\Image;

use Exception;
use eZ\Publish\API\Repository\Values\Content\Field;
use eZ\Publish\API\Repository\Values\Content\Thumbnail;
use eZ\Publish\API\Repository\Values\Content\VersionInfo as APIVersionInfo;
use eZ\Publish\Core\MVC\Exception\SourceImageNotFoundException;
use eZ\Publish\Core\Repository\Values\Content\VersionInfo;
use eZ\Publish\SPI\Repository\Strategy\ContentThumbnail\Field\FieldTypeBasedThumbnailStrategy;
use eZ\Publish\SPI\Variation\VariationHandler;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class ImageThumbnailStrategy implements FieldTypeBasedThumbnailStrategy, LoggerAwareInterface
{
    use LoggerAwareTrait;

    /** @var string */
    private $fieldTypeIdentifier;

    /** @var \eZ\Publish\SPI\Variation\VariationHandler */
    private $variationHandler;

    /** @var string */
    private $variationName;

    public function __construct(
        string $fieldTypeIdentifier,
        VariationHandler $variationHandler,
        string $variationName,
        ?LoggerInterface $logger = null
    ) {
        $this->fieldTypeIdentifier = $fieldTypeIdentifier;
        $this->variationHandler = $variationHandler;
        $this->variationName = $variationName;
        $this->logger = $logger ?? new NullLogger();
    }

    public function getFieldTypeIdentifier(): string
    {
        return $this->fieldTypeIdentifier;
    }

    public function getThumbnail(Field $field, ?APIVersionInfo $versionInfo = null): ?Thumbnail
    {
        try {
            /** @var \eZ\Publish\SPI\Variation\Values\ImageVariation $variation */
            $variation = $this->variationHandler->getVariation(
                $field,
                $versionInfo ?? new VersionInfo(),
                $this->variationName
            );
        } catch (SourceImageNotFoundException $e) {
            $this->logger->warning(
                sprintf(
                    'Thumbnail source image generated for %s field and %s variation could not be found (%s). %s',
                    $field->fieldTypeIdentifier,
                    $this->variationName,
                    $e->getMessage(),
                    $this->generateContentDetailsMessage($versionInfo)
                ),
                [
                    'exception' => $e,
                ]
            );

            return null;
        } catch (Exception $e) {
            $this->logger->warning(
                sprintf(
                    'Thumbnail could not be generated for %s field and %s variation due to %s. %s',
                    $field->fieldTypeIdentifier,
                    $this->variationName,
                    $e->getMessage(),
                    $this->generateContentDetailsMessage($versionInfo)
                ),
                [
                    'exception' => $e,
                ]
            );

            return null;
        }

        return new Thumbnail([
            'resource' => $variation->uri,
            'width' => $variation->width,
            'height' => $variation->height,
            'mimeType' => $variation->mimeType,
        ]);
    }

    private function generateContentDetailsMessage(?APIVersionInfo $versionInfo): string
    {
        return $versionInfo !== null
            ? sprintf('Content: %d, Version No: %d', $versionInfo->getContentInfo()->id, $versionInfo->versionNo)
            : '';
    }
}
