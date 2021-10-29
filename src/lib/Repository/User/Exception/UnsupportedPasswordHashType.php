<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\Repository\User\Exception;

use Ibexa\Core\Base\Exceptions\InvalidArgumentException;

class UnsupportedPasswordHashType extends InvalidArgumentException
{
    public function __construct(int $hashType)
    {
        parent::__construct('hashType', "Password hash type '$hashType' is not recognized");
    }
}

class_alias(UnsupportedPasswordHashType::class, 'eZ\Publish\Core\Repository\User\Exception\UnsupportedPasswordHashType');
