<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\MVC\Symfony\Templating;

use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Core\Helper\TranslationHelper;
use Ibexa\Core\MVC\ConfigResolverInterface;
use Ibexa\Core\MVC\Symfony\RequestStackAware;
use Ibexa\Core\MVC\Symfony\Routing\UrlAliasRouter;
use Symfony\Component\Routing\RouterInterface;

/**
 * Templating helper object globally accessible, through the "ezpublish" variable (in Twig).
 * Container is injected to be sure to lazy load underlying services and to avoid scope conflict.
 */
class GlobalHelper
{
    use RequestStackAware;

    /** @var \Ibexa\Core\MVC\ConfigResolverInterface */
    protected $configResolver;

    /** @var \Ibexa\Contracts\Core\Repository\LocationService */
    protected $locationService;

    /** @var \Symfony\Component\Routing\RouterInterface */
    protected $router;

    /** @var \Ibexa\Core\Helper\TranslationHelper */
    protected $translationHelper;

    public function __construct(
        ConfigResolverInterface $configResolver,
        LocationService $locationService,
        RouterInterface $router,
        TranslationHelper $translationHelper
    ) {
        $this->configResolver = $configResolver;
        $this->locationService = $locationService;
        $this->router = $router;
        $this->translationHelper = $translationHelper;
    }

    /**
     * Returns the current siteaccess.
     *
     * @return \Ibexa\Core\MVC\Symfony\SiteAccess|null
     */
    public function getSiteaccess()
    {
        $request = $this->getCurrentRequest();
        if ($request) {
            return $request->attributes->get('siteaccess');
        }
    }

    /**
     * Returns the view parameters as a hash.
     *
     * @return array|null
     */
    public function getViewParameters()
    {
        $request = $this->getCurrentRequest();
        if ($request) {
            return $request->attributes->get('viewParameters');
        }
    }

    /**
     * Returns the view parameters as a string.
     * e.g. /(foo)/bar.
     *
     * @return string
     */
    public function getViewParametersString()
    {
        $request = $this->getCurrentRequest();
        if ($request) {
            return $request->attributes->get('viewParametersString');
        }
    }

    /**
     * Returns the requested URI string (aka semanticPathInfo).
     *
     * @return string
     */
    public function getRequestedUriString()
    {
        $request = $this->getCurrentRequest();
        if ($request) {
            return $request->attributes->get('semanticPathinfo');
        }
    }

    /**
     * Returns the "system" URI string.
     * System URI is the URI for internal content controller.
     * E.g. /content/location/123/full.
     *
     * If current route is not an URLAlias, then the current Pathinfo is returned.
     *
     * @return string|null
     */
    public function getSystemUriString()
    {
        $request = $this->getCurrentRequest();
        if ($request) {
            if ($request->attributes->get('_route') === UrlAliasRouter::URL_ALIAS_ROUTE_NAME) {
                return $this->router
                    ->generate(
                        '_ez_content_view',
                        [
                            'contentId' => $request->attributes->get('contentId'),
                            'locationId' => $request->attributes->get('locationId'),
                            'viewType' => $request->attributes->get('viewType'),
                        ]
                    );
            }

            return $this->getRequestedUriString();
        }
    }

    /**
     * Returns the root location.
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\Location
     */
    public function getRootLocation()
    {
        return $this->locationService->loadLocation(
            $this->configResolver->getParameter('content.tree_root.location_id')
        );
    }

    /**
     * Returns the translation SiteAccess for $language, or null if it cannot be found.
     *
     * @param string $language
     *
     * @return string|null
     */
    public function getTranslationSiteAccess($language)
    {
        return $this->translationHelper->getTranslationSiteAccess($language);
    }

    /**
     * Returns the list of available languages.
     *
     * @return array
     */
    public function getAvailableLanguages()
    {
        return $this->translationHelper->getAvailableLanguages();
    }

    /**
     * Returns the config resolver.
     *
     * @return \Ibexa\Core\MVC\ConfigResolverInterface
     */
    public function getConfigResolver()
    {
        return $this->configResolver;
    }
}

class_alias(GlobalHelper::class, 'eZ\Publish\Core\MVC\Symfony\Templating\GlobalHelper');
