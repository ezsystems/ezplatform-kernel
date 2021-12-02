<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\MVC\Symfony\Templating\Twig\Extension;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Field;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;
use Ibexa\Core\Helper\TranslationHelper;
use Ibexa\Core\MVC\Symfony\FieldType\View\ParameterProviderRegistryInterface;
use Ibexa\Core\MVC\Symfony\Templating\FieldBlockRendererInterface;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Twig extension for content fields/fieldDefinitions rendering (view and edit).
 */
class FieldRenderingExtension extends AbstractExtension
{
    /** @var \Ibexa\Core\MVC\Symfony\Templating\FieldBlockRendererInterface */
    private $fieldBlockRenderer;

    /** @var \Ibexa\Core\MVC\Symfony\FieldType\View\ParameterProviderRegistryInterface */
    private $parameterProviderRegistry;

    /** @var \Ibexa\Core\Helper\TranslationHelper */
    private $translationHelper;

    /**
     * Hash of field type identifiers (i.e. "ezstring"), indexed by field definition identifier.
     *
     * @var array
     */
    private $fieldTypeIdentifiers = [];

    public function __construct(
        FieldBlockRendererInterface $fieldBlockRenderer,
        ParameterProviderRegistryInterface $parameterProviderRegistry,
        TranslationHelper $translationHelper
    ) {
        $this->fieldBlockRenderer = $fieldBlockRenderer;
        $this->parameterProviderRegistry = $parameterProviderRegistry;
        $this->translationHelper = $translationHelper;
    }

    public function getFunctions()
    {
        $renderFieldCallable = function (Environment $environment, Content $content, $fieldIdentifier, array $params = []) {
            $this->fieldBlockRenderer->setTwig($environment);

            return $this->renderField($content, $fieldIdentifier, $params);
        };

        $renderFieldDefinitionSettingsCallable = function (Environment $environment, FieldDefinition $fieldDefinition, array $params = []) {
            $this->fieldBlockRenderer->setTwig($environment);

            return $this->renderFieldDefinitionSettings($fieldDefinition, $params);
        };

        return [
            new TwigFunction(
                'ez_render_field',
                $renderFieldCallable,
                [
                    'is_safe' => ['html'],
                    'needs_environment' => true,
                    'deprecated' => '4.0',
                    'alternative' => 'ibexa_render_field',
                ]
            ),
            new TwigFunction(
                'ibexa_render_field',
                $renderFieldCallable,
                ['is_safe' => ['html'], 'needs_environment' => true]
            ),
            new TwigFunction(
                'ez_render_field_definition_settings',
                $renderFieldDefinitionSettingsCallable,
                [
                    'is_safe' => ['html'],
                    'needs_environment' => true,
                    'deprecated' => '4.0',
                    'alternative' => 'ibexa_render_field_definition_settings',
                ]
            ),
            new TwigFunction(
                'ibexa_render_field_definition_settings',
                $renderFieldDefinitionSettingsCallable,
                ['is_safe' => ['html'], 'needs_environment' => true]
            ),
        ];
    }

    /**
     * Renders the HTML for the settings for the given field definition
     * $definition.
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition $fieldDefinition
     *
     * @return string
     */
    public function renderFieldDefinitionSettings(FieldDefinition $fieldDefinition, array $params = [])
    {
        return $this->fieldBlockRenderer->renderFieldDefinitionView($fieldDefinition, $params);
    }

    /**
     * Renders the HTML for a given field.
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Content $content
     * @param string $fieldIdentifier Identifier for the field we want to render
     * @param array $params An array of parameters to pass to the field view
     *
     * @return string The HTML markup
     *
     * @throws \Ibexa\Core\Base\Exceptions\InvalidArgumentException
     */
    public function renderField(Content $content, $fieldIdentifier, array $params = [])
    {
        $field = $this->translationHelper->getTranslatedField($content, $fieldIdentifier, isset($params['lang']) ? $params['lang'] : null);
        if (!$field instanceof Field) {
            throw new InvalidArgumentException(
                '$fieldIdentifier',
                "'{$fieldIdentifier}' Field does not exist in Content item {$content->contentInfo->id} '{$content->contentInfo->name}'"
            );
        }

        $params = $this->getRenderFieldBlockParameters($content, $field, $params);
        $fieldTypeIdentifier = $this->getFieldTypeIdentifier($content, $field);

        return $this->fieldBlockRenderer->renderContentFieldView($field, $fieldTypeIdentifier, $params);
    }

    /**
     * Generates the array of parameter to pass to the field template.
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Content $content
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Field $field the Field to display
     * @param array $params An array of parameters to pass to the field view
     *
     * @return array
     */
    private function getRenderFieldBlockParameters(Content $content, Field $field, array $params = [])
    {
        // Merging passed parameters to default ones
        $params += [
            'parameters' => [], // parameters dedicated to template processing
            'attr' => [], // attributes to add on the enclosing HTML tags
        ];

        $versionInfo = $content->getVersionInfo();
        $contentInfo = $versionInfo->getContentInfo();
        $contentType = $content->getContentType();
        $fieldDefinition = $contentType->getFieldDefinition($field->fieldDefIdentifier);
        // Adding Field, FieldSettings and ContentInfo objects to
        // parameters to be passed to the template
        $params += [
            'field' => $field,
            'content' => $content,
            'contentInfo' => $contentInfo,
            'versionInfo' => $versionInfo,
            'fieldSettings' => $fieldDefinition->getFieldSettings(),
        ];

        // Adding field type specific parameters if any.
        if ($this->parameterProviderRegistry->hasParameterProvider($fieldDefinition->fieldTypeIdentifier)) {
            $params['parameters'] += $this->parameterProviderRegistry
                ->getParameterProvider($fieldDefinition->fieldTypeIdentifier)
                ->getViewParameters($field);
        }

        // make sure we can easily add class="<fieldtypeidentifier>-field" to the
        // generated HTML
        if (isset($params['attr']['class'])) {
            $params['attr']['class'] .= ' ' . $this->getFieldTypeIdentifier($content, $field) . '-field';
        } else {
            $params['attr']['class'] = $this->getFieldTypeIdentifier($content, $field) . '-field';
        }

        return $params;
    }

    /**
     * Returns the field type identifier for $field.
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Content $content
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Field $field
     *
     * @return string
     */
    private function getFieldTypeIdentifier(Content $content, Field $field)
    {
        $contentType = $content->getContentType();
        $key = $contentType->id . '  ' . $field->fieldDefIdentifier;

        if (!isset($this->fieldTypeIdentifiers[$key])) {
            $this->fieldTypeIdentifiers[$key] = $contentType
                ->getFieldDefinition($field->fieldDefIdentifier)
                ->fieldTypeIdentifier;
        }

        return $this->fieldTypeIdentifiers[$key];
    }
}

class_alias(FieldRenderingExtension::class, 'eZ\Publish\Core\MVC\Symfony\Templating\Twig\Extension\FieldRenderingExtension');
