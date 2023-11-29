<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Core\MVC\Symfony\ErrorHandler;

use Ibexa\Core\MVC\Symfony\ErrorHandler\Php82HideDeprecationsErrorHandler;
use PHPUnit\Framework\TestCase;
use const PHP_VERSION_ID;

final class Php82HideDeprecationsErrorHandlerTest extends TestCase
{
    private int $originalErrorReporting;

    /** @var callable|null */
    private $originalErrorHandler;

    protected function setUp(): void
    {
        $this->originalErrorReporting = error_reporting();
        set_error_handler(
            $this->originalErrorHandler = set_error_handler(
                static function () {}
            )
        );
    }

    protected function tearDown(): void
    {
        error_reporting($this->originalErrorReporting);
        set_error_handler($this->originalErrorHandler);
    }

    public function testRegister(): void
    {
        if (PHP_VERSION_ID < 80200) {
            $this->markTestSkipped('Does not affect versions below PHP 8.2.0');
        }

        $errorHandler = new Php82HideDeprecationsErrorHandler();

        $errorHandler::register(true);
        $debugErrorReporting = error_reporting();

        $errorHandler::register(false);
        $noDebugErrorReporting = error_reporting();

        $this->assertSame(E_ALL & ~E_DEPRECATED, $debugErrorReporting);
        $this->assertSame(E_ALL & ~E_DEPRECATED, $noDebugErrorReporting);
    }
}
