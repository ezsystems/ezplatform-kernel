<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Core;

use Ibexa\Contracts\Core\Test\Repository\SetupFactory\Legacy;
use Ibexa\Tests\Integration\Core\Repository\BaseTest;

abstract class BaseGatewayTest extends BaseTest
{
    /** @var \Ibexa\Contracts\Core\Repository\Repository */
    protected $repository;

    protected function setUp(): void
    {
        $this->repository = (new Legacy())->getRepository(true);
    }
}

class_alias(BaseGatewayTest::class, 'eZ\Publish\SPI\Tests\BaseGatewayTest');
