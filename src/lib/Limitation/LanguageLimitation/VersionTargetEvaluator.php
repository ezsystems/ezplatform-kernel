<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\Limitation\LanguageLimitation;

use Ibexa\Contracts\Core\Limitation\Target;
use Ibexa\Contracts\Core\Repository\Values\User\Limitation;

/**
 * @internal for internal use by LanguageLimitation
 */
interface VersionTargetEvaluator
{
    public function accept(Target\Version $targetVersion): bool;

    public function evaluate(Target\Version $targetVersion, Limitation $limitationValue): ?bool;
}

class_alias(VersionTargetEvaluator::class, 'eZ\Publish\Core\Limitation\LanguageLimitation\VersionTargetEvaluator');
