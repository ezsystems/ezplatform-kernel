<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Installer;

use Ibexa\Contracts\Core\Test\IbexaKernelTestCase;

abstract class TestCase extends IbexaKernelTestCase
{
    protected static function getKernelClass(): string
    {
        return TestKernel::class;
    }
}
