<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Bundle\Core\DependencyInjection\Compiler;

use Ibexa\Core\MVC\Symfony\View\ContentView;

/**
 * The LocationViewPass adds DIC compiler pass related to content view.
 * This includes adding ContentViewProvider implementations.
 *
 * @see \Ibexa\Core\MVC\Symfony\View\Manager
 * @deprecated since 6.0
 */
class LocationViewPass extends ViewManagerPass
{
    public const VIEW_PROVIDER_IDENTIFIER = 'ezpublish.location_view_provider';
    public const ADD_VIEW_PROVIDER_METHOD = 'addLocationViewProvider';
    public const VIEW_TYPE = ContentView::DEFAULT_VIEW_TYPE;
}

class_alias(LocationViewPass::class, 'eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Compiler\LocationViewPass');
