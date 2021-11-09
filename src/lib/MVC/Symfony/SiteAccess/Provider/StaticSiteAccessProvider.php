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
use Ibexa\Core\MVC\Symfony\SiteAccessGroup;
use Traversable;

/**
 * @internal For internal use only, do not rely on this class.
 *           Be aware this should be regarded as experimental feature.
 *           As in, method signatures and behavior might change in the future.
 */
final class StaticSiteAccessProvider implements SiteAccessProviderInterface
{
    /** @var string[] */
    private $siteAccessList;

    /** @var string[] */
    private $groupsBySiteAccess;

    /**
     * @param string[] $siteAccessList
     * @param string[] $groupsBySiteAccess
     */
    public function __construct(
        array $siteAccessList,
        array $groupsBySiteAccess = []
    ) {
        $this->siteAccessList = $siteAccessList;
        $this->groupsBySiteAccess = $groupsBySiteAccess;
    }

    public function getSiteAccesses(): Traversable
    {
        foreach ($this->siteAccessList as $name) {
            yield $this->createSiteAccess($name);
        }

        yield from [];
    }

    public function isDefined(string $name): bool
    {
        return \in_array($name, $this->siteAccessList, true);
    }

    public function getSiteAccess(string $name): SiteAccess
    {
        if ($this->isDefined($name)) {
            return $this->createSiteAccess($name);
        }

        throw new NotFoundException('Site Access', $name);
    }

    private function createSiteAccess(string $name): SiteAccess
    {
        $siteAccess = new SiteAccess($name, SiteAccess::DEFAULT_MATCHING_TYPE, null, self::class);
        $siteAccess->groups = array_map(
            static function ($groupName) {
                return new SiteAccessGroup($groupName);
            },
            $this->groupsBySiteAccess[$name] ?? []
        );

        return $siteAccess;
    }
}

class_alias(StaticSiteAccessProvider::class, 'eZ\Publish\Core\MVC\Symfony\SiteAccess\Provider\StaticSiteAccessProvider');
