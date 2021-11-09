<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Bundle\Core\ApiLoader\Exception;

use InvalidArgumentException;

class InvalidRepositoryException extends InvalidArgumentException
{
}

class_alias(InvalidRepositoryException::class, 'eZ\Bundle\EzPublishCoreBundle\ApiLoader\Exception\InvalidRepositoryException');
