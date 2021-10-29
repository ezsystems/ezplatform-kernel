<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Bundle\Core\DependencyInjection\Configuration;

use Symfony\Component\Config\Definition\Builder\NodeBuilder;

interface FieldTypeParserInterface extends ParserInterface
{
    /**
     * Returns the fieldType identifier the config parser works for.
     * Required to create configuration node under system.<siteaccess>.fieldtypes.
     *
     * @return string
     */
    public function getFieldTypeIdentifier();

    /**
     * Adds fieldType semantic configuration definition.
     *
     * @param \Symfony\Component\Config\Definition\Builder\NodeBuilder $nodeBuilder Node just under ezpublish.system.<siteaccess>.fieldtypes.<identifier>
     */
    public function addFieldTypeSemanticConfig(NodeBuilder $nodeBuilder);
}

class_alias(FieldTypeParserInterface::class, 'eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\FieldTypeParserInterface');
