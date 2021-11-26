<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Bundle\Core\Imagine\Filter\Loader;

use Imagine\Exception\InvalidArgumentException;
use Imagine\Image\ImageInterface;

/**
 * Filter loader for geometry/scale filter.
 * Proxy to RelativeResizeFilterLoader.
 */
class ScaleFilterLoader extends FilterLoaderWrapped
{
    public function load(ImageInterface $image, array $options = [])
    {
        if (count($options) < 2) {
            throw new InvalidArgumentException('Missing width and/or height options');
        }

        list($width, $height) = $options;
        $size = $image->getSize();
        $ratioWidth = $width / $size->getWidth();
        $ratioHeight = $height / $size->getHeight();

        // We shall use the side which has the lowest ratio with target value
        // as $width and $height are always maximum values.
        if ($ratioWidth <= $ratioHeight) {
            $method = 'widen';
            $value = $width;
        } else {
            $method = 'heighten';
            $value = $height;
        }

        return $this->innerLoader->load($image, [$method => $value]);
    }
}

class_alias(ScaleFilterLoader::class, 'eZ\Bundle\EzPublishCoreBundle\Imagine\Filter\Loader\ScaleFilterLoader');
