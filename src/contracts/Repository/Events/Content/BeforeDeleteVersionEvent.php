<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Events\Content;

use Ibexa\Contracts\Core\Repository\Event\BeforeEvent;
use Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo;

final class BeforeDeleteVersionEvent extends BeforeEvent
{
    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo */
    private $versionInfo;

    public function __construct(VersionInfo $versionInfo)
    {
        $this->versionInfo = $versionInfo;
    }

    public function getVersionInfo(): VersionInfo
    {
        return $this->versionInfo;
    }
}

class_alias(BeforeDeleteVersionEvent::class, 'eZ\Publish\API\Repository\Events\Content\BeforeDeleteVersionEvent');
