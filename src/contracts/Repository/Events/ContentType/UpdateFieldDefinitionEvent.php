<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Events\ContentType;

use Ibexa\Contracts\Core\Repository\Event\AfterEvent;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeDraft;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinitionUpdateStruct;

final class UpdateFieldDefinitionEvent extends AfterEvent
{
    /** @var \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeDraft */
    private $contentTypeDraft;

    /** @var \Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition */
    private $fieldDefinition;

    /** @var \Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinitionUpdateStruct */
    private $fieldDefinitionUpdateStruct;

    public function __construct(
        ContentTypeDraft $contentTypeDraft,
        FieldDefinition $fieldDefinition,
        FieldDefinitionUpdateStruct $fieldDefinitionUpdateStruct
    ) {
        $this->contentTypeDraft = $contentTypeDraft;
        $this->fieldDefinition = $fieldDefinition;
        $this->fieldDefinitionUpdateStruct = $fieldDefinitionUpdateStruct;
    }

    public function getContentTypeDraft(): ContentTypeDraft
    {
        return $this->contentTypeDraft;
    }

    public function getFieldDefinition(): FieldDefinition
    {
        return $this->fieldDefinition;
    }

    public function getFieldDefinitionUpdateStruct(): FieldDefinitionUpdateStruct
    {
        return $this->fieldDefinitionUpdateStruct;
    }
}

class_alias(UpdateFieldDefinitionEvent::class, 'eZ\Publish\API\Repository\Events\ContentType\UpdateFieldDefinitionEvent');
