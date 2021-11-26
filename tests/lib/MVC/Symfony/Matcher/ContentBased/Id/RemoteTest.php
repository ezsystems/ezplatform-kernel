<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Core\MVC\Symfony\Matcher\ContentBased\Id;

use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Core\MVC\Symfony\Matcher\ContentBased\Id\Remote as RemoteIdMatcher;
use Ibexa\Tests\Core\MVC\Symfony\Matcher\ContentBased\BaseTest;

class RemoteTest extends BaseTest
{
    /** @var \Ibexa\Core\MVC\Symfony\Matcher\ContentBased\Id\Remote */
    private $matcher;

    protected function setUp(): void
    {
        parent::setUp();
        $this->matcher = new RemoteIdMatcher();
    }

    /**
     * @dataProvider matchLocationProvider
     * @covers \Ibexa\Core\MVC\Symfony\Matcher\ContentBased\Id\Remote::matchLocation
     * @covers \Ibexa\Core\MVC\Symfony\Matcher\ContentBased\MultipleValued::setMatchingConfig
     *
     * @param string|string[] $matchingConfig
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Location $location
     * @param bool $expectedResult
     */
    public function testMatchLocation($matchingConfig, Location $location, $expectedResult)
    {
        $this->matcher->setMatchingConfig($matchingConfig);
        $this->assertSame($expectedResult, $this->matcher->matchLocation($location));
    }

    public function matchLocationProvider()
    {
        return [
            [
                'foo',
                $this->getLocationMock(['remoteId' => 'foo']),
                true,
            ],
            [
                'foo',
                $this->getLocationMock(['remoteId' => 'bar']),
                false,
            ],
            [
                ['foo', 'baz'],
                $this->getLocationMock(['remoteId' => 'bar']),
                false,
            ],
            [
                ['foo', 'baz'],
                $this->getLocationMock(['remoteId' => 'baz']),
                true,
            ],
        ];
    }

    /**
     * @dataProvider matchContentInfoProvider
     * @covers \Ibexa\Core\MVC\Symfony\Matcher\ContentBased\Id\Remote::matchContentInfo
     * @covers \Ibexa\Core\MVC\Symfony\Matcher\ContentBased\MultipleValued::setMatchingConfig
     *
     * @param string|string[] $matchingConfig
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo $contentInfo
     * @param bool $expectedResult
     */
    public function testMatchContentInfo($matchingConfig, ContentInfo $contentInfo, $expectedResult)
    {
        $this->matcher->setMatchingConfig($matchingConfig);
        $this->assertSame($expectedResult, $this->matcher->matchContentInfo($contentInfo));
    }

    public function matchContentInfoProvider()
    {
        return [
            [
                'foo',
                $this->getContentInfoMock(['remoteId' => 'foo']),
                true,
            ],
            [
                'foo',
                $this->getContentInfoMock(['remoteId' => 'bar']),
                false,
            ],
            [
                ['foo', 'baz'],
                $this->getContentInfoMock(['remoteId' => 'bar']),
                false,
            ],
            [
                ['foo', 'baz'],
                $this->getContentInfoMock(['remoteId' => 'baz']),
                true,
            ],
        ];
    }
}

class_alias(RemoteTest::class, 'eZ\Publish\Core\MVC\Symfony\Matcher\Tests\ContentBased\Id\RemoteTest');
