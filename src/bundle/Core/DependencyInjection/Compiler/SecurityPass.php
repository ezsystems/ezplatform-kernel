<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Bundle\Core\DependencyInjection\Compiler;

use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\Core\MVC\Symfony\Security\Authentication\AnonymousAuthenticationProvider;
use Ibexa\Core\MVC\Symfony\Security\Authentication\DefaultAuthenticationSuccessHandler;
use Ibexa\Core\MVC\Symfony\Security\Authentication\RememberMeRepositoryAuthenticationProvider;
use Ibexa\Core\MVC\Symfony\Security\Authentication\RepositoryAuthenticationProvider;
use Ibexa\Core\MVC\Symfony\Security\HttpUtils;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Security related compiler pass.
 * Manipulates Symfony core security services to adapt them to eZ security needs.
 */
class SecurityPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!($container->hasDefinition('security.authentication.provider.dao') &&
              $container->hasDefinition('security.authentication.provider.rememberme') &&
              $container->hasDefinition('security.authentication.provider.anonymous'))) {
            return;
        }

        $configResolverRef = new Reference('ezpublish.config.resolver');
        $permissionResolverRef = new Reference(PermissionResolver::class);
        $userServiceRef = new Reference(UserService::class);

        // Override and inject the Repository in the authentication provider.
        // We need it for checking user credentials
        $daoAuthenticationProviderDef = $container->findDefinition('security.authentication.provider.dao');
        $daoAuthenticationProviderDef->setClass(RepositoryAuthenticationProvider::class);
        $daoAuthenticationProviderDef->addMethodCall(
            'setPermissionResolver',
            [$permissionResolverRef]
        );
        $daoAuthenticationProviderDef->addMethodCall(
            'setUserService',
            [$userServiceRef]
        );

        $rememberMeAuthenticationProviderDef = $container->findDefinition('security.authentication.provider.rememberme');
        $rememberMeAuthenticationProviderDef->setClass(RememberMeRepositoryAuthenticationProvider::class);
        $rememberMeAuthenticationProviderDef->addMethodCall(
            'setPermissionResolver',
            [$permissionResolverRef]
        );

        $anonymousAuthenticationProviderDef = $container->findDefinition('security.authentication.provider.anonymous');
        $anonymousAuthenticationProviderDef->setClass(AnonymousAuthenticationProvider::class);
        $anonymousAuthenticationProviderDef->addMethodCall(
            'setPermissionResolver',
            [$permissionResolverRef]
        );

        $anonymousAuthenticationProviderDef->addMethodCall(
            'setConfigResolver',
            [$configResolverRef]
        );

        if (!$container->hasDefinition('security.http_utils')) {
            return;
        }

        $httpUtilsDef = $container->findDefinition('security.http_utils');
        $httpUtilsDef->setClass(HttpUtils::class);
        $httpUtilsDef->addMethodCall(
            'setSiteAccess',
            [new Reference('ezpublish.siteaccess')]
        );

        if (!$container->hasDefinition('security.authentication.success_handler')) {
            return;
        }

        $successHandlerDef = $container->getDefinition('security.authentication.success_handler');
        $successHandlerDef->setClass(DefaultAuthenticationSuccessHandler::class);
        $successHandlerDef->addMethodCall(
            'setConfigResolver',
            [$configResolverRef]
        );
    }
}

class_alias(SecurityPass::class, 'eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Compiler\SecurityPass');
