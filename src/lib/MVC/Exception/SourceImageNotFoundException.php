<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\MVC\Exception;

use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;

class SourceImageNotFoundException extends NotFoundException
{
}

class_alias(SourceImageNotFoundException::class, 'eZ\Publish\Core\MVC\Exception\SourceImageNotFoundException');
