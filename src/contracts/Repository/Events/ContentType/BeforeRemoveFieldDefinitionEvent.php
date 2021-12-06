<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Events\ContentType;

use Ibexa\Contracts\Core\Repository\Event\BeforeEvent;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeDraft;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;

final class BeforeRemoveFieldDefinitionEvent extends BeforeEvent
{
    /** @var \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeDraft */
    private $contentTypeDraft;

    /** @var \Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition */
    private $fieldDefinition;

    public function __construct(ContentTypeDraft $contentTypeDraft, FieldDefinition $fieldDefinition)
    {
        $this->contentTypeDraft = $contentTypeDraft;
        $this->fieldDefinition = $fieldDefinition;
    }

    public function getContentTypeDraft(): ContentTypeDraft
    {
        return $this->contentTypeDraft;
    }

    public function getFieldDefinition(): FieldDefinition
    {
        return $this->fieldDefinition;
    }
}

class_alias(BeforeRemoveFieldDefinitionEvent::class, 'eZ\Publish\API\Repository\Events\ContentType\BeforeRemoveFieldDefinitionEvent');
