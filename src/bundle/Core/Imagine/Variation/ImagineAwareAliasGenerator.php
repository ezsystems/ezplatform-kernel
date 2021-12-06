<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Bundle\Core\Imagine\Variation;

use Ibexa\Bundle\Core\Imagine\IORepositoryResolver;
use Ibexa\Bundle\Core\Imagine\VariationPathGenerator;
use Ibexa\Contracts\Core\Repository\Values\Content\Field;
use Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo;
use Ibexa\Contracts\Core\Variation\Values\ImageVariation;
use Ibexa\Contracts\Core\Variation\VariationHandler;
use Ibexa\Core\IO\IOServiceInterface;
use Imagine\Image\ImagineInterface;

/**
 * Alias Generator Decorator which ensures (using Imagine if needed) that ImageVariation has proper
 * dimensions.
 */
class ImagineAwareAliasGenerator implements VariationHandler
{
    /** @var \Ibexa\Contracts\Core\Variation\VariationHandler */
    private $aliasGenerator;

    /** @var \Ibexa\Bundle\Core\Imagine\VariationPathGenerator */
    private $variationPathGenerator;

    /** @var \Ibexa\Core\IO\IOServiceInterface */
    private $ioService;

    /** @var \Ibexa\Core\MVC\ConfigResolverInterface */
    private $configResolver;

    /** @var \Imagine\Image\ImagineInterface */
    private $imagine;

    public function __construct(
        VariationHandler $aliasGenerator,
        VariationPathGenerator $variationPathGenerator,
        IOServiceInterface $ioService,
        ImagineInterface $imagine
    ) {
        $this->aliasGenerator = $aliasGenerator;
        $this->variationPathGenerator = $variationPathGenerator;
        $this->ioService = $ioService;
        $this->imagine = $imagine;
    }

    /**
     * Returns a Variation object, ensuring proper image dimensions.
     *
     * {@inheritdoc}
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    public function getVariation(
        Field $field,
        VersionInfo $versionInfo,
        $variationName,
        array $parameters = []
    ) {
        /** @var \Ibexa\Contracts\Core\Variation\Values\ImageVariation $variation */
        $variation = $this->aliasGenerator->getVariation(
            $field,
            $versionInfo,
            $variationName,
            $parameters
        );

        if (null === $variation->width || null === $variation->height) {
            $variationBinaryFile = $this->getVariationBinaryFile($field->value->id, $variationName);
            $image = $this->imagine->load($this->ioService->getFileContents($variationBinaryFile));
            $dimensions = $image->getSize();

            return new ImageVariation(
                [
                    'name' => $variation->name,
                    'fileName' => $variation->fileName,
                    'dirPath' => $variation->dirPath,
                    'uri' => $variation->uri,
                    'imageId' => $variation->imageId,
                    'width' => $dimensions->getWidth(),
                    'height' => $dimensions->getHeight(),
                    'fileSize' => $variationBinaryFile->size,
                    'mimeType' => $this->ioService->getMimeType($variationBinaryFile->id),
                    'lastModified' => $variationBinaryFile->mtime,
                ]
            );
        }

        return $variation;
    }

    /**
     * Get image variation filesystem path.
     *
     * @param string $originalPath
     * @param string $variationName
     *
     * @return \Ibexa\Core\IO\Values\BinaryFile
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    private function getVariationBinaryFile($originalPath, $variationName)
    {
        if ($variationName !== IORepositoryResolver::VARIATION_ORIGINAL) {
            $variationPath = $this->variationPathGenerator->getVariationPath(
                $originalPath,
                $variationName
            );
        } else {
            $variationPath = $originalPath;
        }

        return $this->ioService->loadBinaryFile($variationPath);
    }
}

class_alias(ImagineAwareAliasGenerator::class, 'eZ\Bundle\EzPublishCoreBundle\Imagine\Variation\ImagineAwareAliasGenerator');
