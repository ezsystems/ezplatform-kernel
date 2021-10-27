<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Core\DependencyInjection\Compiler;

use Ibexa\Bundle\Core\DependencyInjection\Compiler\ConsoleCommandPass;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

final class ConsoleCommandPassTest extends AbstractCompilerPassTestCase
{
    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new ConsoleCommandPass());
    }

    public function testAddSiteaccessOption(): void
    {
        $commandDefinition = new Definition();
        $serviceId = 'some_service_id';
        $commandDefinition->addTag('console.command');

        $this->setDefinition($serviceId, $commandDefinition);
        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            $serviceId,
            'addOption',
            [
                'siteaccess',
                null,
                InputOption::VALUE_OPTIONAL,
                'SiteAccess to use for operations. If not provided, default siteaccess will be used',
            ]
        );
    }
}

class_alias(ConsoleCommandPassTest::class, 'eZ\Bundle\EzPublishCoreBundle\Tests\DependencyInjection\Compiler\ConsoleCommandPassTest');
