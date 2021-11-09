<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\MVC\Symfony\SiteAccess\Provider;

use Ibexa\Core\Base\Exceptions\NotFoundException;
use Ibexa\Core\MVC\Symfony\SiteAccess;
use Ibexa\Core\MVC\Symfony\SiteAccess\SiteAccessProviderInterface;
use Traversable;

final class ChainSiteAccessProvider implements SiteAccessProviderInterface
{
    /** @var \Ibexa\Core\MVC\Symfony\SiteAccess\SiteAccessProviderInterface[] */
    private $providers;

    /**
     * @param \Ibexa\Core\MVC\Symfony\SiteAccess\SiteAccessProviderInterface[] $providers
     */
    public function __construct(iterable $providers = [])
    {
        $this->providers = $providers;
    }

    public function getSiteAccesses(): Traversable
    {
        foreach ($this->providers as $provider) {
            foreach ($provider->getSiteAccesses() as $siteAccess) {
                yield $siteAccess;
            }
        }

        yield from [];
    }

    public function isDefined(string $name): bool
    {
        foreach ($this->providers as $provider) {
            if ($provider->isDefined($name)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @throws \Ibexa\Core\Base\Exceptions\NotFoundException
     */
    public function getSiteAccess(string $name): SiteAccess
    {
        foreach ($this->providers as $provider) {
            if ($provider->isDefined($name)) {
                return $provider->getSiteAccess($name);
            }
        }

        throw new NotFoundException('Site Access', $name);
    }
}

class_alias(ChainSiteAccessProvider::class, 'eZ\Publish\Core\MVC\Symfony\SiteAccess\Provider\ChainSiteAccessProvider');
