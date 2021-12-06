<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Bundle\IO\DependencyInjection\ConfigurationFactory\MetadataHandler;

use Ibexa\Bundle\IO\DependencyInjection\ConfigurationFactory\Flysystem as BaseFactory;

class Flysystem extends BaseFactory
{
    public function getParentServiceId()
    {
        return 'ezpublish.core.io.metadata_handler.flysystem';
    }
}

class_alias(Flysystem::class, 'eZ\Bundle\EzPublishIOBundle\DependencyInjection\ConfigurationFactory\MetadataHandler\Flysystem');
