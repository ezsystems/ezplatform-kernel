<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Core\Persistence\FieldValue\Converter;

use Ibexa\Contracts\Core\Persistence\Content\FieldTypeConstraints;
use Ibexa\Contracts\Core\Persistence\Content\Type\FieldDefinition;
use Ibexa\Core\IO\IOServiceInterface;
use Ibexa\Core\IO\UrlRedecoratorInterface;
use Ibexa\Core\Persistence\Legacy\Content\FieldValue\Converter\ImageConverter;
use Ibexa\Core\Persistence\Legacy\Content\StorageFieldDefinition;
use PHPUnit\Framework\TestCase;

final class ImageConverterTest extends TestCase
{
    /** @var \Ibexa\Core\Persistence\Legacy\Content\FieldValue\Converter\ImageConverter */
    private $imageConverter;

    /** @var \Ibexa\Core\IO\UrlRedecoratorInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $urlRedecorator;

    /** @var \Ibexa\Core\IO\IOServiceInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $ioService;

    protected function setUp(): void
    {
        $this->ioService = $this->createMock(IOServiceInterface::class);
        $this->urlRedecorator = $this->createMock(UrlRedecoratorInterface::class);

        $this->imageConverter = new ImageConverter(
            $this->ioService,
            $this->urlRedecorator
        );
    }

    /**
     * @dataProvider dataProviderForTestToStorageFieldDefinition
     */
    public function testToStorageFieldDefinition(
        FieldDefinition $fieldDefinition,
        StorageFieldDefinition $expectedStorageDef
    ): void {
        $storageFieldDefinition = new StorageFieldDefinition();

        $this->imageConverter->toStorageFieldDefinition($fieldDefinition, $storageFieldDefinition);

        self::assertEquals(
            $expectedStorageDef,
            $storageFieldDefinition
        );
    }

    public function dataProviderForTestToStorageFieldDefinition(): iterable
    {
        yield [
            new FieldDefinition([
                'fieldTypeConstraints' => new FieldTypeConstraints([
                    'validators' => [],
                ]),
            ]),
            new StorageFieldDefinition([
                'dataInt1' => 0,
                'dataInt2' => 0,
            ]),
        ];

        yield [
            new FieldDefinition([
                'fieldTypeConstraints' => new FieldTypeConstraints([
                    'validators' => [
                        'FileSizeValidator' => [
                            'maxFileSize' => 1024,
                        ],
                    ],
                ]),
            ]),
            new StorageFieldDefinition([
                'dataInt1' => 1024,
                'dataInt2' => 0,
            ]),
        ];

        yield [
            new FieldDefinition([
                'fieldTypeConstraints' => new FieldTypeConstraints([
                    'validators' => [
                        'AlternativeTextValidator' => [
                            'required' => true,
                        ],
                    ],
                ]),
            ]),
            new StorageFieldDefinition([
                'dataInt1' => 0,
                'dataInt2' => 1,
            ]),
        ];

        yield [
            new FieldDefinition([
                'fieldTypeConstraints' => new FieldTypeConstraints([
                    'validators' => [
                        'AlternativeTextValidator' => [
                            'required' => false,
                        ],
                    ],
                ]),
            ]),
            new StorageFieldDefinition([
                'dataInt1' => 0,
                'dataInt2' => 0,
            ]),
        ];
    }

    /**
     * @dataProvider dataProviderForTestToFieldDefinition
     */
    public function testToFieldDefinition(
        StorageFieldDefinition $storageDef,
        FieldDefinition $expectedFieldDefinition
    ): void {
        $fieldDefinition = new FieldDefinition();

        $this->imageConverter->toFieldDefinition($storageDef, $fieldDefinition);

        self::assertEquals(
            $expectedFieldDefinition,
            $fieldDefinition
        );
    }

    public function dataProviderForTestToFieldDefinition(): iterable
    {
        yield [
            new StorageFieldDefinition([
                'dataInt1' => 0,
                'dataInt2' => 0,
            ]),
            new FieldDefinition([
                'fieldTypeConstraints' => new FieldTypeConstraints([
                    'validators' => [
                        'FileSizeValidator' => [
                            'maxFileSize' => null,
                        ],
                        'AlternativeTextValidator' => [
                            'required' => false,
                        ],
                    ],
                ]),
            ]),
        ];

        yield [
            new StorageFieldDefinition([
                'dataInt1' => 1024,
                'dataInt2' => 1,
            ]),
            new FieldDefinition([
                'fieldTypeConstraints' => new FieldTypeConstraints([
                    'validators' => [
                        'FileSizeValidator' => [
                            'maxFileSize' => 1024,
                        ],
                        'AlternativeTextValidator' => [
                            'required' => true,
                        ],
                    ],
                ]),
            ]),
        ];
    }
}

class_alias(ImageConverterTest::class, 'eZ\Publish\Core\Persistence\Tests\FieldValue\Converter\ImageConverterTest');
