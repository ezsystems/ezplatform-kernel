<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace eZ\Publish\API\Repository\Events\Content;

use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\ContentCreateStruct;
use eZ\Publish\SPI\Repository\Event\BeforeEvent;
use UnexpectedValueException;

final class BeforeCreateContentEvent extends BeforeEvent
{
    /** @var \eZ\Publish\API\Repository\Values\Content\ContentCreateStruct */
    private $contentCreateStruct;

    /** @var array */
    private $locationCreateStructs;

    /** @var \eZ\Publish\API\Repository\Values\Content\Content|null */
    private $content;

    /** @var string[]|null */
    private $fieldIdentifiersToValidate;

    public function __construct(
        ContentCreateStruct $contentCreateStruct,
        array $locationCreateStructs,
        ?array $fieldIdentifiersToValidate = null
    ) {
        $this->contentCreateStruct = $contentCreateStruct;
        $this->locationCreateStructs = $locationCreateStructs;
        $this->fieldIdentifiersToValidate = $fieldIdentifiersToValidate;
    }

    public function getContentCreateStruct(): ContentCreateStruct
    {
        return $this->contentCreateStruct;
    }

    public function getLocationCreateStructs(): array
    {
        return $this->locationCreateStructs;
    }

    /**
     * @return string[]|null
     */
    public function getFieldIdentifiersToValidate(): ?array
    {
        return $this->fieldIdentifiersToValidate;
    }

    public function getContent(): Content
    {
        if (!$this->hasContent()) {
            throw new UnexpectedValueException(sprintf('Return value is not set or not of type %s. Check hasContent() or set it using setContent() before you call the getter.', Content::class));
        }

        return $this->content;
    }

    public function setContent(?Content $content): void
    {
        $this->content = $content;
    }

    public function hasContent(): bool
    {
        return $this->content instanceof Content;
    }
}
