<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\IO\Exception;

use Exception;
use Ibexa\Core\Base\Exceptions\NotFoundException as BaseNotFoundException;

class BinaryFileNotFoundException extends BaseNotFoundException
{
    public function __construct($path, Exception $previous = null)
    {
        parent::__construct('BinaryFile', $path, $previous);
    }
}

class_alias(BinaryFileNotFoundException::class, 'eZ\Publish\Core\IO\Exception\BinaryFileNotFoundException');
