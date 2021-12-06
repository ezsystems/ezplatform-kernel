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
final class ContentDeleteEvaluator implements VersionTargetEvaluator
{
    public function accept(Target\Version $targetVersion): bool
    {
        return !empty($targetVersion->getTranslationsToDelete());
    }

    /**
     * Allow access if all of the given language codes for content matches limitation values.
     */
    public function evaluate(Target\Version $targetVersion, Limitation $limitationValue): ?bool
    {
        $diff = array_diff(
            $targetVersion->getTranslationsToDelete(),
            $limitationValue->limitationValues
        );
        $accessVote = empty($diff)
            ? LanguageLimitationType::ACCESS_GRANTED
            : LanguageLimitationType::ACCESS_DENIED;

        return $accessVote;
    }
}

class_alias(ContentDeleteEvaluator::class, 'eZ\Publish\Core\Limitation\LanguageLimitation\ContentDeleteEvaluator');
