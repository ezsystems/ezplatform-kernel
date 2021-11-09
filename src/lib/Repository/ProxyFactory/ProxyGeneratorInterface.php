<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\Repository\ProxyFactory;

use Closure;
use ProxyManager\Proxy\VirtualProxyInterface;

/**
 * @internal
 */
interface ProxyGeneratorInterface
{
    /**
     * @template T
     *
     * @param class-string<T> $className
     * @param \Closure $initializer
     * @param array<string, mixed> $proxyOptions
     *
     * @return \ProxyManager\Proxy\VirtualProxyInterface&T
     */
    public function createProxy(string $className, Closure $initializer, array $proxyOptions = []): VirtualProxyInterface;

    public function warmUp(iterable $classes): void;
}

class_alias(ProxyGeneratorInterface::class, 'eZ\Publish\Core\Repository\ProxyFactory\ProxyGeneratorInterface');
