<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Bundle\Core\Imagine\Filter\Loader;

use Imagine\Exception\InvalidArgumentException;
use Imagine\Image\ImageInterface;

/**
 * Filter loader for geometry/scaleheight filter.
 * Proxy to RelativeResizeFilterLoader.
 */
class ScaleHeightFilterLoader extends FilterLoaderWrapped
{
    public function load(ImageInterface $image, array $options = [])
    {
        if (empty($options)) {
            throw new InvalidArgumentException('Missing width option');
        }

        return $this->innerLoader->load($image, ['heighten' => $options[0]]);
    }
}

class_alias(ScaleHeightFilterLoader::class, 'eZ\Bundle\EzPublishCoreBundle\Imagine\Filter\Loader\ScaleHeightFilterLoader');
