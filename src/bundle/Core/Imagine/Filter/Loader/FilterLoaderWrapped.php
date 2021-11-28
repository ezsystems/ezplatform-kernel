<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Bundle\Core\Imagine\Filter\Loader;

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

class_alias(FilterLoaderWrapped::class, 'eZ\Bundle\EzPublishCoreBundle\Imagine\Filter\Loader\FilterLoaderWrapped');
