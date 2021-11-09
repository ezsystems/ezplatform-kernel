<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Core\DependencyInjection\Configuration\ConfigResolver;

use Ibexa\Bundle\Core\DependencyInjection\Configuration\ConfigResolver\GlobalScopeConfigResolver;
use Ibexa\Core\MVC\ConfigResolverInterface;

class GlobalScopeConfigResolverTest extends ConfigResolverTest
{
    protected function getResolver(string $defaultNamespace = self::DEFAULT_NAMESPACE): ConfigResolverInterface
    {
        $configResolver = new GlobalScopeConfigResolver(
            $defaultNamespace
        );
        $configResolver->setContainer($this->containerMock);

        return $configResolver;
    }

    protected function getScope(): string
    {
        return 'global';
    }
}

class_alias(GlobalScopeConfigResolverTest::class, 'eZ\Bundle\EzPublishCoreBundle\Tests\DependencyInjection\Configuration\ConfigResolver\GlobalScopeConfigResolverTest');
