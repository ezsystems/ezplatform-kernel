<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Bundle\Core\DependencyInjection\Configuration\Parser;

class FieldDefinitionSettingsTemplates extends Templates
{
    public const NODE_KEY = 'fielddefinition_settings_templates';
    public const INFO = 'Template settings for field definition settings rendered by the ibexa_render_field_definition_settings() Twig function';
    public const INFO_TEMPLATE_KEY = 'Template file where to find block definition to display field definition settings';
}

class_alias(FieldDefinitionSettingsTemplates::class, 'eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\Parser\FieldDefinitionSettingsTemplates');
