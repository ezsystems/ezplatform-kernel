<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Core\Matcher;

use Ibexa\Core\Base\Exceptions\NotFoundException;
use Ibexa\Core\MVC\Symfony\Matcher\ViewMatcherInterface;

final class ViewMatcherRegistry
{
    /** @var \Ibexa\Core\MVC\Symfony\Matcher\ViewMatcherInterface[] */
    private $matchers;

    /**
     * @param \Ibexa\Core\MVC\Symfony\Matcher\ViewMatcherInterface[] $matchers
     */
    public function __construct(array $matchers = [])
    {
        $this->matchers = $matchers;
    }

    public function setMatcher(string $matcherIdentifier, ViewMatcherInterface $matcher): void
    {
        $this->matchers[$matcherIdentifier] = $matcher;
    }

    /**
     * @param string $matcherIdentifier
     *
     * @return \Ibexa\Core\MVC\Symfony\Matcher\ViewMatcherInterface
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    public function getMatcher(string $matcherIdentifier): ViewMatcherInterface
    {
        if (!isset($this->matchers[$matcherIdentifier])) {
            throw new NotFoundException('Matcher', $matcherIdentifier);
        }

        return $this->matchers[$matcherIdentifier];
    }
}

class_alias(ViewMatcherRegistry::class, 'eZ\Bundle\EzPublishCoreBundle\Matcher\ViewMatcherRegistry');
