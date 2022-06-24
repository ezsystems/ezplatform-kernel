<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Core\DependencyInjection\Compiler;

use eZ\Publish\Core\Base\Container\Compiler\Search\AggregateFieldValueMapperPass;
use eZ\Publish\SPI\Search\FieldType\BooleanField;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

final class AggregateFieldValueMapperPassTest extends AbstractCompilerPassTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->setDefinition(AggregateFieldValueMapperPass::SERVICE_ID, new Definition());
    }

    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new AggregateFieldValueMapperPass());
    }

    public function testAddMapper(): void
    {
        $booleanMapper = new Definition();
        $fieldValueMapperServiceId = 'field_value_mapper_service_id';
        $booleanMapper->addTag(
            AggregateFieldValueMapperPass::TAG,
            ['maps' => BooleanField::class]
        );

        $this->setDefinition($fieldValueMapperServiceId, $booleanMapper);
        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            AggregateFieldValueMapperPass::SERVICE_ID,
            'addMapper',
            [new Reference($fieldValueMapperServiceId), BooleanField::class]
        );
    }
}
