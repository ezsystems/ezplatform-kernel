<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Core\Cache\Warmer;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\Repository\Values\Content\Section;
use Ibexa\Contracts\Core\Repository\Values\Content\Thumbnail;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeGroup;
use Ibexa\Contracts\Core\Repository\Values\User\User;
use Ibexa\Core\Repository\ProxyFactory\ProxyGeneratorInterface;
use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;

final class ProxyCacheWarmer implements CacheWarmerInterface
{
    public const PROXY_CLASSES = [
        Content::class,
        ContentInfo::class,
        ContentType::class,
        ContentTypeGroup::class,
        Language::class,
        Location::class,
        Section::class,
        User::class,
        Thumbnail::class,
    ];

    /** @var \Ibexa\Core\Repository\ProxyFactory\ProxyGeneratorInterface */
    private $proxyGenerator;

    public function __construct(ProxyGeneratorInterface $proxyGenerator)
    {
        $this->proxyGenerator = $proxyGenerator;
    }

    public function isOptional(): bool
    {
        return false;
    }

    public function warmUp($cacheDir): void
    {
        $this->proxyGenerator->warmUp(self::PROXY_CLASSES);
    }
}

class_alias(ProxyCacheWarmer::class, 'eZ\Bundle\EzPublishCoreBundle\Cache\Warmer\ProxyCacheWarmer');
