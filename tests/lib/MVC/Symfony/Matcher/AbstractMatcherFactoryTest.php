<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Core\MVC\Symfony\Matcher;

use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Core\MVC\Symfony\View\ContentView;
use Ibexa\Core\Repository\Repository;
use Ibexa\Core\Repository\Values\Content\Content;
use Ibexa\Core\Repository\Values\Content\Location;
use Ibexa\Core\Repository\Values\Content\VersionInfo;
use PHPUnit\Framework\TestCase;

abstract class AbstractMatcherFactoryTest extends TestCase
{
    /**
     * Returns a valid ValueObject (supported by current MatcherFactory), that will match the test rules.
     * i.e. Should return eZ\Publish\API\Repository\Values\Content\Location for LocationMatcherFactory.
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\ValueObject
     */
    abstract protected function getMatchableValueObject();

    /**
     * Returns a valid ValueObject (supported by current MatcherFactory), that won't match the test rules.
     * i.e. Should return eZ\Publish\API\Repository\Values\Content\Location for LocationMatcherFactory.
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\ValueObject
     */
    abstract protected function getNonMatchableValueObject();

    /**
     * Returns the matcher class to use in test configuration.
     * Must be relative to the matcher's ::MATCHER_RELATIVE_NAMESPACE constant.
     * i.e.: Id\\Location.
     *
     * @return string
     */
    abstract protected function getMatcherClass();

    /**
     * @covers \eZ\Publish\Core\MVC\Symfony\Matcher\AbstractMatcherFactory::__construct
     * @covers \eZ\Publish\Core\MVC\Symfony\Matcher\AbstractMatcherFactory::match
     */
    public function testMatchFailNoViewType()
    {
        $matcherFactory = new $this->matcherFactoryClass($this->getRepositoryMock(), []);
        $this->assertNull($matcherFactory->match($this->getContentView(), 'full'));
    }

    /**
     * @covers \eZ\Publish\Core\MVC\Symfony\Matcher\AbstractMatcherFactory::__construct
     * @covers \eZ\Publish\Core\MVC\Symfony\Matcher\AbstractMatcherFactory::match
     * @covers \eZ\Publish\Core\MVC\Symfony\Matcher\AbstractMatcherFactory::getMatcher
     */
    public function testMatchInvalidMatcher()
    {
        $this->expectException(\InvalidArgumentException::class);

        $matcherFactory = new $this->matcherFactoryClass(
            $this->getRepositoryMock(),
            [
                'full' => [
                    'test' => [
                        'template' => 'foo.html.twig',
                        'match' => [
                            'NonExistingMatcher' => true,
                        ],
                    ],
                ],
            ]
        );
        $matcherFactory->match($this->getMatchableValueObject(), 'full');
    }

    /**
     * @covers \eZ\Publish\Core\MVC\Symfony\Matcher\AbstractMatcherFactory::__construct
     * @covers \eZ\Publish\Core\MVC\Symfony\Matcher\AbstractMatcherFactory::match
     * @covers \eZ\Publish\Core\MVC\Symfony\Matcher\AbstractMatcherFactory::getMatcher
     */
    public function testMatch()
    {
        $expectedConfigHash = [
            'template' => 'foo.html.twig',
            'match' => [
                $this->getMatcherClass() => 456,
            ],
        ];
        $matcherFactory = new $this->matcherFactoryClass(
            $this->getRepositoryMock(),
            [
                'full' => [
                    'not_matching' => [
                        'template' => 'bar.html.twig',
                        'match' => [
                            $this->getMatcherClass() => 123,
                        ],
                    ],
                    'test' => $expectedConfigHash,
                ],
            ]
        );
        $configHash = $matcherFactory->match($this->getMatchableValueObject());
        $this->assertArrayHasKey('matcher', $configHash);
        $this->assertInstanceOf(
            constant("$this->matcherFactoryClass::MATCHER_RELATIVE_NAMESPACE") . '\\' . $this->getMatcherClass(),
            $configHash['matcher']
        );
        // Calling a 2nd time to check if the result has been properly cached in memory
        $this->assertSame(
            $configHash,
            $matcherFactory->match(
                $this->getMatchableValueObject(),
                'full'
            )
        );

        unset($configHash['matcher']);
        $this->assertSame($expectedConfigHash, $configHash);
    }

    /**
     * @covers \eZ\Publish\Core\MVC\Symfony\Matcher\AbstractMatcherFactory::__construct
     * @covers \eZ\Publish\Core\MVC\Symfony\Matcher\AbstractMatcherFactory::match
     * @covers \eZ\Publish\Core\MVC\Symfony\Matcher\AbstractMatcherFactory::getMatcher
     */
    public function testMatchFail()
    {
        $matcherFactory = new $this->matcherFactoryClass(
            $this->getRepositoryMock(),
            [
                'full' => [
                    'not_matching' => [
                        'template' => 'bar.html.twig',
                        'match' => [
                            $this->getMatcherClass() => 123,
                        ],
                    ],
                    'test' => [
                        'template' => 'foo.html.twig',
                        'match' => [
                            $this->getMatcherClass() => 456,
                        ],
                    ],
                ],
            ]
        );
        $this->assertNull(
            $matcherFactory->match(
                $this->getNonMatchableValueObject(),
                'full'
            )
        );
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject
     */
    protected function getRepositoryMock()
    {
        return $this->createMock(Repository::class);
    }

    protected function getContentView(
        array $contentInfoProperties = [],
        array $locationProperties = []
    ): ContentView {
        $view = new ContentView();
        $view->setContent(
            new Content(
                [
                    'versionInfo' => new VersionInfo(
                        [
                            'contentInfo' => new ContentInfo($contentInfoProperties),
                        ]
                    ),
                ]
            )
        );
        $view->setLocation(new Location($locationProperties));

        return $view;
    }
}

class_alias(AbstractMatcherFactoryTest::class, 'eZ\Publish\Core\MVC\Symfony\Matcher\Tests\AbstractMatcherFactoryTest');
