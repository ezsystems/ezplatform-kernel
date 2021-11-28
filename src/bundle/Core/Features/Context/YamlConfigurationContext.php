<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Bundle\Core\Features\Context;

use Behat\Behat\Context\Context;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Yaml\Yaml;

/**
 * Adds extra YAML configuration through ezplatform_behat.yml.
 *
 * New configuration blocks are added to unique files, and added to the imports.
 * Existing configuration strings re-use the same file if applicable.
 */
class YamlConfigurationContext implements Context
{
    /** @var \Symfony\Component\HttpKernel\KernelInterface */
    private $kernel;

    private static $platformConfigurationFilePath = 'config/packages/%env%/ezplatform.yaml';

    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    public function addConfiguration(array $configuration)
    {
        $env = $this->getEnvironment();

        $yamlString = Yaml::dump($configuration, 5, 4);
        $destinationFileName = 'ezplatform_behat_' . sha1($yamlString) . '.yaml';
        $destinationFilePath = "config/packages/{$env}/{$destinationFileName}";

        $configurationDir = dirname($destinationFilePath);
        if (!is_dir($configurationDir)) {
            if (false === @mkdir($configurationDir)) {
                $error = error_get_last();
                throw new RuntimeException(sprintf(
                    'Unable to create the configuration directory "%s": %s',
                    $configurationDir,
                    $error['message']
                ));
            }
        }

        if (!file_exists($destinationFilePath)) {
            file_put_contents($destinationFilePath, $yamlString);
        }

        $this->addImportToPlatformYaml($destinationFileName, $env);
        $this->clearSymfonyCache();
    }

    public function getEnvironment(): string
    {
        return $this->kernel->getEnvironment();
    }

    public function isSymfonyCacheClearRequired(): string
    {
        return 'prod' === $this->getEnvironment();
    }

    private function addImportToPlatformYaml(string $importedFileName, string $env): void
    {
        $filePath = str_replace('%env%', $env, self::$platformConfigurationFilePath);

        if (!file_exists($filePath)) {
            file_put_contents($filePath, Yaml::dump([]));
        }

        $platformConfig = Yaml::parse(file_get_contents($filePath));

        if (!array_key_exists('imports', $platformConfig)) {
            $platformConfig = array_merge(['imports' => []], $platformConfig);
        }

        foreach ($platformConfig['imports'] as $import) {
            if ($import['resource'] == $importedFileName) {
                $importAlreadyExists = true;
            }
        }

        if (!isset($importAlreadyExists)) {
            $platformConfig['imports'][] = ['resource' => $importedFileName];

            file_put_contents(
                $filePath,
                Yaml::dump($platformConfig, 5, 4)
            );
        }
    }

    public function clearSymfonyCache(): void
    {
        $application = new Application($this->kernel);
        $application->setAutoExit(false);

        $input = new ArrayInput(
            [
                'command' => 'cache:clear',
                '--no-warmup' => true,
                '--no-optional-warmers' => true,
            ]
        );

        $application->run($input);
    }
}

class_alias(YamlConfigurationContext::class, 'eZ\Bundle\EzPublishCoreBundle\Features\Context\YamlConfigurationContext');
