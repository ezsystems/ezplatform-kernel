<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Events\URLWildcard;

use Ibexa\Contracts\Core\Repository\Event\BeforeEvent;
use Ibexa\Contracts\Core\Repository\Values\Content\URLWildcardTranslationResult;
use UnexpectedValueException;

final class BeforeTranslateEvent extends BeforeEvent
{
    private $url;

    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\URLWildcardTranslationResult|null */
    private $result;

    public function __construct($url)
    {
        $this->url = $url;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function getResult(): URLWildcardTranslationResult
    {
        if (!$this->hasResult()) {
            throw new UnexpectedValueException(sprintf('Return value is not set or not of type %s. Check hasResult() or set it using setResult() before you call the getter.', URLWildcardTranslationResult::class));
        }

        return $this->result;
    }

    public function setResult(?URLWildcardTranslationResult $result): void
    {
        $this->result = $result;
    }

    public function hasResult(): bool
    {
        return $this->result instanceof URLWildcardTranslationResult;
    }
}

class_alias(BeforeTranslateEvent::class, 'eZ\Publish\API\Repository\Events\URLWildcard\BeforeTranslateEvent');
