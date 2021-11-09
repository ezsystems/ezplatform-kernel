<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Limitation;

/**
 * Marker interface for PermissionResolver::canUser $targets objects.
 *
 * It's aimed to provide Limitations with information about intent (result of an action) to evaluate.
 *
 * @see \Ibexa\Contracts\Core\Repository\PermissionResolver::canUser
 */
interface Target
{
}

class_alias(Target::class, 'eZ\Publish\SPI\Limitation\Target');
