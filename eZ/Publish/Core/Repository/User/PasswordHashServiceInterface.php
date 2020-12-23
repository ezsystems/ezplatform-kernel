<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace eZ\Publish\Core\Repository\User;

use eZ\Publish\API\Repository\PasswordHashService;

/**
 * @deprecated since eZ Platform 3.3.0, to be removed in eZ Platform 4.0.0. Use
 * \eZ\Publish\API\Repository\PasswordHashService directly instead.
 */
interface PasswordHashServiceInterface extends PasswordHashService
{
}
