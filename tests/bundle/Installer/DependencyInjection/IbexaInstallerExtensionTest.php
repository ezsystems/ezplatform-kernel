<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Installer\DependencyInjection;

use Ibexa\Bundle\Installer\Command\InstallPlatformCommand;
use Ibexa\Bundle\Installer\DependencyInjection\Compiler\InstallerTagPass;
use Ibexa\Bundle\Installer\DependencyInjection\IbexaRepositoryInstallerExtension;
use Ibexa\Bundle\Installer\Installer\CoreInstaller;
use Ibexa\Bundle\Installer\Installer\DbBasedInstaller;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;

/**
 * @covers \Ibexa\Bundle\Installer\DependencyInjection\IbexaRepositoryInstallerExtension
 */
class EzSystemsPlatformInstallerExtensionTest extends AbstractExtensionTestCase
{
    /**
     * @covers \Ibexa\Bundle\Installer\DependencyInjection\IbexaRepositoryInstallerExtension::load
     */
    public function testLoadLoadsTaggedCoreInstaller(): void
    {
        $this->load();
        $this->assertContainerBuilderHasServiceDefinitionWithParent(
            CoreInstaller::class,
            DbBasedInstaller::class
        );
        $this->assertContainerBuilderHasServiceDefinitionWithTag(
            CoreInstaller::class,
            InstallerTagPass::INSTALLER_TAG,
            ['type' => 'clean']
        );
    }

    /**
     * @covers \Ibexa\Bundle\Installer\DependencyInjection\IbexaRepositoryInstallerExtension::load
     */
    public function testLoadLoadsTaggedInstallerCommand(): void
    {
        $this->load();
        $this->assertContainerBuilderHasServiceDefinitionWithTag(
            InstallPlatformCommand::class,
            'console.command'
        );
    }

    protected function getContainerExtensions(): array
    {
        return [
            new IbexaRepositoryInstallerExtension(),
        ];
    }
}

class_alias(EzSystemsPlatformInstallerExtensionTest::class, 'EzSystems\PlatformInstallerBundleTests\DependencyInjection\EzSystemsPlatformInstallerExtensionTest');
