<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Bundle\Core\Imagine;

use Ibexa\Core\MVC\ConfigResolverInterface;

class PlaceholderAliasGeneratorConfigurator
{
    /** @var \Ibexa\Core\MVC\ConfigResolverInterface */
    private $configResolver;

    /** @var \Ibexa\Bundle\Core\Imagine\PlaceholderProviderRegistry */
    private $providerRegistry;

    /** @var array */
    private $providersConfig;

    public function __construct(
        ConfigResolverInterface $configResolver,
        PlaceholderProviderRegistry $providerRegistry,
        array $providersConfig
    ) {
        $this->configResolver = $configResolver;
        $this->providerRegistry = $providerRegistry;
        $this->providersConfig = $providersConfig;
    }

    public function configure(PlaceholderAliasGenerator $generator)
    {
        $binaryHandlerName = $this->configResolver->getParameter('io.binarydata_handler');

        if (isset($this->providersConfig[$binaryHandlerName])) {
            $config = $this->providersConfig[$binaryHandlerName];

            $provider = $this->providerRegistry->getProvider($config['provider']);

            $generator->setPlaceholderProvider($provider, $config['options']);
            $generator->setVerifyBinaryDataAvailability($config['verify_binary_data_availability'] ?? false);
        }
    }
}

class_alias(PlaceholderAliasGeneratorConfigurator::class, 'eZ\Bundle\EzPublishCoreBundle\Imagine\PlaceholderAliasGeneratorConfigurator');
