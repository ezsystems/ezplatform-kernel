<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Bundle\Core\DependencyInjection\Configuration\Suggestion\Formatter;

use Ibexa\Bundle\Core\DependencyInjection\Configuration\Suggestion\ConfigSuggestion;

/**
 * Interface for ConfigSuggestion formatters.
 *
 * A SuggestionFormatter will convert a ConfigSuggestion value object to a human readable format
 * (e.g. YAML, XML, JSON...).
 */
interface SuggestionFormatterInterface
{
    public function format(ConfigSuggestion $configSuggestion);
}

class_alias(SuggestionFormatterInterface::class, 'eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\Suggestion\Formatter\SuggestionFormatterInterface');
