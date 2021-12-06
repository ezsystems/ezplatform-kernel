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
final class VersionTranslationUpdateEvaluator implements VersionTargetEvaluator
{
    public function accept(Target\Version $targetVersion): bool
    {
        return
            !empty($targetVersion->forUpdateLanguageCodesList)
            || null !== $targetVersion->forUpdateInitialLanguageCode;
    }

    public function evaluate(Target\Version $targetVersion, Limitation $limitationValue): ?bool
    {
        $accessVote = LanguageLimitationType::ACCESS_ABSTAIN;

        if (!empty($targetVersion->forUpdateLanguageCodesList)) {
            $diff = array_diff(
                $targetVersion->forUpdateLanguageCodesList,
                $limitationValue->limitationValues
            );
            $accessVote = empty($diff)
                ? LanguageLimitationType::ACCESS_GRANTED
                : LanguageLimitationType::ACCESS_DENIED;
        }

        if (
            $accessVote !== LanguageLimitationType::ACCESS_DENIED
            && null !== $targetVersion->forUpdateInitialLanguageCode
        ) {
            $accessVote = in_array(
                $targetVersion->forUpdateInitialLanguageCode,
                $limitationValue->limitationValues
            )
                ? LanguageLimitationType::ACCESS_GRANTED
                : LanguageLimitationType::ACCESS_DENIED;
        }

        return $accessVote;
    }
}

class_alias(VersionTranslationUpdateEvaluator::class, 'eZ\Publish\Core\Limitation\LanguageLimitation\VersionTranslationUpdateEvaluator');
