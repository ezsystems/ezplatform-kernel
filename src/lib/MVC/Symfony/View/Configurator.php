<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\MVC\Symfony\View;

/**
 * Configures a View object.
 *
 * Example: set the template, add extra parameters.
 */
interface Configurator
{
    public function configure(View $view);
}

class_alias(Configurator::class, 'eZ\Publish\Core\MVC\Symfony\View\Configurator');
