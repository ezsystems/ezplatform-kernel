<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Bundle\Core\URLChecker;

interface URLHandlerRegistryInterface
{
    /**
     * Adds scheme handler.
     *
     * @param string $scheme
     * @param \Ibexa\Bundle\Core\URLChecker\URLHandlerInterface $handler
     */
    public function addHandler($scheme, URLHandlerInterface $handler);

    /**
     * Is scheme supported ?
     *
     * @param string $scheme
     *
     * @return bool
     */
    public function supported($scheme);

    /**
     * Returns handler for scheme.
     *
     * @param string $scheme
     *
     * @return \Ibexa\Bundle\Core\URLChecker\URLHandlerInterface
     *
     * @throw \InvalidArgumentException When scheme isn't supported
     */
    public function getHandler($scheme);
}

class_alias(URLHandlerRegistryInterface::class, 'eZ\Bundle\EzPublishCoreBundle\URLChecker\URLHandlerRegistryInterface');
