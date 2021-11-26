<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\Parser;

class FieldDefinitionEditTemplates extends Templates
{
    public const NODE_KEY = 'fielddefinition_edit_templates';
    public const INFO = 'Settings for field definition templates';
    public const INFO_TEMPLATE_KEY = 'Template file where to find block definition to display field definition settings';
}
