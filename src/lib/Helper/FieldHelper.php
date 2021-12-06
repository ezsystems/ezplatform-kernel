<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\Helper;

use Ibexa\Contracts\Core\Repository\ContentTypeService;
use Ibexa\Contracts\Core\Repository\FieldTypeService;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;

class FieldHelper
{
    /** @var \Ibexa\Contracts\Core\Repository\ContentTypeService */
    private $contentTypeService;

    /** @var \Ibexa\Contracts\Core\Repository\FieldTypeService */
    private $fieldTypeService;

    /** @var TranslationHelper */
    private $translationHelper;

    public function __construct(TranslationHelper $translationHelper, ContentTypeService $contentTypeService, FieldTypeService $fieldTypeService)
    {
        $this->fieldTypeService = $fieldTypeService;
        $this->contentTypeService = $contentTypeService;
        $this->translationHelper = $translationHelper;
    }

    /**
     * Checks if provided field can be considered empty.
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Content $content
     * @param string $fieldDefIdentifier
     * @param null $forcedLanguage
     *
     * @return bool
     */
    public function isFieldEmpty(Content $content, $fieldDefIdentifier, $forcedLanguage = null)
    {
        $field = $this->translationHelper->getTranslatedField($content, $fieldDefIdentifier, $forcedLanguage);
        $fieldDefinition = $content->getContentType()->getFieldDefinition($fieldDefIdentifier);

        return $this
            ->fieldTypeService
            ->getFieldType($fieldDefinition->fieldTypeIdentifier)
            ->isEmptyValue($field->value);
    }

    /**
     * Returns FieldDefinition object based on $contentInfo and $fieldDefIdentifier.
     *
     * @deprecated If you have Content you can instead do: $content->getContentType()->getFieldDefinition($identifier)
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo $contentInfo
     * @param string $fieldDefIdentifier
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition
     */
    public function getFieldDefinition(ContentInfo $contentInfo, $fieldDefIdentifier)
    {
        return $this
            ->contentTypeService
            ->loadContentType($contentInfo->contentTypeId)
            ->getFieldDefinition($fieldDefIdentifier);
    }
}

class_alias(FieldHelper::class, 'eZ\Publish\Core\Helper\FieldHelper');
