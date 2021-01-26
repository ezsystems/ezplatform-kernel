<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace eZ\Publish\Core\FieldType\Image;

use eZ\Publish\API\Repository\Values\Content\Field;
use eZ\Publish\API\Repository\Values\Content\Thumbnail;
use eZ\Publish\Core\Repository\ProxyFactory\ProxyGeneratorInterface;
use eZ\Publish\SPI\Repository\Strategy\ContentThumbnail\Field\FieldTypeBasedThumbnailStrategy;
use ProxyManager\Proxy\LazyLoadingInterface;

final class ImageThumbnailProxyStrategy implements FieldTypeBasedThumbnailStrategy
{
    /** @var \eZ\Publish\Core\FieldType\Image\ImageThumbnailStrategy */
    private $imageThumbnailStrategy;

    /** @var \eZ\Publish\Core\Repository\ProxyFactory\ProxyGeneratorInterface */
    private $proxyGenerator;

    public function __construct(
        ImageThumbnailStrategy $imageThumbnailStrategy,
        ProxyGeneratorInterface $proxyGenerator
    ) {
        $this->imageThumbnailStrategy = $imageThumbnailStrategy;
        $this->proxyGenerator = $proxyGenerator;
    }

    public function getFieldTypeIdentifier(): string
    {
        return $this->imageThumbnailStrategy->getFieldTypeIdentifier();
    }

    public function getThumbnail(Field $field): ?Thumbnail
    {
        $initializer = function (
            &$wrappedObject, LazyLoadingInterface $proxy, $method, array $parameters, &$initializer
        ) use ($field): bool {
            $initializer = null;

            $wrappedObject = $this->imageThumbnailStrategy->getThumbnail($field);

            return true;
        };

        return $this->proxyGenerator->createProxy(Thumbnail::class, $initializer);
    }
}
