<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Bundle\Core\DependencyInjection\Configuration\Parser;

class FieldTemplates extends Templates
{
    public const NODE_KEY = 'field_templates';
    public const INFO = 'Template settings for fields rendered by the ibexa_render_field() Twig function';
    public const INFO_TEMPLATE_KEY = 'Template file where to find block definition to display fields';
}

class_alias(FieldTemplates::class, 'eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\Parser\FieldTemplates');
