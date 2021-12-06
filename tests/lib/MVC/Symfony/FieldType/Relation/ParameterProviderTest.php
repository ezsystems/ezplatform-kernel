<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Core\MVC\Symfony\FieldType\Relation;

use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Contracts\Core\Repository\Values\Content\Field;
use Ibexa\Core\Base\Exceptions\NotFoundException;
use Ibexa\Core\Base\Exceptions\UnauthorizedException;
use Ibexa\Core\FieldType\Relation\Value;
use Ibexa\Core\MVC\Symfony\FieldType\Relation\ParameterProvider;
use PHPUnit\Framework\TestCase;

class ParameterProviderTest extends TestCase
{
    public function providerForTestGetViewParameters()
    {
        return [
            [ContentInfo::STATUS_DRAFT, ['available' => true]],
            [ContentInfo::STATUS_PUBLISHED, ['available' => true]],
            [ContentInfo::STATUS_TRASHED, ['available' => false]],
        ];
    }

    /**
     * @dataProvider providerForTestGetViewParameters
     */
    public function testGetViewParameters($status, array $expected)
    {
        $contentServiceMock = $this->createMock(ContentService::class);
        $contentServiceMock
            ->method('loadContentInfo')
            ->will(TestCase::returnValue(
                new ContentInfo(['status' => $status])
            ));

        $parameterProvider = new ParameterProvider($contentServiceMock);
        $parameters = $parameterProvider->getViewParameters(new Field([
            'value' => new Value(123),
        ]));

        TestCase::assertSame($parameters, $expected);
    }

    public function testNotFoundGetViewParameters()
    {
        $contentId = 123;

        $contentServiceMock = $this->createMock(ContentService::class);
        $contentServiceMock
            ->method('loadContentInfo')
            ->will(TestCase::throwException(new NotFoundException('ContentInfo', $contentId)));

        $parameterProvider = new ParameterProvider($contentServiceMock);
        $parameters = $parameterProvider->getViewParameters(new Field([
            'value' => new Value($contentId),
        ]));

        TestCase::assertSame($parameters, ['available' => false]);
    }

    public function testUnauthorizedGetViewParameters()
    {
        $contentId = 123;

        $contentServiceMock = $this->createMock(ContentService::class);
        $contentServiceMock
            ->method('loadContentInfo')
            ->will(TestCase::throwException(new UnauthorizedException('content', 'read')));

        $parameterProvider = new ParameterProvider($contentServiceMock);
        $parameters = $parameterProvider->getViewParameters(new Field([
            'value' => new Value($contentId),
        ]));

        TestCase::assertSame($parameters, ['available' => false]);
    }
}

class_alias(ParameterProviderTest::class, 'eZ\Publish\Core\MVC\Symfony\FieldType\Tests\Relation\ParameterProviderTest');
