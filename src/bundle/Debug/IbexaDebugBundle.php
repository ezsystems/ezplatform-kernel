<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Bundle\Debug;

use Ibexa\Bundle\Debug\DependencyInjection\Compiler\DataCollectorPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class IbexaDebugBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new DataCollectorPass());
    }
}

class_alias(IbexaDebugBundle::class, 'eZ\Bundle\EzPublishDebugBundle\EzPublishDebugBundle');
