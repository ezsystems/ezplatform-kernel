<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace eZ\Bundle\EzPublishCoreBundle\Imagine\Filter\Imagick;

use eZ\Bundle\EzPublishCoreBundle\Imagine\Filter\AbstractFilter;
use Imagine\Image\ImageInterface;

class ReduceNoiseFilter extends AbstractFilter
{
    /**
     * @param \Imagine\Image\ImageInterface|\Imagine\Imagick\Image $image
     *
     * @return \Imagine\Image\ImageInterface
     */
    public function apply(ImageInterface $image)
    {
        /** @var \Imagick $imagick */
        $imagick = $image->getImagick();
        $imagick->reduceNoiseImage((float)$this->getOption('radius', 0));

        return $image;
    }
}
