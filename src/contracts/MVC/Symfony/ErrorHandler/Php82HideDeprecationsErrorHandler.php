<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\MVC\Symfony\ErrorHandler;

use const PHP_VERSION_ID;
use Symfony\Component\Runtime\Internal\BasicErrorHandler;

final class Php82HideDeprecationsErrorHandler
{
    public static function register(bool $debug): void
    {
        BasicErrorHandler::register($debug);

        if (PHP_VERSION_ID > 80200) {
            error_reporting(E_ALL & ~E_DEPRECATED);
        }
    }
}
