<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\MVC\Symfony\View;

/**
 * Collects parameters that will be injected into View objects.
 */
interface ParametersInjector
{
    public function injectViewParameters(View $view, array $parameters);
}

class_alias(ParametersInjector::class, 'eZ\Publish\Core\MVC\Symfony\View\ParametersInjector');
