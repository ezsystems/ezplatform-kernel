<?php
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
    }

    public function testBasicKernelCompiles(): void
    {
        self::getServiceByClassName(Repository::class);
        $this->expectNotToPerformAssertions();
    }
}
