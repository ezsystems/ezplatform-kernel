<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\IO;

/**
 * Converts an URL from one decorator to another.
 *
 * ```php
 * $redecorator = new UrlRedecorator(
 *   new Prefix( 'a' ),
 *   new Prefix( 'b' )
 * );
 *
 * $redecorator->redecorateFromSource( 'a/url' );
 * // 'b/url'
 *
 * $redecorator->redecorateFromTarget( 'b/url' );
 * // 'a/url'
 * ```
 */
interface UrlRedecoratorInterface
{
    /**
     * Redecorates $uri from source to target.
     *
     * @param string $uri
     *
     * @return string
     *
     * @throws \Ibexa\Core\IO\Exception\InvalidBinaryFileIdException If $uri couldn't be interpreted b y the target decorator
     */
    public function redecorateFromSource($uri);

    /**
     * Redecorates $uri from source to target.
     *
     * @param string $uri
     *
     * @return string
     *
     * @throws \Ibexa\Core\IO\Exception\InvalidBinaryFileIdException If $uri couldn't be interpreted b y the target decorator
     */
    public function redecorateFromTarget($uri);
}

class_alias(UrlRedecoratorInterface::class, 'eZ\Publish\Core\IO\UrlRedecoratorInterface');
