<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Events\UserPreference;

use Ibexa\Contracts\Core\Repository\Event\BeforeEvent;

final class BeforeSetUserPreferenceEvent extends BeforeEvent
{
    /** @var \Ibexa\Contracts\Core\Repository\Values\UserPreference\UserPreferenceSetStruct[] */
    private $userPreferenceSetStructs;

    public function __construct(array $userPreferenceSetStructs)
    {
        $this->userPreferenceSetStructs = $userPreferenceSetStructs;
    }

    public function getUserPreferenceSetStructs(): array
    {
        return $this->userPreferenceSetStructs;
    }
}

class_alias(BeforeSetUserPreferenceEvent::class, 'eZ\Publish\API\Repository\Events\UserPreference\BeforeSetUserPreferenceEvent');
