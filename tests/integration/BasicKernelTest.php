<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Core;

use eZ\Publish\API\Repository\Repository;
use Ibexa\Contracts\Core\Test\IbexaKernelTestCase;

/**
 * @coversNothing
 */
final class BasicKernelTest extends IbexaKernelTestCase
{
    protected function setUp(): void
    {
        self::bootKernel();

        self::loadSchema();
        self::loadFixtures();
    }

    public function testBasicKernelCompiles(): void
    {
        self::getServiceByClassName(Repository::class);
        $this->expectNotToPerformAssertions();
    }
}
