<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Bundle\Core\DependencyInjection\Configuration;

use Ibexa\Bundle\Core\DependencyInjection\Configuration\SiteAccessAware\ContextualizerInterface;
use Ibexa\Core\Base\Exceptions\InvalidArgumentType;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;

/**
 * Main configuration parser/mapper.
 * It acts as a proxy to inner parsers.
 */
class ConfigParser implements ParserInterface
{
    /** @var \Ibexa\Bundle\Core\DependencyInjection\Configuration\ParserInterface[] */
    private $configParsers;

    public function __construct(array $configParsers = [])
    {
        foreach ($configParsers as $parser) {
            if (!$parser instanceof ParserInterface) {
                throw new InvalidArgumentType(
                    'Inner config parser',
                    ParserInterface::class,
                    $parser
                );
            }
        }

        $this->configParsers = $configParsers;
    }

    /**
     * @param \Ibexa\Bundle\Core\DependencyInjection\Configuration\ParserInterface[] $configParsers
     */
    public function setConfigParsers($configParsers)
    {
        $this->configParsers = $configParsers;
    }

    /**
     * @return \Ibexa\Bundle\Core\DependencyInjection\Configuration\ParserInterface[]
     */
    public function getConfigParsers()
    {
        return $this->configParsers;
    }

    public function mapConfig(array &$scopeSettings, $currentScope, ContextualizerInterface $contextualizer)
    {
        foreach ($this->configParsers as $parser) {
            $parser->mapConfig($scopeSettings, $currentScope, $contextualizer);
        }
    }

    public function preMap(array $config, ContextualizerInterface $contextualizer)
    {
        foreach ($this->configParsers as $parser) {
            $parser->preMap($config, $contextualizer);
        }
    }

    public function postMap(array $config, ContextualizerInterface $contextualizer)
    {
        foreach ($this->configParsers as $parser) {
            $parser->postMap($config, $contextualizer);
        }
    }

    public function addSemanticConfig(NodeBuilder $nodeBuilder)
    {
        $fieldTypeNodeBuilder = $nodeBuilder
            ->arrayNode('fieldtypes')
            ->children();

        // Delegate to configuration parsers
        foreach ($this->configParsers as $parser) {
            if ($parser instanceof FieldTypeParserInterface) {
                $parser->addSemanticConfig($fieldTypeNodeBuilder);
            } else {
                $parser->addSemanticConfig($nodeBuilder);
            }
        }
    }
}

class_alias(ConfigParser::class, 'eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\ConfigParser');
