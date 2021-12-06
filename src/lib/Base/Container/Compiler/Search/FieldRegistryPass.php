<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\Base\Container\Compiler\Search;

use Ibexa\Core\Base\Container\Compiler\TaggedServiceIdsIterator\BackwardCompatibleIterator;
use LogicException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * This compiler pass will register eZ Publish indexable field types.
 */
class FieldRegistryPass implements CompilerPassInterface
{
    public const FIELD_TYPE_INDEXABLE_SERVICE_TAG = 'ezplatform.field_type.indexable';
    public const DEPRECATED_FIELD_TYPE_INDEXABLE_SERVICE_TAG = 'ezpublish.fieldType.indexable';

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     *
     * @throws \LogicException
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('ezpublish.search.common.field_registry')) {
            return;
        }

        $fieldRegistryDefinition = $container->getDefinition('ezpublish.search.common.field_registry');

        $fieldTypesIterator = new BackwardCompatibleIterator(
            $container,
            self::FIELD_TYPE_INDEXABLE_SERVICE_TAG,
            self::DEPRECATED_FIELD_TYPE_INDEXABLE_SERVICE_TAG
        );

        foreach ($fieldTypesIterator as $id => $attributes) {
            foreach ($attributes as $attribute) {
                if (!isset($attribute['alias'])) {
                    throw new LogicException(
                        sprintf(
                            'The %s or %s service tag needs an "alias" attribute to identify the indexable Field Type.',
                            self::DEPRECATED_FIELD_TYPE_INDEXABLE_SERVICE_TAG,
                            self::FIELD_TYPE_INDEXABLE_SERVICE_TAG
                        )
                    );
                }

                $fieldRegistryDefinition->addMethodCall(
                    'registerType',
                    [
                        $attribute['alias'],
                        new Reference($id),
                    ]
                );
            }
        }
    }
}

class_alias(FieldRegistryPass::class, 'eZ\Publish\Core\Base\Container\Compiler\Search\FieldRegistryPass');
