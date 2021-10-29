<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\MVC\Symfony\Security\Authentication;

use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Core\MVC\Symfony\Security\UserInterface;
use Symfony\Component\Security\Core\Authentication\Provider\RememberMeAuthenticationProvider;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class RememberMeRepositoryAuthenticationProvider extends RememberMeAuthenticationProvider
{
    /** @var \Ibexa\Contracts\Core\Repository\PermissionResolver */
    private $permissionResolver;

    public function setPermissionResolver(PermissionResolver $permissionResolver)
    {
        $this->permissionResolver = $permissionResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function authenticate(TokenInterface $token)
    {
        $authenticatedToken = parent::authenticate($token);
        if (empty($authenticatedToken)) {
            throw new AuthenticationException('The token is not supported by this authentication provider.');
        }

        if ($authenticatedToken->getUser() instanceof UserInterface) {
            $this->permissionResolver->setCurrentUserReference(
                $authenticatedToken->getUser()->getAPIUser()
            );
        }

        return $authenticatedToken;
    }
}

class_alias(RememberMeRepositoryAuthenticationProvider::class, 'eZ\Publish\Core\MVC\Symfony\Security\Authentication\RememberMeRepositoryAuthenticationProvider');
