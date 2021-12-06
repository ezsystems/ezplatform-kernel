<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Events\URL;

use Ibexa\Contracts\Core\Repository\Event\BeforeEvent;
use Ibexa\Contracts\Core\Repository\Values\URL\URL;
use Ibexa\Contracts\Core\Repository\Values\URL\URLUpdateStruct;
use UnexpectedValueException;

final class BeforeUpdateUrlEvent extends BeforeEvent
{
    /** @var \Ibexa\Contracts\Core\Repository\Values\URL\URL */
    private $url;

    /** @var \Ibexa\Contracts\Core\Repository\Values\URL\URLUpdateStruct */
    private $struct;

    /** @var \Ibexa\Contracts\Core\Repository\Values\URL\URL|null */
    private $updatedUrl;

    public function __construct(URL $url, URLUpdateStruct $struct)
    {
        $this->url = $url;
        $this->struct = $struct;
    }

    public function getUrl(): URL
    {
        return $this->url;
    }

    public function getStruct(): URLUpdateStruct
    {
        return $this->struct;
    }

    public function getUpdatedUrl(): URL
    {
        if (!$this->hasUpdatedUrl()) {
            throw new UnexpectedValueException(sprintf('Return value is not set or not of type %s. Check hasUpdatedUrl() or set it using setUpdatedUrl() before you call the getter.', URL::class));
        }

        return $this->updatedUrl;
    }

    public function setUpdatedUrl(?URL $updatedUrl): void
    {
        $this->updatedUrl = $updatedUrl;
    }

    public function hasUpdatedUrl(): bool
    {
        return $this->updatedUrl instanceof URL;
    }
}

class_alias(BeforeUpdateUrlEvent::class, 'eZ\Publish\API\Repository\Events\URL\BeforeUpdateUrlEvent');
