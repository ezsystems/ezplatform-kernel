<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\MVC\Symfony\View\Provider;

use Ibexa\Core\Base\Exceptions\InvalidArgumentException;
use Ibexa\Core\MVC\Symfony\View\View;

class Registry
{
    /**
     * Array of ViewProvider, indexed by handled type.
     *
     * @var \Ibexa\Core\MVC\Symfony\View\ViewProvider[][]
     */
    private $viewProviders;

    /**
     * @param \Ibexa\Core\MVC\Symfony\View\View $view
     *
     * @return \Ibexa\Core\MVC\Symfony\View\ViewProvider[]
     *
     * @throws \Ibexa\Core\Base\Exceptions\InvalidArgumentException
     */
    public function getViewProviders(View $view)
    {
        foreach (array_keys($this->viewProviders) as $type) {
            if ($view instanceof $type) {
                return $this->viewProviders[$type];
            }
        }
        throw new InvalidArgumentException('view', 'No compatible ViewProvider found for ' . gettype($view));
    }

    /**
     * Sets the complete list of view providers.
     *
     * @param array $viewProviders ['type' => [ViewProvider1, ViewProvider2]]
     */
    public function setViewProviders(array $viewProviders)
    {
        $this->viewProviders = $viewProviders;
    }
}

class_alias(Registry::class, 'eZ\Publish\Core\MVC\Symfony\View\Provider\Registry');
