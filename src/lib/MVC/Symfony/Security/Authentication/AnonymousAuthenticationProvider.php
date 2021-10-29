<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\MVC\Symfony\Security\Authentication;

use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Core\MVC\ConfigResolverInterface;
use Ibexa\Core\Repository\Values\User\UserReference;
use Symfony\Component\Security\Core\Authentication\Provider\AnonymousAuthenticationProvider as BaseAnonymousProvider;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class AnonymousAuthenticationProvider extends BaseAnonymousProvider
{
    /** @var \Ibexa\Core\MVC\ConfigResolverInterface */
    private $configResolver;

    /** @var \Ibexa\Contracts\Core\Repository\PermissionResolver */
    private $permissionResolver;

    public function setConfigResolver(ConfigResolverInterface $configResolver)
    {
        $this->configResolver = $configResolver;
    }

    public function setPermissionResolver(PermissionResolver $permissionResolver)
    {
        $this->permissionResolver = $permissionResolver;
    }

    public function authenticate(TokenInterface $token)
    {
        $token = parent::authenticate($token);
        $this->permissionResolver->setCurrentUserReference(new UserReference($this->configResolver->getParameter('anonymous_user_id')));

        return $token;
    }
}

class_alias(AnonymousAuthenticationProvider::class, 'eZ\Publish\Core\MVC\Symfony\Security\Authentication\AnonymousAuthenticationProvider');
