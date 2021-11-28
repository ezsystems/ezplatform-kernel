<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Events\Content;

use Ibexa\Contracts\Core\Repository\Event\AfterEvent;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentCreateStruct;

final class CreateContentEvent extends AfterEvent
{
    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\ContentCreateStruct */
    private $contentCreateStruct;

    /** @var array */
    private $locationCreateStructs;

    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Content */
    private $content;

    /** @var string[]|null */
    private $fieldIdentifiersToValidate;

    public function __construct(
        Content $content,
        ContentCreateStruct $contentCreateStruct,
        array $locationCreateStructs,
        ?array $fieldIdentifiersToValidate = null
    ) {
        $this->content = $content;
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

    public function getContent(): Content
    {
        return $this->content;
    }

    /**
     * @return string[]|null
     */
    public function getFieldIdentifiersToValidate(): ?array
    {
        return $this->fieldIdentifiersToValidate;
    }
}

class_alias(CreateContentEvent::class, 'eZ\Publish\API\Repository\Events\Content\CreateContentEvent');
