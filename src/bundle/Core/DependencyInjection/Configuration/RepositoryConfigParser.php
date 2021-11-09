<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Core\DependencyInjection\Configuration;

use Ibexa\Core\Base\Exceptions\InvalidArgumentType;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;

final class RepositoryConfigParser implements RepositoryConfigParserInterface
{
    /** @var iterable<\Ibexa\Bundle\Core\DependencyInjection\Configuration\RepositoryConfigParserInterface> */
    private $configParsers;

    /**
     * @param \Ibexa\Bundle\Core\DependencyInjection\Configuration\RepositoryConfigParserInterface[] $configParsers
     */
    public function __construct(iterable $configParsers = [])
    {
        foreach ($configParsers as $parser) {
            if (!$parser instanceof RepositoryConfigParserInterface) {
                throw new InvalidArgumentType('Inner repository config parser', RepositoryConfigParserInterface::class, $parser);
            }
        }

        $this->configParsers = $configParsers;
    }

    public function addSemanticConfig(NodeBuilder $nodeBuilder): void
    {
        foreach ($this->configParsers as $parser) {
            $parser->addSemanticConfig($nodeBuilder);
        }
    }
}
