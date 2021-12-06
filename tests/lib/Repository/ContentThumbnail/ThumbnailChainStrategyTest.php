<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Core\Repository\ContentThumbnail;

use Ibexa\Contracts\Core\Repository\Strategy\ContentThumbnail\ThumbnailStrategy;
use Ibexa\Contracts\Core\Repository\Values\Content\Field;
use Ibexa\Contracts\Core\Repository\Values\Content\Thumbnail;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Core\Repository\Strategy\ContentThumbnail\ThumbnailChainStrategy;
use PHPUnit\Framework\TestCase;

class ThumbnailChainStrategyTest extends TestCase
{
    public function testThumbnailStrategyChaining(): void
    {
        $firstStrategyMock = $this->createMock(ThumbnailStrategy::class);
        $secondStrategyMock = $this->createMock(ThumbnailStrategy::class);

        $contentTypeMock = $this->createMock(ContentType::class);
        $fieldMocks = [
            $this->createMock(Field::class),
            $this->createMock(Field::class),
            $this->createMock(Field::class),
        ];

        $firstStrategyMock
            ->expects($this->once())
            ->method('getThumbnail')
            ->willReturn(null);

        $secondStrategyMock
            ->expects($this->once())
            ->method('getThumbnail')
            ->willReturn(new Thumbnail());

        $thumbnailChainStrategy = new ThumbnailChainStrategy([
            $firstStrategyMock,
            $secondStrategyMock,
        ]);

        $result = $thumbnailChainStrategy->getThumbnail(
            $contentTypeMock,
            $fieldMocks
        );

        $this->assertInstanceOf(Thumbnail::class, $result);
    }

    public function testThumbnailStrategyChainBreakOnThumbnailFound(): void
    {
        $firstStrategyMock = $this->createMock(ThumbnailStrategy::class);
        $secondStrategyMock = $this->createMock(ThumbnailStrategy::class);
        $thirdStrategyMock = $this->createMock(ThumbnailStrategy::class);

        $contentTypeMock = $this->createMock(ContentType::class);
        $fieldMocks = [
            $this->createMock(Field::class),
            $this->createMock(Field::class),
            $this->createMock(Field::class),
        ];

        $firstStrategyMock
            ->expects($this->once())
            ->method('getThumbnail')
            ->willReturn(null);

        $secondStrategyMock
            ->expects($this->once())
            ->method('getThumbnail')
            ->willReturn(new Thumbnail([
                'resource' => 'second',
            ]));

        $thirdStrategyMock
            ->expects($this->never())
            ->method('getThumbnail')
            ->willReturn(new Thumbnail([
                'resource' => 'third',
            ]));

        $thumbnailChainStrategy = new ThumbnailChainStrategy([
            $firstStrategyMock,
            $secondStrategyMock,
            $thirdStrategyMock,
        ]);

        $result = $thumbnailChainStrategy->getThumbnail(
            $contentTypeMock,
            $fieldMocks
        );

        $this->assertInstanceOf(Thumbnail::class, $result);
        $this->assertEquals(new Thumbnail(['resource' => 'second']), $result);
    }
}

class_alias(ThumbnailChainStrategyTest::class, 'eZ\Publish\Core\Repository\Tests\ContentThumbnail\ThumbnailChainStrategyTest');
