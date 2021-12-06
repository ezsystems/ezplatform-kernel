<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\MVC\Symfony\Templating\Twig\Extension;

use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Contracts\Core\Repository\Values\Content\Field;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Core\Base\Exceptions\InvalidArgumentType;
use Ibexa\Core\Helper\FieldHelper;
use Ibexa\Core\Helper\TranslationHelper;
use Psr\Log\LoggerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Twig content extension for eZ Publish specific usage.
 * Exposes helpers to play with public API objects.
 */
class ContentExtension extends AbstractExtension
{
    /** @var \Ibexa\Contracts\Core\Repository\Repository */
    protected $repository;

    /** @var \Ibexa\Core\Helper\TranslationHelper */
    protected $translationHelper;

    /** @var \Ibexa\Core\Helper\FieldHelper */
    protected $fieldHelper;

    /** @var \Psr\Log\LoggerInterface */
    protected $logger;

    public function __construct(
        Repository $repository,
        TranslationHelper $translationHelper,
        FieldHelper $fieldHelper,
        LoggerInterface $logger = null
    ) {
        $this->repository = $repository;
        $this->translationHelper = $translationHelper;
        $this->fieldHelper = $fieldHelper;
        $this->logger = $logger;
    }

    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array
     */
    public function getFunctions()
    {
        return [
            new TwigFunction(
                'ez_content_name',
                [$this, 'getTranslatedContentName'],
                [
                    'deprecated' => '4.0',
                    'alternative' => 'ibexa_content_name',
                ]
            ),
            new TwigFunction(
                'ibexa_content_name',
                [$this, 'getTranslatedContentName']
            ),
            new TwigFunction(
                'ez_field_value',
                [$this, 'getTranslatedFieldValue'],
                [
                    'deprecated' => '4.0',
                    'alternative' => 'ibexa_field_value',
                ]
            ),
            new TwigFunction(
                'ibexa_field_value',
                [$this, 'getTranslatedFieldValue']
            ),
            new TwigFunction(
                'ez_field',
                [$this, 'getTranslatedField'],
                [
                    'deprecated' => '4.0',
                    'alternative' => 'ibexa_field',
                ]
            ),
            new TwigFunction(
                'ibexa_field',
                [$this, 'getTranslatedField']
            ),
            new TwigFunction(
                'ez_field_is_empty',
                [$this, 'isFieldEmpty'],
                [
                    'deprecated' => '4.0',
                    'alternative' => 'ibexa_field_is_empty',
                ]
            ),
            new TwigFunction(
                'ibexa_field_is_empty',
                [$this, 'isFieldEmpty']
            ),
            new TwigFunction(
                'ez_field_name',
                [$this, 'getTranslatedFieldDefinitionName'],
                [
                    'deprecated' => '4.0',
                    'alternative' => 'ibexa_field_name',
                ]
            ),
            new TwigFunction(
                'ibexa_field_name',
                [$this, 'getTranslatedFieldDefinitionName']
            ),
            new TwigFunction(
                'ez_field_description',
                [$this, 'getTranslatedFieldDefinitionDescription'],
                [
                    'deprecated' => '4.0',
                    'alternative' => 'ibexa_field_description',
                ]
            ),
            new TwigFunction(
                'ibexa_field_description',
                [$this, 'getTranslatedFieldDefinitionDescription']
            ),
            new TwigFunction(
                'ez_content_field_identifier_first_filled_image',
                [$this, 'getFirstFilledImageFieldIdentifier'],
                [
                    'deprecated' => '4.0',
                    'alternative' => 'ibexa_content_field_identifier_first_filled_image',
                ]
            ),
            new TwigFunction(
                'ibexa_content_field_identifier_first_filled_image',
                [$this, 'getFirstFilledImageFieldIdentifier']
            ),
        ];
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\ValueObject $content Must be a valid Content or ContentInfo object.
     * @param string $forcedLanguage Locale we want the content name translation in (e.g. "fre-FR"). Null by default (takes current locale)
     *
     * @throws \Ibexa\Core\Base\Exceptions\InvalidArgumentType When $content is not a valid Content or ContentInfo object.
     *
     * @return string
     */
    public function getTranslatedContentName(ValueObject $content, $forcedLanguage = null)
    {
        if ($content instanceof Content) {
            return $this->translationHelper->getTranslatedContentName($content, $forcedLanguage);
        } elseif ($content instanceof ContentInfo) {
            return $this->translationHelper->getTranslatedContentNameByContentInfo($content, $forcedLanguage);
        }

        throw new InvalidArgumentType(
            '$content',
            sprintf('%s or %s', Content::class, ContentInfo::class),
            $content
        );
    }

    /**
     * Returns the translated field, very similar to getTranslatedFieldValue but this returns the whole field.
     * To be used with ibexa_image_alias for example, which requires the whole field.
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Content $content
     * @param string $fieldDefIdentifier Identifier for the field we want to get.
     * @param string $forcedLanguage Locale we want the field in (e.g. "cro-HR"). Null by default (takes current locale).
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\Field
     */
    public function getTranslatedField(Content $content, $fieldDefIdentifier, $forcedLanguage = null)
    {
        return $this->translationHelper->getTranslatedField($content, $fieldDefIdentifier, $forcedLanguage);
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Content $content
     * @param string $fieldDefIdentifier Identifier for the field we want to get the value from.
     * @param string $forcedLanguage Locale we want the content name translation in (e.g. "fre-FR"). Null by default (takes current locale).
     *
     * @return mixed A primitive type or a field type Value object depending on the field type.
     */
    public function getTranslatedFieldValue(Content $content, $fieldDefIdentifier, $forcedLanguage = null)
    {
        return $this->translationHelper->getTranslatedField($content, $fieldDefIdentifier, $forcedLanguage)->value;
    }

    /**
     * Gets name of a FieldDefinition name by loading ContentType based on Content/ContentInfo object.
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\ValueObject $content Must be Content or ContentInfo object
     * @param string $fieldDefIdentifier Identifier for the field we want to get the name from
     * @param string $forcedLanguage Locale we want the content name translation in (e.g. "fre-FR"). Null by default (takes current locale)
     *
     * @throws \Ibexa\Core\Base\Exceptions\InvalidArgumentType When $content is not a valid Content object.
     *
     * @return string|null
     */
    public function getTranslatedFieldDefinitionName(ValueObject $content, $fieldDefIdentifier, $forcedLanguage = null)
    {
        if ($contentType = $this->getContentType($content)) {
            return $this->translationHelper->getTranslatedFieldDefinitionProperty(
                $contentType,
                $fieldDefIdentifier,
                'name',
                $forcedLanguage
            );
        }

        throw new InvalidArgumentType('$content', 'Content|ContentInfo', $content);
    }

    /**
     * Gets name of a FieldDefinition description by loading ContentType based on Content/ContentInfo object.
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\ValueObject $content Must be Content or ContentInfo object
     * @param string $fieldDefIdentifier Identifier for the field we want to get the name from
     * @param string $forcedLanguage Locale we want the content name translation in (e.g. "fre-FR"). Null by default (takes current locale)
     *
     * @throws \Ibexa\Core\Base\Exceptions\InvalidArgumentType When $content is not a valid Content object.
     *
     * @return string|null
     */
    public function getTranslatedFieldDefinitionDescription(ValueObject $content, $fieldDefIdentifier, $forcedLanguage = null)
    {
        if ($contentType = $this->getContentType($content)) {
            return $this->translationHelper->getTranslatedFieldDefinitionProperty(
                $contentType,
                $fieldDefIdentifier,
                'description',
                $forcedLanguage
            );
        }

        throw new InvalidArgumentType('$content', 'Content|ContentInfo', $content);
    }

    /**
     * Checks if a given field is considered empty.
     * This method accepts field as Objects or by identifiers.
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Content $content
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Field|string $fieldDefIdentifier Field or Field Identifier to
     *                                                                                   get the value from.
     * @param string $forcedLanguage Locale we want the content name translation in (e.g. "fre-FR").
     *                               Null by default (takes current locale).
     *
     * @return bool
     */
    public function isFieldEmpty(Content $content, $fieldDefIdentifier, $forcedLanguage = null)
    {
        if ($fieldDefIdentifier instanceof Field) {
            $fieldDefIdentifier = $fieldDefIdentifier->fieldDefIdentifier;
        }

        return $this->fieldHelper->isFieldEmpty($content, $fieldDefIdentifier, $forcedLanguage);
    }

    /**
     * Get ContentType by Content/ContentInfo.
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Content|\Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo $content
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType|null
     */
    private function getContentType(ValueObject $content)
    {
        if ($content instanceof Content) {
            return $this->repository->getContentTypeService()->loadContentType(
                $content->getVersionInfo()->getContentInfo()->contentTypeId
            );
        } elseif ($content instanceof ContentInfo) {
            return $this->repository->getContentTypeService()->loadContentType($content->contentTypeId);
        }
    }

    public function getFirstFilledImageFieldIdentifier(Content $content)
    {
        foreach ($content->getFieldsByLanguage() as $field) {
            $fieldTypeIdentifier = $content->getContentType()
                ->getFieldDefinition($field->fieldDefIdentifier)
                ->fieldTypeIdentifier;

            if ($fieldTypeIdentifier !== 'ezimage') {
                continue;
            }

            if ($this->fieldHelper->isFieldEmpty($content, $field->fieldDefIdentifier)) {
                continue;
            }

            return $field->fieldDefIdentifier;
        }

        return null;
    }
}

class_alias(ContentExtension::class, 'eZ\Publish\Core\MVC\Symfony\Templating\Twig\Extension\ContentExtension');
