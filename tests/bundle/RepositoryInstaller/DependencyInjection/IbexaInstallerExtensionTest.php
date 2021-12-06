<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\RepositoryInstaller\DependencyInjection;

use Ibexa\Bundle\RepositoryInstaller\Command\InstallPlatformCommand;
use Ibexa\Bundle\RepositoryInstaller\DependencyInjection\Compiler\InstallerTagPass;
use Ibexa\Bundle\RepositoryInstaller\DependencyInjection\IbexaRepositoryInstallerExtension;
use Ibexa\Bundle\RepositoryInstaller\Installer\CoreInstaller;
use Ibexa\Bundle\RepositoryInstaller\Installer\DbBasedInstaller;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;

/**
 * @covers \Ibexa\Bundle\RepositoryInstaller\DependencyInjection\IbexaRepositoryInstallerExtension
 */
class IbexaInstallerExtensionTest extends AbstractExtensionTestCase
{
    /**
     * @covers \Ibexa\Bundle\RepositoryInstaller\DependencyInjection\IbexaRepositoryInstallerExtension::load
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
     * @covers \Ibexa\Bundle\RepositoryInstaller\DependencyInjection\IbexaRepositoryInstallerExtension::load
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

class_alias(IbexaInstallerExtensionTest::class, 'EzSystems\PlatformInstallerBundleTests\DependencyInjection\EzSystemsPlatformInstallerExtensionTest');
