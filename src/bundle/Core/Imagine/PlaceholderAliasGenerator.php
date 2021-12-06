<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Bundle\Core\Imagine;

use Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException as APIInvalidArgumentException;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Values\Content\Field;
use Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo;
use Ibexa\Contracts\Core\Variation\VariationHandler;
use Ibexa\Core\FieldType\Image\Value as ImageValue;
use Ibexa\Core\FieldType\Value;
use Ibexa\Core\IO\IOServiceInterface;
use InvalidArgumentException;
use Liip\ImagineBundle\Exception\Imagine\Cache\Resolver\NotResolvableException;
use Liip\ImagineBundle\Imagine\Cache\Resolver\ResolverInterface;

class PlaceholderAliasGenerator implements VariationHandler
{
    /** @var \Ibexa\Contracts\Core\Variation\VariationHandler */
    private $aliasGenerator;

    /** @var \Liip\ImagineBundle\Imagine\Cache\Resolver\ResolverInterface */
    private $ioResolver;

    /** @var \Ibexa\Core\IO\IOServiceInterface */
    private $ioService;

    /** @var \Ibexa\Bundle\Core\Imagine\PlaceholderProvider|null */
    private $placeholderProvider;

    /** @var array */
    private $placeholderOptions = [];

    /** @var bool */
    private $verifyBinaryDataAvailability = false;

    public function __construct(
        VariationHandler $aliasGenerator,
        ResolverInterface $ioResolver,
        IOServiceInterface $ioService
    ) {
        $this->aliasGenerator = $aliasGenerator;
        $this->ioResolver = $ioResolver;
        $this->ioService = $ioService;
    }

    /**
     * {@inheritdoc}
     */
    public function getVariation(Field $field, VersionInfo $versionInfo, $variationName, array $parameters = [])
    {
        if ($this->placeholderProvider !== null) {
            /** @var \Ibexa\Core\FieldType\Image\Value $imageValue */
            $imageValue = $field->value;
            if (!$this->supportsValue($imageValue)) {
                throw new InvalidArgumentException("Value of Field with ID {$field->id} ($field->fieldDefIdentifier) cannot be used for generating an image placeholder.");
            }

            if (!$this->isOriginalImageAvailable($imageValue)) {
                $binary = $this->ioService->newBinaryCreateStructFromLocalFile(
                    $this->placeholderProvider->getPlaceholder($imageValue, $this->placeholderOptions)
                );
                $binary->id = $imageValue->id;

                $this->ioService->createBinaryFile($binary);
            }
        }

        return $this->aliasGenerator->getVariation($field, $versionInfo, $variationName, $parameters);
    }

    public function setPlaceholderProvider(PlaceholderProvider $provider, array $options = [])
    {
        $this->placeholderProvider = $provider;
        $this->placeholderOptions = $options;
    }

    /**
     * Enable/disable binary data availability verification.
     *
     * If enabled then binary data storage will be used to check if original file exists. Required for DFS setup.
     *
     * @param bool $verifyBinaryDataAvailability
     */
    public function setVerifyBinaryDataAvailability(bool $verifyBinaryDataAvailability): void
    {
        $this->verifyBinaryDataAvailability = $verifyBinaryDataAvailability;
    }

    public function supportsValue(Value $value): bool
    {
        return $value instanceof ImageValue;
    }

    private function isOriginalImageAvailable(ImageValue $imageValue): bool
    {
        try {
            $this->ioResolver->resolve($imageValue->id, IORepositoryResolver::VARIATION_ORIGINAL);
        } catch (NotResolvableException $e) {
            return false;
        }

        if ($this->verifyBinaryDataAvailability) {
            try {
                // Try to open input stream to original file
                $this->ioService->getFileInputStream($this->ioService->loadBinaryFile($imageValue->id));
            } catch (NotFoundException | APIInvalidArgumentException $e) {
                return false;
            }
        }

        return true;
    }
}

class_alias(PlaceholderAliasGenerator::class, 'eZ\Bundle\EzPublishCoreBundle\Imagine\PlaceholderAliasGenerator');
