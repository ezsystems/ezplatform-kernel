<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Bundle\Core\Imagine\Filter\Gmagick;

use Ibexa\Bundle\Core\Imagine\Filter\AbstractFilter;
use Imagine\Image\ImageInterface;

class SwirlFilter extends AbstractFilter
{
    /**
     * @param \Imagine\Image\ImageInterface|\Imagine\Gmagick\Image $image
     *
     * @return \Imagine\Image\ImageInterface
     */
    public function apply(ImageInterface $image)
    {
        /** @var \Gmagick $gmagick */
        $gmagick = $image->getGmagick();
        $gmagick->swirlimage((float)$this->getOption('degrees', 60));

        return $image;
    }
}

class_alias(SwirlFilter::class, 'eZ\Bundle\EzPublishCoreBundle\Imagine\Filter\Gmagick\SwirlFilter');
