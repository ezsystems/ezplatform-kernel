<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Bundle\Core\SiteAccess;

use Ibexa\Bundle\Core\SiteAccess\Matcher as CoreMatcher;
use Ibexa\Bundle\Core\SiteAccess\MatcherBuilder;
use Ibexa\Bundle\Core\SiteAccess\SiteAccessMatcherRegistryInterface;
use Ibexa\Core\MVC\Symfony\Routing\SimplifiedRequest;
use Ibexa\Core\MVC\Symfony\SiteAccess\Matcher;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ibexa\Bundle\Core\SiteAccess\MatcherBuilder
 */
class MatcherBuilderTest extends TestCase
{
    /** @var \PHPUnit\Framework\MockObject\MockObject */
    private $siteAccessMatcherRegistry;

    protected function setUp(): void
    {
        parent::setUp();
        $this->siteAccessMatcherRegistry = $this->createMock(SiteAccessMatcherRegistryInterface::class);
    }

    public function testBuildMatcherNoService()
    {
        $this->siteAccessMatcherRegistry
            ->expects($this->never())
            ->method('getMatcher');
        $matcherBuilder = new MatcherBuilder($this->siteAccessMatcherRegistry);
        $matcher = $this->createMock(Matcher::class);
        $builtMatcher = $matcherBuilder->buildMatcher('\\' . get_class($matcher), [], new SimplifiedRequest());
        $this->assertInstanceOf(get_class($matcher), $builtMatcher);
    }

    public function testBuildMatcherServiceWrongInterface()
    {
        $this->expectException(\TypeError::class);

        $serviceId = 'foo';
        $this->siteAccessMatcherRegistry
            ->expects($this->once())
            ->method('getMatcher')
            ->with($serviceId)
            ->will($this->returnValue($this->createMock(Matcher::class)));
        $matcherBuilder = new MatcherBuilder($this->siteAccessMatcherRegistry);
        $matcherBuilder->buildMatcher("@$serviceId", [], new SimplifiedRequest());
    }

    public function testBuildMatcherService()
    {
        $serviceId = 'foo';
        $matcher = $this->createMock(CoreMatcher::class);
        $this->siteAccessMatcherRegistry
            ->expects($this->once())
            ->method('getMatcher')
            ->with($serviceId)
            ->will($this->returnValue($matcher));

        $matchingConfig = ['foo' => 'bar'];
        $request = new SimplifiedRequest();
        $matcher
            ->expects($this->once())
            ->method('setMatchingConfiguration')
            ->with($matchingConfig);
        $matcher
            ->expects($this->once())
            ->method('setRequest')
            ->with($request);

        $matcherBuilder = new MatcherBuilder($this->siteAccessMatcherRegistry);
        $matcherBuilder->buildMatcher("@$serviceId", $matchingConfig, $request);
    }
}

class_alias(MatcherBuilderTest::class, 'eZ\Bundle\EzPublishCoreBundle\Tests\SiteAccess\MatcherBuilderTest');
