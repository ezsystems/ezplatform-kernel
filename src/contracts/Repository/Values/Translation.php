<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Values;

/**
 * Base class fro translation messages.
 *
 * Use its extensions: Translation\Singular, Translation\Plural.
 */
abstract class Translation extends ValueObject
{
}

class_alias(Translation::class, 'eZ\Publish\API\Repository\Values\Translation');
