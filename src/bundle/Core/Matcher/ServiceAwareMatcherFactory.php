<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Bundle\Core\Matcher;

use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Core\MVC\Symfony\Matcher\ClassNameMatcherFactory;

/**
 * A view matcher factory that also accepts services as matchers.
 *
 * If a service id is passed as the MatcherIdentifier, this service will be used for the matching.
 * Otherwise, it will fallback to the class name based matcher factory.
 */
final class ServiceAwareMatcherFactory extends ClassNameMatcherFactory
{
    /** @var \Ibexa\Bundle\Core\Matcher\ViewMatcherRegistry */
    private $viewMatcherRegistry;

    public function __construct(
        ViewMatcherRegistry $viewMatcherRegistry,
        Repository $repository,
        $relativeNamespace = null,
        array $matchConfig = []
    ) {
        $this->viewMatcherRegistry = $viewMatcherRegistry;

        parent::__construct($repository, $relativeNamespace, $matchConfig);
    }

    /**
     * @param string $matcherIdentifier
     *
     * @return \Ibexa\Core\MVC\Symfony\Matcher\ContentBased\MatcherInterface
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    protected function getMatcher($matcherIdentifier)
    {
        if (strpos($matcherIdentifier, '@') === 0) {
            return $this->viewMatcherRegistry->getMatcher(substr($matcherIdentifier, 1));
        }

        return parent::getMatcher($matcherIdentifier);
    }
}

class_alias(ServiceAwareMatcherFactory::class, 'eZ\Bundle\EzPublishCoreBundle\Matcher\ServiceAwareMatcherFactory');
