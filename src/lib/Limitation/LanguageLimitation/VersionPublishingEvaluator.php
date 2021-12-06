<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\Limitation\LanguageLimitation;

use Ibexa\Contracts\Core\Limitation\Target;
use Ibexa\Contracts\Core\Repository\Values\User\Limitation;
use Ibexa\Core\Limitation\LanguageLimitationType;

/**
 * @internal for internal use by LanguageLimitation
 */
final class VersionPublishingEvaluator implements VersionTargetEvaluator
{
    public function accept(Target\Version $targetVersion): bool
    {
        return !empty($targetVersion->forPublishLanguageCodesList);
    }

    /**
     * Evaluate publishing a specific translation of a Version.
     */
    public function evaluate(Target\Version $targetVersion, Limitation $limitationValue): ?bool
    {
        $diff = array_diff(
            $targetVersion->forPublishLanguageCodesList,
            $limitationValue->limitationValues
        );

        return empty($diff)
            ? LanguageLimitationType::ACCESS_GRANTED
            : LanguageLimitationType::ACCESS_DENIED;
    }
}

class_alias(VersionPublishingEvaluator::class, 'eZ\Publish\Core\Limitation\LanguageLimitation\VersionPublishingEvaluator');
