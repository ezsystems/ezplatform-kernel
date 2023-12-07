<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Core\MVC\Symfony\ErrorHandler;

use Ibexa\Contracts\Core\MVC\Symfony\ErrorHandler\Php82HideDeprecationsErrorHandler;
use PHPUnit\Framework\TestCase;

/**
 * @requires PHP >= 8.2.0
 */
final class Php82HideDeprecationsErrorHandlerTest extends TestCase
{
    /** @var int */
    private $originalErrorReporting;

    protected function setUp(): void
    {
        $this->originalErrorReporting = error_reporting();
    }

    protected function tearDown(): void
    {
        error_reporting($this->originalErrorReporting);
        restore_error_handler();
    }

    public function testRegisterDebug(): void
    {
        Php82HideDeprecationsErrorHandler::register(true);
        $errorReporting = error_reporting();

        $this->assertSame(E_ALL & ~E_DEPRECATED, $errorReporting);
    }

    public function testRegisterNoDebug(): void
    {
        Php82HideDeprecationsErrorHandler::register(false);
        $errorReporting = error_reporting();

        $this->assertSame(E_ALL & ~E_DEPRECATED, $errorReporting);
    }
}
