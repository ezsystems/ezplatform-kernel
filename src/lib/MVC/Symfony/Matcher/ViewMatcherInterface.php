<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\MVC\Symfony\Matcher;

use Ibexa\Core\MVC\Symfony\View\View;

/**
 * Matches a View against a set of matchers.
 */
interface ViewMatcherInterface
{
    /**
     * Registers the matching configuration for the matcher.
     * It's up to the implementor to validate $matchingConfig since it can be anything configured by the end-developer.
     *
     * @param mixed $matchingConfig
     *
     * @throws \InvalidArgumentException Should be thrown if $matchingConfig is not valid.
     */
    public function setMatchingConfig($matchingConfig);

    /**
     * Matches the $view against a set of matchers.
     *
     * @param \Ibexa\Core\MVC\Symfony\View\View $view
     *
     * @return bool
     */
    public function match(View $view);
}

class_alias(ViewMatcherInterface::class, 'eZ\Publish\Core\MVC\Symfony\Matcher\ViewMatcherInterface');
