<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Bundle\Core\ApiLoader\Exception;

use InvalidArgumentException;

class InvalidStorageEngine extends InvalidArgumentException
{
}

class_alias(InvalidStorageEngine::class, 'eZ\Bundle\EzPublishCoreBundle\ApiLoader\Exception\InvalidStorageEngine');
