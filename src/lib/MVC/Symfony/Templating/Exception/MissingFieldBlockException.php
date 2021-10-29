<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\MVC\Symfony\Templating\Exception;

use RuntimeException;

class MissingFieldBlockException extends RuntimeException
{
}

class_alias(MissingFieldBlockException::class, 'eZ\Publish\Core\MVC\Symfony\Templating\Exception\MissingFieldBlockException');
