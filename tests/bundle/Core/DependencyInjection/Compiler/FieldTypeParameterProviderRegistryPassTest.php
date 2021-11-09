<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Bundle\Core\DependencyInjection\Compiler;

use Ibexa\Bundle\Core\DependencyInjection\Compiler\FieldTypeParameterProviderRegistryPass;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class FieldTypeParameterProviderRegistryPassTest extends AbstractCompilerPassTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->setDefinition('ezpublish.fieldType.parameterProviderRegistry', new Definition());
    }

    /**
     * Register the compiler pass under test, just like you would do inside a bundle's load()
     * method:.
     *
     *   $container->addCompilerPass(new MyCompilerPass());
     */
    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new FieldTypeParameterProviderRegistryPass());
    }

    /**
     * @dataProvider tagsProvider
     */
    public function testRegisterFieldType(string $tag)
    {
        $fieldTypeIdentifier = 'field_type_identifier';
        $serviceId = 'service_id';
        $def = new Definition();
        $def->addTag($tag, ['alias' => $fieldTypeIdentifier]);
        $this->setDefinition($serviceId, $def);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'ezpublish.fieldType.parameterProviderRegistry',
            'setParameterProvider',
            [new Reference($serviceId), $fieldTypeIdentifier]
        );
    }

    /**
     * @dataProvider tagsProvider
     *
     * @param string $tag
     */
    public function testRegisterFieldTypeNoAlias(string $tag)
    {
        $this->expectException(\LogicException::class);

        $fieldTypeIdentifier = 'field_type_identifier';
        $serviceId = 'service_id';
        $def = new Definition();
        $def->addTag($tag);
        $this->setDefinition($serviceId, $def);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'ezpublish.fieldType.parameterProviderRegistry',
            'setParameterProvider',
            [new Reference($serviceId), $fieldTypeIdentifier]
        );
    }

    public function tagsProvider(): array
    {
        return [
            [FieldTypeParameterProviderRegistryPass::DEPRECATED_FIELD_TYPE_PARAMETER_PROVIDER_SERVICE_TAG],
            [FieldTypeParameterProviderRegistryPass::FIELD_TYPE_PARAMETER_PROVIDER_SERVICE_TAG],
        ];
    }
}

class_alias(FieldTypeParameterProviderRegistryPassTest::class, 'eZ\Bundle\EzPublishCoreBundle\Tests\DependencyInjection\Compiler\FieldTypeParameterProviderRegistryPassTest');
