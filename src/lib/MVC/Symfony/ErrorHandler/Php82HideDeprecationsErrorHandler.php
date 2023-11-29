<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\MVC\Symfony\ErrorHandler;

use Symfony\Component\Runtime\Internal\BasicErrorHandler;
use const PHP_VERSION_ID;

final class Php82HideDeprecationsErrorHandler extends BasicErrorHandler
{
    public static function register(bool $debug): void
    {
        parent::register($debug);

        if (PHP_VERSION_ID > 80200) {
            error_reporting(E_ALL & ~E_DEPRECATED);
        }
    }
}
