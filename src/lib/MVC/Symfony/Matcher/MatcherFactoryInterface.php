<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\MVC\Symfony\Matcher;

use Ibexa\Core\MVC\Symfony\View\View;

interface MatcherFactoryInterface
{
    /**
     * Checks if $valueObject has a usable configuration for $viewType.
     * If so, the configuration hash will be returned.
     *
     * $valueObject can be for example a Location or a Content object.
     *
     * @param \Ibexa\Core\MVC\Symfony\View\View $view
     *
     * @return array|null The matched configuration as a hash, containing template or controller to use, or null if not matched.
     */
    public function match(View $view);
}

class_alias(MatcherFactoryInterface::class, 'eZ\Publish\Core\MVC\Symfony\Matcher\MatcherFactoryInterface');
