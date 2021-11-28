<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Core\SiteAccess\Config;

use Ibexa\Bundle\Core\DependencyInjection\Configuration\ComplexSettings\ComplexSettingParser;
use Ibexa\Contracts\Core\SiteAccess\ConfigProcessor;
use Ibexa\Core\MVC\ConfigResolverInterface;
use Ibexa\Core\MVC\Exception\ParameterNotFoundException;
use Ibexa\Core\MVC\Symfony\SiteAccess\SiteAccessService;
use function str_replace;

final class ComplexConfigProcessor implements ConfigProcessor
{
    private const DEFAULT_NAMESPACE = 'ezsettings';

    /** @var \Ibexa\Core\MVC\ConfigResolverInterface */
    private $configResolver;

    /** @var \Ibexa\Core\MVC\Symfony\SiteAccess\SiteAccessService */
    private $siteAccessService;

    /** @var \Ibexa\Bundle\Core\DependencyInjection\Configuration\ComplexSettings\ComplexSettingParserInterface */
    private $complexSettingParser;

    public function __construct(
        ConfigResolverInterface $configResolver,
        SiteAccessService $siteAccessService
    ) {
        $this->configResolver = $configResolver;
        $this->siteAccessService = $siteAccessService;

        // instantiate non-injectable DI configuration parser
        $this->complexSettingParser = new ComplexSettingParser();
    }

    public function processComplexSetting(string $setting): string
    {
        $siteAccessName = $this->siteAccessService->getCurrent()->name;

        if (!$this->configResolver->hasParameter($setting, null, $siteAccessName)) {
            throw new ParameterNotFoundException($setting, null, [$siteAccessName]);
        }

        $settingValue = $this->configResolver->getParameter($setting, null, $siteAccessName);

        if (!$this->complexSettingParser->containsDynamicSettings($settingValue)) {
            return $settingValue;
        }

        // we kind of need to process this as well, don't we ?
        if ($this->complexSettingParser->isDynamicSetting($settingValue)) {
            $parts = $this->complexSettingParser->parseDynamicSetting($settingValue);

            return $this->configResolver->getParameter($parts['param'], null, $siteAccessName);
        }

        return $this->processSettingValue($settingValue);
    }

    public function processSettingValue(string $value): string
    {
        foreach ($this->complexSettingParser->parseComplexSetting($value) as $dynamicSetting) {
            $parts = $this->complexSettingParser->parseDynamicSetting($dynamicSetting);
            if (!isset($parts['namespace'])) {
                $parts['namespace'] = self::DEFAULT_NAMESPACE;
            }

            $dynamicSettingValue = $this->configResolver->getParameter(
                $parts['param'],
                $parts['namespace'],
                $parts['scope'] ?? $this->siteAccessService->getCurrent()->name
            );

            $value = str_replace($dynamicSetting, $dynamicSettingValue, $value);
        }

        return $value;
    }
}

class_alias(ComplexConfigProcessor::class, 'eZ\Bundle\EzPublishCoreBundle\SiteAccess\Config\ComplexConfigProcessor');
