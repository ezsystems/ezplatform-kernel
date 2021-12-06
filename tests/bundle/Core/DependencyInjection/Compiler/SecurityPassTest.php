<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Bundle\Core\DependencyInjection\Compiler;

use Ibexa\Bundle\Core\DependencyInjection\Compiler\SecurityPass;
use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Contracts\Core\Repository\UserService;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class SecurityPassTest extends AbstractCompilerPassTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->setDefinition('security.authentication.provider.dao', new Definition());
        $this->setDefinition('security.authentication.provider.rememberme', new Definition());
        $this->setDefinition('security.authentication.provider.anonymous', new Definition());
        $this->setDefinition('security.http_utils', new Definition());
        $this->setDefinition('security.authentication.success_handler', new Definition());
        $this->setDefinition('ezpublish.config.resolver', new Definition());
        $this->setDefinition('ezpublish.siteaccess', new Definition());
        $this->setDefinition(PermissionResolver::class, new Definition());
        $this->setDefinition(UserService::class, new Definition());
    }

    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new SecurityPass());
    }

    public function testAlteredDaoAuthenticationProvider()
    {
        $this->compile();
        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'security.authentication.provider.dao',
            'setPermissionResolver',
            [new Reference(PermissionResolver::class)]
        );
        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'security.authentication.provider.dao',
            'setUserService',
            [new Reference(UserService::class)]
        );
        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'security.authentication.provider.rememberme',
            'setPermissionResolver',
            [new Reference(PermissionResolver::class)]
        );
        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'security.authentication.provider.anonymous',
            'setPermissionResolver',
            [new Reference(PermissionResolver::class)]
        );
        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'security.authentication.provider.anonymous',
            'setConfigResolver',
            [new Reference('ezpublish.config.resolver')]
        );
        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'security.http_utils',
            'setSiteAccess',
            [new Reference('ezpublish.siteaccess')]
        );
        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'security.authentication.success_handler',
            'setConfigResolver',
            [new Reference('ezpublish.config.resolver')]
        );
    }
}

class_alias(SecurityPassTest::class, 'eZ\Bundle\EzPublishCoreBundle\Tests\DependencyInjection\Compiler\SecurityPassTest');
