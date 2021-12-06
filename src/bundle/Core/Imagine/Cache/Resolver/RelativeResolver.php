<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Bundle\Core\Imagine\Cache\Resolver;

use Liip\ImagineBundle\Imagine\Cache\Resolver\ProxyResolver as ImagineProxyResolver;
use Liip\ImagineBundle\Imagine\Cache\Resolver\ResolverInterface;

/**
 * Relative resolver, omits host info.
 */
class RelativeResolver extends ImagineProxyResolver
{
    /**
     * @param \Liip\ImagineBundle\Imagine\Cache\Resolver\ResolverInterface $resolver
     */
    public function __construct(ResolverInterface $resolver)
    {
        parent::__construct($resolver, []);
    }

    /**
     * Returns relative image path.
     *
     * @param $url string
     *
     * @return string
     */
    protected function rewriteUrl($url)
    {
        return parse_url($url, PHP_URL_PATH);
    }
}

class_alias(RelativeResolver::class, 'eZ\Bundle\EzPublishCoreBundle\Imagine\Cache\Resolver\RelativeResolver');
