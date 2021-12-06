<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Bundle\IO;

use Ibexa\Bundle\IO\DependencyInjection\Compiler;
use Ibexa\Bundle\IO\DependencyInjection\ConfigurationFactory;
use Ibexa\Bundle\IO\DependencyInjection\IbexaIOExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class IbexaIOBundle extends Bundle
{
    /** @var \Ibexa\Bundle\IO\DependencyInjection\IbexaIOExtension */
    protected $extension;

    public function build(ContainerBuilder $container)
    {
        $extension = $this->getContainerExtension();
        $container->addCompilerPass(
            new Compiler\IOConfigurationPass(
                $extension->getMetadataHandlerFactories(),
                $extension->getBinarydataHandlerFactories()
            )
        );
        $container->addCompilerPass(new Compiler\MigrationFileListerPass());
        parent::build($container);
    }

    public function getContainerExtension()
    {
        if (!isset($this->extension)) {
            $this->extension = new IbexaIOExtension();
            $this->extension->addMetadataHandlerFactory('flysystem', new ConfigurationFactory\MetadataHandler\Flysystem());
            $this->extension->addMetadataHandlerFactory('legacy_dfs_cluster', new ConfigurationFactory\MetadataHandler\LegacyDFSCluster());
            $this->extension->addBinarydataHandlerFactory('flysystem', new ConfigurationFactory\BinarydataHandler\Flysystem());
        }

        return $this->extension;
    }
}

class_alias(IbexaIOBundle::class, 'eZ\Bundle\EzPublishIOBundle\EzPublishIOBundle');
