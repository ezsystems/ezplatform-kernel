<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Events\URLAlias;

use Ibexa\Contracts\Core\Repository\Event\AfterEvent;
use Ibexa\Contracts\Core\Repository\Values\Content\URLAlias;

final class CreateGlobalUrlAliasEvent extends AfterEvent
{
    private $resource;

    private $path;

    private $languageCode;

    private $forwarding;

    private $alwaysAvailable;

    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\URLAlias */
    private $urlAlias;

    public function __construct(
        URLAlias $urlAlias,
        $resource,
        $path,
        $languageCode,
        $forwarding,
        $alwaysAvailable
    ) {
        $this->resource = $resource;
        $this->path = $path;
        $this->languageCode = $languageCode;
        $this->forwarding = $forwarding;
        $this->alwaysAvailable = $alwaysAvailable;
        $this->urlAlias = $urlAlias;
    }

    public function getResource()
    {
        return $this->resource;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function getLanguageCode()
    {
        return $this->languageCode;
    }

    public function getForwarding()
    {
        return $this->forwarding;
    }

    public function getAlwaysAvailable()
    {
        return $this->alwaysAvailable;
    }

    public function getUrlAlias(): URLAlias
    {
        return $this->urlAlias;
    }
}

class_alias(CreateGlobalUrlAliasEvent::class, 'eZ\Publish\API\Repository\Events\URLAlias\CreateGlobalUrlAliasEvent');
