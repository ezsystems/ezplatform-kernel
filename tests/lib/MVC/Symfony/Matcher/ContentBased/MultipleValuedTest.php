<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Core\MVC\Symfony\Matcher\ContentBased;

use Ibexa\Core\MVC\Symfony\Matcher\ContentBased\MultipleValued;

class MultipleValuedTest extends BaseTest
{
    /**
     * @dataProvider matchingConfigProvider
     * @covers \Ibexa\Core\MVC\Symfony\Matcher\ContentBased\MultipleValued::setMatchingConfig
     * @covers \Ibexa\Core\MVC\Symfony\Matcher\ContentBased\MultipleValued::getValues
     */
    public function testSetMatchingConfig($matchingConfig)
    {
        $matcher = $this->getMultipleValuedMatcherMock();
        $matcher->setMatchingConfig($matchingConfig);
        $values = $matcher->getValues();
        $this->assertIsArray($values);

        $matchingConfig = is_array($matchingConfig) ? $matchingConfig : [$matchingConfig];
        foreach ($matchingConfig as $val) {
            $this->assertContains($val, $values);
        }
    }

    /**
     * Returns a set of matching values, either single or multiple.
     *
     * @return array
     */
    public function matchingConfigProvider()
    {
        return [
            [
                'singleValue',
                ['one', 'two', 'three'],
                [123, 'nous irons au bois'],
                456,
            ],
        ];
    }

    /**
     * @covers \Ibexa\Core\MVC\RepositoryAware::setRepository
     * @covers \Ibexa\Core\MVC\Symfony\Matcher\ContentBased\MultipleValued::getRepository
     */
    public function testInjectRepository()
    {
        $matcher = $this->getMultipleValuedMatcherMock();
        $matcher->setRepository($this->repositoryMock);
        $this->assertSame($this->repositoryMock, $matcher->getRepository());
    }

    private function getMultipleValuedMatcherMock()
    {
        return $this->getMockForAbstractClass(MultipleValued::class);
    }
}

class_alias(MultipleValuedTest::class, 'eZ\Publish\Core\MVC\Symfony\Matcher\Tests\ContentBased\MultipleValuedTest');
