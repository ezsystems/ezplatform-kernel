<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Core;

use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Core\MVC\ConfigResolverInterface;
use Ibexa\Core\MVC\Symfony\Templating\GlobalHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class Controller extends AbstractController
{
    public function getRepository(): Repository
    {
        return $this->container->get('ezpublish.api.repository');
    }

    protected function getConfigResolver(): ConfigResolverInterface
    {
        return $this->container->get('ezpublish.config.resolver');
    }

    public function getGlobalHelper(): GlobalHelper
    {
        return $this->container->get('ezpublish.templating.global_helper');
    }

    /**
     * Returns the root location object for current siteaccess configuration.
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\Location
     */
    public function getRootLocation(): Location
    {
        return $this->getGlobalHelper()->getRootLocation();
    }

    public static function getSubscribedServices(): array
    {
        return array_merge(
            parent::getSubscribedServices(),
            [
                'ezpublish.api.repository' => Repository::class,
                'ezpublish.config.resolver' => ConfigResolverInterface::class,
                'ezpublish.templating.global_helper' => GlobalHelper::class,
            ]
        );
    }
}

class_alias(Controller::class, 'eZ\Bundle\EzPublishCoreBundle\Controller');
