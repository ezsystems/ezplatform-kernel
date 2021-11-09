<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Values\User;

/**
 * This class represents a draft of a role.
 *
 * @property-read \Ibexa\Contracts\Core\Repository\Values\User\PolicyDraft[] $policies
 */
abstract class RoleDraft extends Role
{
}

class_alias(RoleDraft::class, 'eZ\Publish\API\Repository\Values\User\RoleDraft');
