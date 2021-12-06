<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\MVC\Symfony\FieldType\View;

use Ibexa\Contracts\Core\Repository\Values\Content\Field;

/**
 * Interface for services providing additional parameters to a fieldtype's view template (using ibexa_render_field() helper).
 * Each instance of this interface needs to be correctly registered in the ParameterProviderRegistry.
 *
 * @see \Ibexa\Core\MVC\Symfony\FieldType\View\ParameterProviderRegistryInterface
 */
interface ParameterProviderInterface
{
    /**
     * Returns a hash of parameters to inject to the associated fieldtype's view template.
     * Returned parameters will only be available for associated field type.
     *
     * Key is the parameter name (the variable name exposed in the template, in the 'parameters' array).
     * Value is the parameter's value.
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Field $field The field parameters are provided for.
     *
     * @return array
     */
    public function getViewParameters(Field $field);
}

class_alias(ParameterProviderInterface::class, 'eZ\Publish\Core\MVC\Symfony\FieldType\View\ParameterProviderInterface');
