<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\MVC\Symfony\Security\Authorization\Voter;

use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Core\MVC\Symfony\Security\Authorization\Attribute as AuthorizationAttribute;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

/**
 * Voter to test access to a ValueObject from Repository (e.g. Content, Location...).
 */
class ValueObjectVoter implements VoterInterface
{
    /** @var \Ibexa\Contracts\Core\Repository\PermissionResolver */
    private $permissionResolver;

    public function __construct(PermissionResolver $permissionResolver)
    {
        $this->permissionResolver = $permissionResolver;
    }

    public function supportsAttribute($attribute)
    {
        return $attribute instanceof AuthorizationAttribute && isset($attribute->limitations['valueObject']);
    }

    public function supportsClass($class)
    {
        return true;
    }

    /**
     * Returns the vote for the given parameters.
     * Checks if user has access to a given action on a given value object.
     *
     * $attributes->limitations is a hash that contains:
     *  - 'valueObject' - The {@see \Ibexa\Contracts\Core\Repository\Values\ValueObject} to check access on. e.g. Location or Content.
     *  - 'targets' - The location, parent or "assignment" value object, or an array of the same.
     *
     * This method must return one of the following constants:
     * ACCESS_GRANTED, ACCESS_DENIED, or ACCESS_ABSTAIN.
     *
     * @see \Ibexa\Contracts\Core\Repository\PermissionResolver::canUser()
     *
     * @param \Symfony\Component\Security\Core\Authentication\Token\TokenInterface $token      A TokenInterface instance
     * @param object         $object     The object to secure
     * @param array          $attributes An array of attributes associated with the method being invoked
     *
     * @return int either ACCESS_GRANTED, ACCESS_ABSTAIN, or ACCESS_DENIED
     */
    public function vote(TokenInterface $token, $object, array $attributes)
    {
        foreach ($attributes as $attribute) {
            if ($this->supportsAttribute($attribute)) {
                $targets = isset($attribute->limitations['targets']) ? $attribute->limitations['targets'] : [];
                if (
                    $this->permissionResolver->canUser(
                        $attribute->module,
                        $attribute->function,
                        $attribute->limitations['valueObject'],
                        $targets
                    ) === false
                ) {
                    return VoterInterface::ACCESS_DENIED;
                }

                return VoterInterface::ACCESS_GRANTED;
            }
        }

        return VoterInterface::ACCESS_ABSTAIN;
    }
}

class_alias(ValueObjectVoter::class, 'eZ\Publish\Core\MVC\Symfony\Security\Authorization\Voter\ValueObjectVoter');
