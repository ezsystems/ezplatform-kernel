<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Bundle\Core\Imagine\Filter\Loader;

use Ibexa\Bundle\Core\Imagine\Filter\FilterInterface;
use Imagine\Image\ImageInterface;
use Liip\ImagineBundle\Imagine\Filter\Loader\LoaderInterface;

class SwirlFilterLoader implements LoaderInterface
{
    /** @var \Ibexa\Bundle\Core\Imagine\Filter\FilterInterface */
    private $filter;

    public function __construct(FilterInterface $filter)
    {
        $this->filter = $filter;
    }

    public function load(ImageInterface $image, array $options = [])
    {
        if (!empty($options)) {
            $this->filter->setOption('degrees', $options[0]);
        }

        return $this->filter->apply($image);
    }
}

class_alias(SwirlFilterLoader::class, 'eZ\Bundle\EzPublishCoreBundle\Imagine\Filter\Loader\SwirlFilterLoader');
