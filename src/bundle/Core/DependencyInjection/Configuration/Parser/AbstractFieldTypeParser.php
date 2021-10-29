<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Bundle\Core\DependencyInjection\Configuration\Parser;

use Ibexa\Bundle\Core\DependencyInjection\Configuration\AbstractParser;
use Ibexa\Bundle\Core\DependencyInjection\Configuration\FieldTypeParserInterface;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;

/**
 * Abstract parser class that field type parsers need to extend in order
 * to receive NodeBuilder at Node just under ezpublish.<system>.<siteaccess>.fieldtypes.<identifier>.
 */
abstract class AbstractFieldTypeParser extends AbstractParser implements FieldTypeParserInterface
{
    /**
     * Adds semantic configuration definition.
     *
     * @param \Symfony\Component\Config\Definition\Builder\NodeBuilder $nodeBuilder Node just under ezpublish.<system>.<siteaccess>
     */
    public function addSemanticConfig(NodeBuilder $nodeBuilder)
    {
        $fieldTypeNodeBuilder = $nodeBuilder->arrayNode($this->getFieldTypeIdentifier())->children();

        $this->addFieldTypeSemanticConfig($fieldTypeNodeBuilder);
    }
}

class_alias(AbstractFieldTypeParser::class, 'eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\Parser\AbstractFieldTypeParser');
