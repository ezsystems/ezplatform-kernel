<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Core\Persistence\Legacy\Content\FieldValue\Converter;

use Ibexa\Contracts\Core\Persistence\Content\FieldTypeConstraints;
use Ibexa\Contracts\Core\Persistence\Content\Type\FieldDefinition as PersistenceFieldDefinition;
use Ibexa\Core\FieldType\FieldSettings;
use Ibexa\Core\FieldType\Media\Type as MediaType;
use Ibexa\Core\Persistence\Legacy\Content\FieldValue\Converter\MediaConverter;
use Ibexa\Core\Persistence\Legacy\Content\StorageFieldDefinition;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ibexa\Core\Persistence\Legacy\Content\FieldValue\Converter\MediaConverter
 */
class MediaTest extends TestCase
{
    protected $converter;

    protected function setUp(): void
    {
        $this->converter = new MediaConverter();
    }

    /**
     * @group fieldType
     * @group ezmedia
     */
    public function testToStorageFieldDefinition()
    {
        $storageFieldDef = new StorageFieldDefinition();

        $fieldTypeConstraints = new FieldTypeConstraints();
        $fieldTypeConstraints->validators = [
            // Setting max file size to 1MB (1.048.576 bytes)
            'FileSizeValidator' => ['maxFileSize' => 1048576],
        ];
        $fieldTypeConstraints->fieldSettings = new FieldSettings(
            [
                'mediaType' => MediaType::TYPE_HTML5_VIDEO,
            ]
        );

        $fieldDef = new PersistenceFieldDefinition(
            [
                'fieldTypeConstraints' => $fieldTypeConstraints,
                'defaultValue' => null,
            ]
        );

        $this->converter->toStorageFieldDefinition($fieldDef, $storageFieldDef);

        self::assertSame(
            $fieldDef->fieldTypeConstraints->validators['FileSizeValidator'],
            ['maxFileSize' => $storageFieldDef->dataInt1]
        );
        self::assertSame(
            $fieldDef->fieldTypeConstraints->fieldSettings['mediaType'],
            $storageFieldDef->dataText1
        );
    }

    /**
     * @group fieldType
     * @group ezmedia
     */
    public function testToFieldDefinition()
    {
        $fieldDef = new PersistenceFieldDefinition();
        $storageDef = new StorageFieldDefinition(
            [
                'dataInt1' => 1048576,
                'dataText1' => MediaType::TYPE_HTML5_VIDEO,
            ]
        );

        $this->converter->toFieldDefinition($storageDef, $fieldDef);
        self::assertSame(
            [
                'FileSizeValidator' => ['maxFileSize' => $storageDef->dataInt1],
            ],
            $fieldDef->fieldTypeConstraints->validators
        );
        self::assertInstanceOf(FieldSettings::class, $fieldDef->fieldTypeConstraints->fieldSettings);
        self::assertSame(
            ['mediaType' => MediaType::TYPE_HTML5_VIDEO],
            $fieldDef->fieldTypeConstraints->fieldSettings->getArrayCopy()
        );
    }
}

class_alias(MediaTest::class, 'eZ\Publish\Core\Persistence\Legacy\Tests\Content\FieldValue\Converter\MediaTest');
