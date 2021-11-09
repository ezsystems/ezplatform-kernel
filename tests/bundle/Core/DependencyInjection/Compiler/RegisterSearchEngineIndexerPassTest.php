<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Core\DependencyInjection\Compiler;

use Ibexa\Bundle\Core\DependencyInjection\Compiler\RegisterSearchEngineIndexerPass;
use LogicException;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

final class RegisterSearchEngineIndexerPassTest extends AbstractCompilerPassTestCase
{
    private const EXAMPLE_SERVICE_ID = 'app.search_engine';
    private const EXAMPLE_ALIAS = 'foo';

    protected function setUp(): void
    {
        parent::setUp();

        $this->setDefinition('ezpublish.api.search_engine.indexer.factory', new Definition());
    }

    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new RegisterSearchEngineIndexerPass());
    }

    /**
     * @dataProvider tagsProvider
     */
    public function testRegisterSearchEngineIndexer(string $tag): void
    {
        $definition = new Definition();
        $definition->addTag($tag, [
            'alias' => self::EXAMPLE_ALIAS,
        ]);

        $this->setDefinition(self::EXAMPLE_SERVICE_ID, $definition);
        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'ezpublish.api.search_engine.indexer.factory',
            'registerSearchEngineIndexer',
            [
                new Reference(self::EXAMPLE_SERVICE_ID),
                self::EXAMPLE_ALIAS,
            ]
        );
    }

    /**
     * @dataProvider tagsProvider
     */
    public function testRegisterSearchEngineIndexerWithoutAliasThrowsLogicException(string $tag): void
    {
        $this->expectException(LogicException::class);

        $definition = new Definition();
        $definition->addTag($tag);

        $this->setDefinition(self::EXAMPLE_SERVICE_ID, $definition);
        $this->compile();
    }

    public function tagsProvider(): iterable
    {
        return [
            [RegisterSearchEngineIndexerPass::SEARCH_ENGINE_INDEXER_SERVICE_TAG],
            [RegisterSearchEngineIndexerPass::DEPRECATED_SEARCH_ENGINE_INDEXER_SERVICE_TAG],
        ];
    }
}

class_alias(RegisterSearchEngineIndexerPassTest::class, 'eZ\Bundle\EzPublishCoreBundle\Tests\DependencyInjection\Compiler\RegisterSearchEngineIndexerPassTest');
