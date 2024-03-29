<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace eZ\Publish\API\Repository\Events\URLWildcard;

use eZ\Publish\API\Repository\Values\Content\URLWildcardTranslationResult;
use eZ\Publish\SPI\Repository\Event\AfterEvent;

final class TranslateEvent extends AfterEvent
{
    private $url;

    /** @var \eZ\Publish\API\Repository\Values\Content\URLWildcardTranslationResult */
    private $result;

    public function __construct(
        URLWildcardTranslationResult $result,
        $url
    ) {
        $this->url = $url;
        $this->result = $result;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function getResult(): URLWildcardTranslationResult
    {
        return $this->result;
    }
}
