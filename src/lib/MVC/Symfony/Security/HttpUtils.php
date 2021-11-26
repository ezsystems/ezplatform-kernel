<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\MVC\Symfony\Security;

use Ibexa\Core\MVC\Symfony\SiteAccess;
use Ibexa\Core\MVC\Symfony\SiteAccess\SiteAccessAware;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\HttpUtils as BaseHttpUtils;

class HttpUtils extends BaseHttpUtils implements SiteAccessAware
{
    /** @var \Ibexa\Core\MVC\Symfony\SiteAccess */
    private $siteAccess;

    /**
     * @param \Ibexa\Core\MVC\Symfony\SiteAccess|null $siteAccess
     */
    public function setSiteAccess(SiteAccess $siteAccess = null)
    {
        $this->siteAccess = $siteAccess;
    }

    private function analyzeLink($path)
    {
        if ($path[0] === '/' && $this->siteAccess->matcher instanceof SiteAccess\URILexer) {
            $path = $this->siteAccess->matcher->analyseLink($path);
        }

        return $path;
    }

    public function generateUri($request, $path)
    {
        if ($this->isRouteName($path)) {
            // Remove siteaccess attribute to avoid triggering reverse siteaccess lookup during link generation.
            $request->attributes->remove('siteaccess');
        }

        return parent::generateUri($request, $this->analyzeLink($path));
    }

    public function checkRequestPath(Request $request, $path)
    {
        return parent::checkRequestPath($request, $this->analyzeLink($path));
    }

    /**
     * @param string $path Path can be URI, absolute URL or a route name.
     *
     * @return bool
     */
    private function isRouteName($path)
    {
        return $path && strpos($path, 'http') !== 0 && strpos($path, '/') !== 0;
    }
}

class_alias(HttpUtils::class, 'eZ\Publish\Core\MVC\Symfony\Security\HttpUtils');
