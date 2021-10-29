<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Bundle\Core\Routing;

use Ibexa\Core\MVC\ConfigResolverInterface;
use Ibexa\Core\MVC\Symfony\Routing\UrlAliasRouter as BaseUrlAliasRouter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

class UrlAliasRouter extends BaseUrlAliasRouter
{
    /** @var \Ibexa\Core\MVC\ConfigResolverInterface */
    protected $configResolver;

    public function setConfigResolver(ConfigResolverInterface $configResolver)
    {
        $this->configResolver = $configResolver;
    }

    public function matchRequest(Request $request)
    {
        // UrlAliasRouter might be disabled from configuration.
        // An example is for running the admin interface: it needs to be entirely run through the legacy kernel.
        if ($this->configResolver->getParameter('url_alias_router') === false) {
            throw new ResourceNotFoundException('Config requires bypassing UrlAliasRouter');
        }

        return parent::matchRequest($request);
    }

    /**
     * Will return the right UrlAlias in regards to configured root location.
     *
     * @param string $pathinfo
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException if the path does not exist or is not valid for the given language
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\URLAlias
     */
    protected function getUrlAlias($pathinfo)
    {
        $pathPrefix = $this->generator->getPathPrefixByRootLocationId($this->rootLocationId);

        if (
            $this->rootLocationId === null ||
            $this->generator->isUriPrefixExcluded($pathinfo) ||
            $pathPrefix === '/'
        ) {
            return parent::getUrlAlias($pathinfo);
        }

        return $this->urlAliasService->lookup($pathPrefix . $pathinfo);
    }
}

class_alias(UrlAliasRouter::class, 'eZ\Bundle\EzPublishCoreBundle\Routing\UrlAliasRouter');
