<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Bundle\Core\Matcher;

use Ibexa\Bundle\Core\Matcher\ViewMatcherRegistry;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Core\MVC\Symfony\Matcher\ContentBased\MatcherInterface;
use PHPUnit\Framework\TestCase;

class ViewMatcherRegistryTest extends TestCase
{
    private const MATCHER_NAME = 'test_matcher';

    public function testGetMatcher(): void
    {
        $matcher = $this->getMatcherMock();
        $registry = new ViewMatcherRegistry([self::MATCHER_NAME => $matcher]);

        $this->assertSame($matcher, $registry->getMatcher(self::MATCHER_NAME));
    }

    public function testSetMatcher(): void
    {
        $matcher = $this->getMatcherMock();
        $registry = new ViewMatcherRegistry();

        $registry->setMatcher(self::MATCHER_NAME, $matcher);

        $this->assertSame($matcher, $registry->getMatcher(self::MATCHER_NAME));
    }

    public function testSetMatcherOverride(): void
    {
        $matcher = $this->getMatcherMock();
        $newMatcher = $this->getMatcherMock();
        $registry = new ViewMatcherRegistry([self::MATCHER_NAME, $matcher]);

        $registry->setMatcher(self::MATCHER_NAME, $newMatcher);

        $this->assertSame($newMatcher, $registry->getMatcher(self::MATCHER_NAME));
    }

    public function testGetMatcherNotFound(): void
    {
        $this->expectException(NotFoundException::class);
        $registry = new ViewMatcherRegistry();

        $registry->getMatcher(self::MATCHER_NAME);
    }

    protected function getMatcherMock(): MatcherInterface
    {
        return $this->createMock(MatcherInterface::class);
    }
}

class_alias(ViewMatcherRegistryTest::class, 'eZ\Bundle\EzPublishCoreBundle\Tests\Matcher\ViewMatcherRegistryTest');
