<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Bundle\Core\DependencyInjection\Configuration\Parser;

class ContentView extends View
{
    public const NODE_KEY = 'content_view';
    public const INFO = 'Template selection settings when displaying a content';
}

class_alias(ContentView::class, 'eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\Parser\ContentView');
