<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\MVC\Symfony\View\Builder\Registry;

use Ibexa\Core\MVC\Symfony\View\Builder\ViewBuilderRegistry;

/**
 * A registry of ViewBuilders that uses the ViewBuilder's match() method to identify the builder against
 * a controller string.
 */
class ControllerMatch implements ViewBuilderRegistry
{
    /** @var \Ibexa\Core\MVC\Symfony\View\Builder\ViewBuilder[] */
    private $registry = [];

    public function __construct(iterable $viewBuilders = [])
    {
        $toAdd = [];
        foreach ($viewBuilders as $viewBuilder) {
            $toAdd[] = $viewBuilder;
        }
        $this->addToRegistry($toAdd);
    }

    /**
     * @param \Ibexa\Core\MVC\Symfony\View\Builder\ViewBuilder[] $viewBuilders
     */
    public function addToRegistry(array $viewBuilders)
    {
        $this->registry = array_merge($this->registry, $viewBuilders);
    }

    /**
     * Returns the ViewBuilder that matches the given controller string.
     *
     * @param string $controllerString A controller string to match against. Example: ez_content:viewAction.
     *
     * @return \Ibexa\Core\MVC\Symfony\View\Builder\ViewBuilder|null
     */
    public function getFromRegistry($controllerString)
    {
        if (!is_string($controllerString)) {
            return null;
        }

        foreach ($this->registry as $viewBuilder) {
            if ($viewBuilder->matches($controllerString)) {
                return $viewBuilder;
            }
        }

        return null;
    }
}

class_alias(ControllerMatch::class, 'eZ\Publish\Core\MVC\Symfony\View\Builder\Registry\ControllerMatch');
