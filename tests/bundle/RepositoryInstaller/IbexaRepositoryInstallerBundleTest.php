<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\RepositoryInstaller;

use Ibexa\Bundle\DoctrineSchema\DependencyInjection\DoctrineSchemaExtension;
use Ibexa\Bundle\RepositoryInstaller\DependencyInjection\Compiler\InstallerTagPass;
use Ibexa\Bundle\RepositoryInstaller\IbexaRepositoryInstallerBundle;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

class IbexaRepositoryInstallerBundleTest extends TestCase
{
    /** @var \Ibexa\Bundle\RepositoryInstaller\IbexaRepositoryInstallerBundle */
    private $bundle;

    public function setUp(): void
    {
        $this->bundle = new IbexaRepositoryInstallerBundle();
    }

    /**
     * @covers \Ibexa\Bundle\RepositoryInstaller\IbexaRepositoryInstallerBundle::build
     */
    public function testBuild(): void
    {
        $container = new ContainerBuilder();
        $container->registerExtension(new DoctrineSchemaExtension());
        $this->bundle->build($container);

        // check if InstallerTagPass was added
        self::assertNotEmpty(
            array_filter(
                $container->getCompilerPassConfig()->getPasses(),
                static function (CompilerPassInterface $compilerPass) {
                    return $compilerPass instanceof InstallerTagPass;
                }
            )
        );
    }

    /**
     * @covers \Ibexa\Bundle\RepositoryInstaller\IbexaRepositoryInstallerBundle::build
     */
    public function testBuildFailsWithoutDoctrineSchemaBundle(): void
    {
        $container = new ContainerBuilder();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Ibexa Installer requires Doctrine Schema Bundle');
        $this->bundle->build($container);
    }
}

class_alias(IbexaRepositoryInstallerBundleTest::class, 'EzSystems\PlatformInstallerBundleTests\EzSystemsPlatformInstallerBundleTest');
