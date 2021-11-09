<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Values\User;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;

/**
 * Context of the password validation.
 *
 * @property-read \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType|null $contentType
 * @property-read \Ibexa\Contracts\Core\Repository\Values\User\User|null $user
 */
class PasswordValidationContext extends ValueObject
{
    /**
     * Content type of the password owner.
     *
     * @var \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType|null
     */
    protected $contentType;

    /**
     * Owner of the password.
     *
     * @var \Ibexa\Contracts\Core\Repository\Values\User\User|null
     */
    protected $user;
}

class_alias(PasswordValidationContext::class, 'eZ\Publish\API\Repository\Values\User\PasswordValidationContext');
