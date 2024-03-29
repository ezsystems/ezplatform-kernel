<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace eZ\Bundle\EzPublishCoreBundle\Tests\DependencyInjection\Compiler;

use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Compiler\TranslationCollectorPass;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class TranslationCollectorPassTest extends AbstractCompilerPassTestCase
{
    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new TranslationCollectorPass());
    }

    public function testTranslationCollector(): void
    {
        $this->setDefinition('translator.default', new Definition());
        $this->setParameter('kernel.project_dir', __DIR__ . $this->normalizePath('/../Fixtures'));

        $this->compile();

        $this->assertContainerBuilderHasParameter('available_translations', ['en', 'hi', 'nb']);
    }

    /**
     * @param $path
     *
     * @return mixed
     */
    private function normalizePath($path)
    {
        return str_replace('/', \DIRECTORY_SEPARATOR, $path);
    }
}
