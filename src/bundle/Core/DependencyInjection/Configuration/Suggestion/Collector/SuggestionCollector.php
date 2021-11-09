<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Bundle\Core\DependencyInjection\Configuration\Suggestion\Collector;

use Ibexa\Bundle\Core\DependencyInjection\Configuration\Suggestion\ConfigSuggestion;

class SuggestionCollector implements SuggestionCollectorInterface
{
    /** @var \Ibexa\Bundle\Core\DependencyInjection\Configuration\Suggestion\ConfigSuggestion[] */
    private $suggestions = [];

    /**
     * Adds a config suggestion to the list.
     *
     * @param \Ibexa\Bundle\Core\DependencyInjection\Configuration\Suggestion\ConfigSuggestion $suggestion
     */
    public function addSuggestion(ConfigSuggestion $suggestion)
    {
        $this->suggestions[] = $suggestion;
    }

    /**
     * Returns all config suggestions.
     *
     * @return \Ibexa\Bundle\Core\DependencyInjection\Configuration\Suggestion\ConfigSuggestion[]
     */
    public function getSuggestions()
    {
        return $this->suggestions;
    }

    /**
     * @return bool
     */
    public function hasSuggestions()
    {
        return !empty($this->suggestions);
    }
}

class_alias(SuggestionCollector::class, 'eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\Suggestion\Collector\SuggestionCollector');
