<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Core\DependencyInjection\Configuration;

use Ibexa\Bundle\Core\DependencyInjection\Configuration\RepositoryConfigParser;
use Ibexa\Bundle\Core\DependencyInjection\Configuration\RepositoryConfigParserInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;

final class RepositoryConfigParserTest extends TestCase
{
    public function testAddSemanticConfig(): void
    {
        $nodeBuilder = $this->createMock(NodeBuilder::class);

        $repositoryConfigParser = new RepositoryConfigParser([
            $this->createRepositoryConfigParserMock($nodeBuilder),
            $this->createRepositoryConfigParserMock($nodeBuilder),
            $this->createRepositoryConfigParserMock($nodeBuilder),
        ]);

        $repositoryConfigParser->addSemanticConfig($nodeBuilder);
    }

    private function createRepositoryConfigParserMock(
        NodeBuilder $nodeBuilder
    ): RepositoryConfigParserInterface {
        $configParser = $this->createMock(RepositoryConfigParserInterface::class);
        $configParser
            ->expects($this->once())
            ->method('addSemanticConfig')
            ->with($nodeBuilder);

        return $configParser;
    }
}
