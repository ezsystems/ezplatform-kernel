<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Integration\RepositoryInstaller\Installer;

use Ibexa\Bundle\RepositoryInstaller\Installer\CoreInstaller;
use Ibexa\Tests\Integration\RepositoryInstaller\TestCase;
use Symfony\Component\Console\Output\NullOutput;

final class CoreInstallerTest extends TestCase
{
    private CoreInstaller $installer;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->installer = self::getServiceByClassName(CoreInstaller::class);
    }

    public function testImportSchema(): void
    {
        $this->installer->setOutput(new NullOutput());
        $this->installer->importSchema();
    }
}
