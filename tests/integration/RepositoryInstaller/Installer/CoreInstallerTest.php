<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Integration\Installer\RepositoryInstaller;

use EzSystems\PlatformInstallerBundle\Installer\CoreInstaller;
use Ibexa\Tests\Integration\RepositoryInstaller\TestCase;
use Symfony\Component\Console\Output\NullOutput;

final class CoreInstallerTest extends TestCase
{
    /** @var \EzSystems\PlatformInstallerBundle\Installer\CoreInstaller */
    private $installer;

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
