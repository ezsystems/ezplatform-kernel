<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Core\MVC\Symfony\FieldType\RelationList;

use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Contracts\Core\Repository\Values\Content\Field;
use Ibexa\Core\FieldType\RelationList\Value;
use Ibexa\Core\MVC\Symfony\FieldType\RelationList\ParameterProvider;
use PHPUnit\Framework\TestCase;

class ParameterProviderTest extends TestCase
{
    public function providerForTestGetViewParameters()
    {
        return [
            [[123, 456, 789], ['available' => [123 => true, 456 => true, 789 => false]]],
            [[123, 456], ['available' => [123 => true, 456 => true]]],
            [[789], ['available' => [789 => false]]],
            [[], ['available' => []]],
        ];
    }

    /**
     * @dataProvider providerForTestGetViewParameters
     */
    public function testGetViewParameters(array $desinationContentIds, array $expected)
    {
        $contentServiceMock = $this->createMock(ContentService::class);
        $contentServiceMock
            ->method('loadContentInfoList')
            ->with($desinationContentIds)
            ->will($this->returnCallback(static function ($arg) {
                $return = [];
                if (in_array(123, $arg)) {
                    $return[123] = new ContentInfo(['status' => ContentInfo::STATUS_DRAFT]);
                }

                if (in_array(456, $arg)) {
                    $return[456] = new ContentInfo(['status' => ContentInfo::STATUS_PUBLISHED]);
                }

                if (in_array(789, $arg)) {
                    $return[789] = new ContentInfo(['status' => ContentInfo::STATUS_TRASHED]);
                }

                return $return;
            }));

        $parameterProvider = new ParameterProvider($contentServiceMock);
        $parameters = $parameterProvider->getViewParameters(new Field([
            'value' => new Value($desinationContentIds),
        ]));

        TestCase::assertSame($parameters, $expected);
    }

    public function testNotFoundGetViewParameters()
    {
        $contentId = 123;

        $contentServiceMock = $this->createMock(ContentService::class);
        $contentServiceMock
            ->method('loadContentInfoList')
            ->with([$contentId])
            ->willReturn([]);

        $parameterProvider = new ParameterProvider($contentServiceMock);
        $parameters = $parameterProvider->getViewParameters(new Field([
            'value' => new Value([$contentId]),
        ]));

        TestCase::assertSame($parameters, ['available' => [$contentId => false]]);
    }

    public function testUnauthorizedGetViewParameters()
    {
        $contentId = 123;

        $contentServiceMock = $this->createMock(ContentService::class);
        $contentServiceMock
            ->method('loadContentInfoList')
            ->with([$contentId])
            ->willReturn([]);

        $parameterProvider = new ParameterProvider($contentServiceMock);
        $parameters = $parameterProvider->getViewParameters(new Field([
            'value' => new Value([$contentId]),
        ]));

        TestCase::assertSame($parameters, ['available' => [$contentId => false]]);
    }
}

class_alias(ParameterProviderTest::class, 'eZ\Publish\Core\MVC\Symfony\FieldType\Tests\RelationList\ParameterProviderTest');
