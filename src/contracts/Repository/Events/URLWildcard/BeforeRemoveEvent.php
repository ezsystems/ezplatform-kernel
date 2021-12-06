<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Events\URLWildcard;

use Ibexa\Contracts\Core\Repository\Event\BeforeEvent;
use Ibexa\Contracts\Core\Repository\Values\Content\URLWildcard;

final class BeforeRemoveEvent extends BeforeEvent
{
    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\URLWildcard */
    private $urlWildcard;

    public function __construct(URLWildcard $urlWildcard)
    {
        $this->urlWildcard = $urlWildcard;
    }

    public function getUrlWildcard(): URLWildcard
    {
        return $this->urlWildcard;
    }
}

class_alias(BeforeRemoveEvent::class, 'eZ\Publish\API\Repository\Events\URLWildcard\BeforeRemoveEvent');
