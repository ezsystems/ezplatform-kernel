<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Bundle\Core\DependencyInjection\Compiler;

use Ibexa\Core\MVC\Symfony\View\ContentView;

/**
 * The ContentViewPass adds DIC compiler pass related to content view.
 * This includes adding ContentViewProvider implementations.
 *
 * @see \Ibexa\Core\MVC\Symfony\View\Manager
 * @deprecated since 6.0
 */
class ContentViewPass extends ViewManagerPass
{
    const VIEW_PROVIDER_IDENTIFIER = 'ezpublish.content_view_provider';
    const ADD_VIEW_PROVIDER_METHOD = 'addContentViewProvider';
    const VIEW_TYPE = ContentView::class;
}

class_alias(ContentViewPass::class, 'eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Compiler\ContentViewPass');
