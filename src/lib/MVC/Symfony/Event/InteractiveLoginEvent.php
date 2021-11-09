<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\MVC\Symfony\Event;

use Ibexa\Contracts\Core\Repository\Values\User\User as APIUser;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Contracts\EventDispatcher\Event;

class InteractiveLoginEvent extends Event
{
    /** @var \Ibexa\Contracts\Core\Repository\Values\User\User */
    private $apiUser;

    /** @var \Symfony\Component\HttpFoundation\Request */
    private $request;

    /** @var \Symfony\Component\Security\Core\Authentication\Token\TokenInterface */
    private $authenticationToken;

    public function __construct(Request $request, TokenInterface $authenticationToken)
    {
        $this->request = $request;
        $this->authenticationToken = $authenticationToken;
    }

    public function getRequest(): Request
    {
        return $this->request;
    }

    public function getAuthenticationToken(): TokenInterface
    {
        return $this->authenticationToken;
    }

    /**
     * Checks if an API user has been provided.
     *
     * @return bool
     */
    public function hasAPIUser(): bool
    {
        return isset($this->apiUser);
    }

    /**
     * Injects an API user to be injected in the repository.
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\User\User $apiUser
     */
    public function setApiUser(APIUser $apiUser): void
    {
        $this->apiUser = $apiUser;
    }

    /**
     * @return \Ibexa\Contracts\Core\Repository\Values\User\User
     */
    public function getAPIUser(): APIUser
    {
        return $this->apiUser;
    }
}

class_alias(InteractiveLoginEvent::class, 'eZ\Publish\Core\MVC\Symfony\Event\InteractiveLoginEvent');
