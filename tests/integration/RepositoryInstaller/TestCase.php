<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\RepositoryInstaller;

use Ibexa\Contracts\Core\Test\IbexaKernelTestCase;

abstract class TestCase extends IbexaKernelTestCase
{
    /** Necessary to allow multiple Kernel classes */
    protected static $class;

    protected static function getKernelClass(): string
    {
        return TestKernel::class;
    }
}
