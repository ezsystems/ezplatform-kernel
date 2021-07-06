<?php
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Test;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\Repository;

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
        $repository = self::getServiceByClassName(ContentService::class);
    }
}