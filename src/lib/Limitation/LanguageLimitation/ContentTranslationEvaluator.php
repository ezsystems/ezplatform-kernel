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
final class ContentTranslationEvaluator implements VersionTargetEvaluator
{
    public function accept(Target\Version $targetVersion): bool
    {
        return !empty($targetVersion->allLanguageCodesList);
    }

    /**
     * Allow access if any of the given language codes for translations matches any of the limitation values.
     */
    public function evaluate(Target\Version $targetVersion, Limitation $limitationValue): ?bool
    {
        $matchingTranslations = array_intersect(
            $targetVersion->allLanguageCodesList,
            $limitationValue->limitationValues
        );

        return empty($matchingTranslations)
            ? LanguageLimitationType::ACCESS_DENIED
            : LanguageLimitationType::ACCESS_GRANTED;
    }
}

class_alias(ContentTranslationEvaluator::class, 'eZ\Publish\Core\Limitation\LanguageLimitation\ContentTranslationEvaluator');
