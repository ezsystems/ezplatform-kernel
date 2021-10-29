<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\MVC\Symfony\SiteAccess;

use Ibexa\Core\MVC\Symfony\SiteAccess;

/**
 * Provides methods for accessing Site Access information.
 */
interface SiteAccessServiceInterface
{
    public function exists(string $name): bool;

    /**
     * @throws \Ibexa\Core\Base\Exceptions\NotFoundException
     */
    public function get(string $name): SiteAccess;

    /**
     * @return \Ibexa\Core\MVC\Symfony\SiteAccess[]
     */
    public function getAll(): iterable;

    public function getCurrent(): ?SiteAccess;

    /**
     * Handles relation between SiteAccesses. Related SiteAccesses share the same repository and root location id.
     *
     * @return string[]
     */
    public function getSiteAccessesRelation(?SiteAccess $siteAccess = null): array;
}

class_alias(SiteAccessServiceInterface::class, 'eZ\Publish\Core\MVC\Symfony\SiteAccess\SiteAccessServiceInterface');
