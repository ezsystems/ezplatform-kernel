<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Core\DependencyInjection\Configuration\Parser\FieldType;

use Ibexa\Bundle\Core\DependencyInjection\Configuration\Parser\AbstractFieldTypeParser;
use Ibexa\Bundle\Core\DependencyInjection\Configuration\SiteAccessAware\ContextualizerInterface;
use Ibexa\Core\FieldType\ImageAsset\Type as ImageAssetFieldType;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;

class ImageAsset extends AbstractFieldTypeParser
{
    /**
     * {@inheritdoc}
     */
    public function getFieldTypeIdentifier(): string
    {
        return ImageAssetFieldType::FIELD_TYPE_IDENTIFIER;
    }

    /**
     * {@inheritdoc}
     */
    public function addFieldTypeSemanticConfig(NodeBuilder $nodeBuilder): void
    {
        $nodeBuilder
            ->scalarNode('content_type_identifier')
                ->isRequired()
                ->cannotBeEmpty()
            ->end()
            ->scalarNode('content_field_identifier')
                ->isRequired()
                ->cannotBeEmpty()
            ->end()
            ->scalarNode('name_field_identifier')
                ->isRequired()
                ->cannotBeEmpty()
            ->end()
            ->scalarNode('parent_location_id')
                ->isRequired()
                ->cannotBeEmpty()
            ->end();
    }

    /**
     * {@inheritdoc}
     */
    public function mapConfig(array &$scopeSettings, $currentScope, ContextualizerInterface $contextualizer): void
    {
        $fieldTypeIdentifier = $this->getFieldTypeIdentifier();

        if (isset($scopeSettings['fieldtypes'][$fieldTypeIdentifier])) {
            $contextualizer->setContextualParameter(
                "fieldtypes.{$fieldTypeIdentifier}.mappings",
                $currentScope,
                $scopeSettings['fieldtypes'][$fieldTypeIdentifier]
            );
        }
    }
}

class_alias(ImageAsset::class, 'eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\Parser\FieldType\ImageAsset');
