<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\IO;

/**
 * Modifies, both way, and URI.
 */
interface UrlDecorator
{
    /**
     * Decorates $uri.
     *
     * @param string $uri
     *
     * @return string Decorated string
     */
    public function decorate($uri);

    /**
     * Un-decorates decorated $uri.
     *
     * @param $uri
     *
     * @return string Un-decorated string
     */
    public function undecorate($uri);
}

class_alias(UrlDecorator::class, 'eZ\Publish\Core\IO\UrlDecorator');
