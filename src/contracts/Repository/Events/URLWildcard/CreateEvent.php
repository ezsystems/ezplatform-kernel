<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Events\URLWildcard;

use Ibexa\Contracts\Core\Repository\Event\AfterEvent;
use Ibexa\Contracts\Core\Repository\Values\Content\URLWildcard;

final class CreateEvent extends AfterEvent
{
    private $sourceUrl;

    private $destinationUrl;

    private $forward;

    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\URLWildcard */
    private $urlWildcard;

    public function __construct(
        URLWildcard $urlWildcard,
        $sourceUrl,
        $destinationUrl,
        $forward
    ) {
        $this->sourceUrl = $sourceUrl;
        $this->destinationUrl = $destinationUrl;
        $this->forward = $forward;
        $this->urlWildcard = $urlWildcard;
    }

    public function getSourceUrl()
    {
        return $this->sourceUrl;
    }

    public function getDestinationUrl()
    {
        return $this->destinationUrl;
    }

    public function getForward()
    {
        return $this->forward;
    }

    public function getUrlWildcard(): URLWildcard
    {
        return $this->urlWildcard;
    }
}

class_alias(CreateEvent::class, 'eZ\Publish\API\Repository\Events\URLWildcard\CreateEvent');
