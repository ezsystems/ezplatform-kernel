<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Bundle\Core\ApiLoader\Exception;

use InvalidArgumentException;

class InvalidSearchEngineIndexer extends InvalidArgumentException
{
}

class_alias(InvalidSearchEngineIndexer::class, 'eZ\Bundle\EzPublishCoreBundle\ApiLoader\Exception\InvalidSearchEngineIndexer');
