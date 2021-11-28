<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Bundle\Core\EventListener;

use Ibexa\Core\MVC\ConfigResolverInterface;
use Ibexa\Core\MVC\Symfony\Event\PostSiteAccessMatchEvent;
use Ibexa\Core\MVC\Symfony\MVCEvents;
use Ibexa\Core\MVC\Symfony\Routing\Generator;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * This siteaccess listener handles routing related runtime configuration.
 */
class RoutingListener implements EventSubscriberInterface
{
    /** @var \Ibexa\Core\MVC\ConfigResolverInterface */
    private $configResolver;

    /** @var \Symfony\Component\Routing\RouterInterface */
    private $urlAliasRouter;

    /** @var \Ibexa\Core\MVC\Symfony\Routing\Generator */
    private $urlAliasGenerator;

    public function __construct(ConfigResolverInterface $configResolver, RouterInterface $urlAliasRouter, Generator $urlAliasGenerator)
    {
        $this->configResolver = $configResolver;
        $this->urlAliasRouter = $urlAliasRouter;
        $this->urlAliasGenerator = $urlAliasGenerator;
    }

    public static function getSubscribedEvents()
    {
        return [
            MVCEvents::SITEACCESS => ['onSiteAccessMatch', 200],
        ];
    }

    public function onSiteAccessMatch(PostSiteAccessMatchEvent $event)
    {
        $rootLocationId = $this->configResolver->getParameter('content.tree_root.location_id');
        $this->urlAliasRouter->setRootLocationId($rootLocationId);
        $this->urlAliasGenerator->setRootLocationId($rootLocationId);
        $this->urlAliasGenerator->setExcludedUriPrefixes($this->configResolver->getParameter('content.tree_root.excluded_uri_prefixes'));
    }
}

class_alias(RoutingListener::class, 'eZ\Bundle\EzPublishCoreBundle\EventListener\RoutingListener');
