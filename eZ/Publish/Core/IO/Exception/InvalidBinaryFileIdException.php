<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace eZ\Publish\Core\IO\Exception;

use eZ\Publish\Core\Base\Exceptions\InvalidArgumentValue;

class InvalidBinaryFileIdException extends InvalidArgumentValue
{
    public function __construct($id)
    {
        parent::__construct('BinaryFile::id', $id, 'BinaryFile');
    }
}
