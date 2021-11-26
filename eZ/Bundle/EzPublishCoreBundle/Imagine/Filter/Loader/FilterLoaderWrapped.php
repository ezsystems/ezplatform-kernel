<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace eZ\Bundle\EzPublishCoreBundle\Imagine\Filter\Loader;

use Liip\ImagineBundle\Imagine\Filter\Loader\LoaderInterface;

abstract class FilterLoaderWrapped implements LoaderInterface
{
    /** @var \Liip\ImagineBundle\Imagine\Filter\Loader\LoaderInterface */
    protected $innerLoader;

    /**
     * @param \Liip\ImagineBundle\Imagine\Filter\Loader\LoaderInterface $innerLoader
     */
    public function setInnerLoader(LoaderInterface $innerLoader)
    {
        $this->innerLoader = $innerLoader;
    }
}
