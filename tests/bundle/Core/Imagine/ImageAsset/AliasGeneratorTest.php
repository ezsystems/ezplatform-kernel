<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Core\Imagine\ImageAsset;

use Ibexa\Bundle\Core\Imagine\ImageAsset\AliasGenerator;
use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\Values\Content\Field;
use Ibexa\Contracts\Core\Variation\Values\Variation;
use Ibexa\Contracts\Core\Variation\VariationHandler;
use Ibexa\Core\FieldType\Image;
use Ibexa\Core\FieldType\ImageAsset;
use Ibexa\Core\Repository\Values\Content\Content;
use Ibexa\Core\Repository\Values\Content\VersionInfo;
use PHPUnit\Framework\TestCase;

class AliasGeneratorTest extends TestCase
{
    /** @var \Ibexa\Bundle\Core\Imagine\ImageAsset\AliasGenerator */
    private $aliasGenerator;

    /** @var \Ibexa\Contracts\Core\Variation\VariationHandler|\PHPUnit\Framework\MockObject\MockObject */
    private $innerAliasGenerator;

    /** @var \Ibexa\Contracts\Core\Repository\ContentService|\PHPUnit\Framework\MockObject\MockObject */
    private $contentService;

    /** @var \Ibexa\Core\FieldType\ImageAsset\AssetMapper|\PHPUnit\Framework\MockObject\MockObject */
    private $assetMapper;

    protected function setUp(): void
    {
        $this->innerAliasGenerator = $this->createMock(VariationHandler::class);
        $this->contentService = $this->createMock(ContentService::class);
        $this->assetMapper = $this->createMock(ImageAsset\AssetMapper::class);

        $this->aliasGenerator = new AliasGenerator(
            $this->innerAliasGenerator,
            $this->contentService,
            $this->assetMapper
        );
    }

    public function testGetVariationOfImageAsset()
    {
        $assetField = new Field([
            'value' => new ImageAsset\Value(486),
        ]);
        $imageField = new Field([
            'value' => new Image\Value([
                'id' => 'images/6/8/4/0/486-10-eng-GB/photo.jpg',
            ]),
        ]);

        $assetVersionInfo = new VersionInfo();
        $imageVersionInfo = new VersionInfo();
        $imageContent = new Content([
            'versionInfo' => $imageVersionInfo,
        ]);

        $variationName = 'thumbnail';
        $parameters = [];

        $expectedVariation = new Variation();

        $this->contentService
            ->expects($this->once())
            ->method('loadContent')
            ->with($assetField->value->destinationContentId)
            ->willReturn($imageContent);

        $this->assetMapper
            ->expects($this->once())
            ->method('getAssetField')
            ->with($imageContent)
            ->willReturn($imageField);

        $this->innerAliasGenerator
            ->expects($this->once())
            ->method('getVariation')
            ->with($imageField, $imageVersionInfo, $variationName, $parameters)
            ->willReturn($expectedVariation);

        $actualVariation = $this->aliasGenerator->getVariation(
            $assetField,
            $assetVersionInfo,
            $variationName,
            $parameters
        );

        $this->assertEquals($expectedVariation, $actualVariation);
    }

    public function testGetVariationOfNonImageAsset()
    {
        $imageField = new Field([
            'value' => new Image\Value([
                'id' => 'images/6/8/4/0/486-10-eng-GB/photo.jpg',
            ]),
        ]);

        $imageVersionInfo = new VersionInfo();
        $variationName = 'thumbnail';
        $parameters = [];

        $expectedVariation = new Variation();

        $this->contentService
            ->expects($this->never())
            ->method('loadContent');

        $this->assetMapper
            ->expects($this->never())
            ->method('getAssetField');

        $this->innerAliasGenerator
            ->expects($this->once())
            ->method('getVariation')
            ->with($imageField, $imageVersionInfo, $variationName, $parameters)
            ->willReturn($expectedVariation);

        $actualVariation = $this->aliasGenerator->getVariation(
            $imageField,
            $imageVersionInfo,
            $variationName,
            $parameters
        );

        $this->assertEquals($expectedVariation, $actualVariation);
    }

    public function testSupport()
    {
        $this->assertTrue($this->aliasGenerator->supportsValue(new ImageAsset\Value()));
        $this->assertFalse($this->aliasGenerator->supportsValue(new Image\Value()));
    }
}

class_alias(AliasGeneratorTest::class, 'eZ\Bundle\EzPublishCoreBundle\Tests\Imagine\ImageAsset\AliasGeneratorTest');
