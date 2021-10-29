<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\MVC\Exception;

use InvalidArgumentException;

/**
 * This exception is thrown when a dynamic parameter could not be found in any scope.
 */
class ParameterNotFoundException extends InvalidArgumentException
{
    public function __construct($paramName, $namespace, array $triedScopes = [])
    {
        $this->message = "Parameter '$paramName' with namespace '$namespace' could not be found.";
        if (!empty($triedScopes)) {
            $this->message .= ' Tried scopes: ' . implode(', ', $triedScopes);
        }
    }
}

class_alias(ParameterNotFoundException::class, 'eZ\Publish\Core\MVC\Exception\ParameterNotFoundException');
