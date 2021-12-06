<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Values\User\Limitation;

use Ibexa\Contracts\Core\Repository\Values\User\Limitation;

abstract class RoleLimitation extends Limitation
{
}

class_alias(RoleLimitation::class, 'eZ\Publish\API\Repository\Values\User\Limitation\RoleLimitation');
