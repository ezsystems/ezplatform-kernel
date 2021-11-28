<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Bundle\LegacySearchEngine;

use Ibexa\Core\Base\Container\Compiler\Search\FieldRegistryPass;
use Ibexa\Core\Base\Container\Compiler\Search\Legacy\CriteriaConverterPass;
use Ibexa\Core\Base\Container\Compiler\Search\Legacy\CriterionFieldValueHandlerRegistryPass;
use Ibexa\Core\Base\Container\Compiler\Search\Legacy\SortClauseConverterPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class IbexaLegacySearchEngineBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new CriteriaConverterPass());
        $container->addCompilerPass(new CriterionFieldValueHandlerRegistryPass());
        $container->addCompilerPass(new SortClauseConverterPass());
        $container->addCompilerPass(new FieldRegistryPass());
    }

    public function getContainerExtension()
    {
        if (!isset($this->extension)) {
            $this->extension = new DependencyInjection\IbexaLegacySearchEngineExtension();
        }

        return $this->extension;
    }
}

class_alias(IbexaLegacySearchEngineBundle::class, 'eZ\Bundle\EzPublishLegacySearchEngineBundle\EzPublishLegacySearchEngineBundle');
