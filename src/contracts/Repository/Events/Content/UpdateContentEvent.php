<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Events\Content;

use Ibexa\Contracts\Core\Repository\Event\AfterEvent;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentUpdateStruct;
use Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo;

final class UpdateContentEvent extends AfterEvent
{
    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Content */
    private $content;

    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo */
    private $versionInfo;

    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\ContentUpdateStruct */
    private $contentUpdateStruct;

    /** @var string[]|null */
    private $fieldIdentifiersToValidate;

    public function __construct(
        Content $content,
        VersionInfo $versionInfo,
        ContentUpdateStruct $contentUpdateStruct,
        ?array $fieldIdentifiersToValidate = null
    ) {
        $this->content = $content;
        $this->versionInfo = $versionInfo;
        $this->contentUpdateStruct = $contentUpdateStruct;
        $this->fieldIdentifiersToValidate = $fieldIdentifiersToValidate;
    }

    public function getContent(): Content
    {
        return $this->content;
    }

    public function getVersionInfo(): VersionInfo
    {
        return $this->versionInfo;
    }

    public function getContentUpdateStruct(): ContentUpdateStruct
    {
        return $this->contentUpdateStruct;
    }

    /**
     * @return string[]|null
     */
    public function getFieldIdentifiersToValidate(): ?array
    {
        return $this->fieldIdentifiersToValidate;
    }
}

class_alias(UpdateContentEvent::class, 'eZ\Publish\API\Repository\Events\Content\UpdateContentEvent');
