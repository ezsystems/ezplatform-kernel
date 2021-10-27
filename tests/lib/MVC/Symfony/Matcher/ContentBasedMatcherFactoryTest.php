<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Core\MVC\Symfony\Matcher;

abstract class ContentBasedMatcherFactoryTest extends AbstractMatcherFactoryTest
{
    /**
     * @covers \eZ\Publish\Core\MVC\Symfony\Matcher\AbstractMatcherFactory::__construct
     * @covers \eZ\Publish\Core\MVC\Symfony\Matcher\AbstractMatcherFactory::match
     * @covers \eZ\Publish\Core\MVC\Symfony\Matcher\AbstractMatcherFactory::getMatcher
     * @covers \eZ\Publish\Core\MVC\Symfony\Matcher\ContentBasedMatcherFactory::getMatcher
     */
    public function testMatchNonContentBasedMatcher()
    {
        $this->expectException(\InvalidArgumentException::class);

        $matcherFactory = new $this->matcherFactoryClass(
            $this->getRepositoryMock(),
            [
                'full' => [
                    'test' => [
                        'template' => 'foo.html.twig',
                        'match' => [
                            \stdClass::class => true,
                        ],
                    ],
                ],
            ]
        );
        $matcherFactory->match($this->getMatchableValueObject(), 'full');
    }
}

class_alias(ContentBasedMatcherFactoryTest::class, 'eZ\Publish\Core\MVC\Symfony\Matcher\Tests\ContentBasedMatcherFactoryTest');
