<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Bundle\Core\DependencyInjection\Configuration\Parser;

class FieldEditTemplates extends Templates
{
    public const NODE_KEY = 'field_edit_templates';
    public const INFO = 'Settings for field edit templates';
    public const INFO_TEMPLATE_KEY = 'Template file where to find block definition to display fields';
}

class_alias(FieldEditTemplates::class, 'eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\Parser\FieldEditTemplates');
