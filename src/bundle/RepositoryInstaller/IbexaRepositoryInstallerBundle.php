<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Bundle\RepositoryInstaller;

use Ibexa\Bundle\DoctrineSchema\DoctrineSchemaBundle;
use Ibexa\Bundle\RepositoryInstaller\DependencyInjection\Compiler\InstallerTagPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class IbexaRepositoryInstallerBundle extends Bundle
{
    /**
     * @throws \RuntimeException
     */
    public function build(ContainerBuilder $container)
    {
        if (!$container->hasExtension('ibexa_doctrine_schema')) {
            throw new RuntimeException(
                sprintf(
                    'Ibexa Installer requires Doctrine Schema Bundle (enable %s)',
                    DoctrineSchemaBundle::class
                )
            );
        }

        parent::build($container);
        $container->addCompilerPass(new InstallerTagPass());
    }
}

class_alias(IbexaRepositoryInstallerBundle::class, 'EzSystems\PlatformInstallerBundle\EzSystemsPlatformInstallerBundle');
