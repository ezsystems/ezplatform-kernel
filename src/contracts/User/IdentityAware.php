<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Contracts\Core\User;

/**
 * Interface for "user identity-aware" services.
 */
interface IdentityAware
{
    public function setIdentity(Identity $identity);
}

class_alias(IdentityAware::class, 'eZ\Publish\SPI\User\IdentityAware');
