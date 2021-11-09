<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\MVC\Symfony\FieldType\View;

/**
 * Interface for fieldtypes view parameter provider registry.
 */
interface ParameterProviderRegistryInterface
{
    /**
     * Checks if a parameter provider is set for a given field type identifier.
     *
     * @param string $fieldTypeIdentifier
     *
     * @return bool
     */
    public function hasParameterProvider($fieldTypeIdentifier);

    /**
     * Returns parameter provider for given field type identifier.
     *
     * @param string $fieldTypeIdentifier
     *
     * @throws \InvalidArgumentException If no parameter provider is provided for $fieldTypeIdentifier.
     *
     * @return \Ibexa\Core\MVC\Symfony\FieldType\View\ParameterProviderInterface
     */
    public function getParameterProvider($fieldTypeIdentifier);

    /**
     * Sets a parameter provider for given field type identifier.
     *
     * @param \Ibexa\Core\MVC\Symfony\FieldType\View\ParameterProviderInterface $parameterProvider
     * @param string $fieldTypeIdentifier
     */
    public function setParameterProvider(ParameterProviderInterface $parameterProvider, $fieldTypeIdentifier);
}

class_alias(ParameterProviderRegistryInterface::class, 'eZ\Publish\Core\MVC\Symfony\FieldType\View\ParameterProviderRegistryInterface');
