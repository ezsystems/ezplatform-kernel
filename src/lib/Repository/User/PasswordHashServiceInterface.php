<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\Repository\User;

use Ibexa\Contracts\Core\Repository\PasswordHashService;

/**
 * @deprecated since Ibexa 3.3.0, to be removed in Ibexa 4.0.0. Use
 * {@see \Ibexa\Contracts\Core\Repository\PasswordHashService} directly instead.
 */
interface PasswordHashServiceInterface extends PasswordHashService
{
}

class_alias(PasswordHashServiceInterface::class, 'eZ\Publish\Core\Repository\User\PasswordHashServiceInterface');
