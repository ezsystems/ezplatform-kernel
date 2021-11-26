<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Core\Persistence\Legacy\Content\FieldValue\Converter;

use Ibexa\Contracts\Core\Persistence\Content\FieldTypeConstraints;
use Ibexa\Contracts\Core\Persistence\Content\FieldValue;
use Ibexa\Contracts\Core\Persistence\Content\Type\FieldDefinition as PersistenceFieldDefinition;
use Ibexa\Core\FieldType\FieldSettings;
use Ibexa\Core\Persistence\Legacy\Content\FieldValue\Converter\CountryConverter;
use Ibexa\Core\Persistence\Legacy\Content\StorageFieldDefinition;
use Ibexa\Core\Persistence\Legacy\Content\StorageFieldValue;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ibexa\Core\Persistence\Legacy\Content\FieldValue\Converter\CountryConverter
 */
class CountryTest extends TestCase
{
    /** @var \Ibexa\Core\Persistence\Legacy\Content\FieldValue\Converter\CountryConverter */
    protected $converter;

    protected function setUp(): void
    {
        parent::setUp();
        $this->converter = new CountryConverter();
    }

    public function providerForTestToStorageValue()
    {
        return [
            [['BE', 'FR'], 'belgium,france', 'BE,FR', 'belgium,france'],
            [null, '', '', ''],
        ];
    }

    /**
     * @group fieldType
     * @group country
     * @dataProvider providerForTestToStorageValue
     */
    public function testToStorageValue($data, $sortKey, $dataText, $sortKeyString)
    {
        $value = new FieldValue();
        $value->data = $data;
        $value->sortKey = $sortKey;
        $storageFieldValue = new StorageFieldValue();

        $this->converter->toStorageValue($value, $storageFieldValue);
        self::assertSame($dataText, $storageFieldValue->dataText);
        self::assertSame($sortKeyString, $storageFieldValue->sortKeyString);
    }

    public function providerForTestToFieldValue()
    {
        return [
            ['BE,FR', 'belgium,france', ['BE', 'FR']],
            ['', '', null],
        ];
    }

    /**
     * @group fieldType
     * @group country
     * @dataProvider providerForTestToFieldValue
     */
    public function testToFieldValue($dataText, $sortKeyString, $data)
    {
        $storageFieldValue = new StorageFieldValue();
        $storageFieldValue->dataText = $dataText;
        $storageFieldValue->sortKeyString = $sortKeyString;
        $fieldValue = new FieldValue();

        $this->converter->toFieldValue($storageFieldValue, $fieldValue);
        self::assertSame($data, $fieldValue->data);
    }

    /**
     * @group fieldType
     * @group country
     */
    public function testToStorageFieldDefinitionMultiple()
    {
        $defaultValue = new FieldValue();
        $defaultValue->data = ['BE', 'FR'];
        $fieldTypeConstraints = new FieldTypeConstraints();
        $fieldTypeConstraints->fieldSettings = new FieldSettings(
            [
                'isMultiple' => true,
            ]
        );

        $storageFieldDef = new StorageFieldDefinition();
        $this->converter->toStorageFieldDefinition(
            new PersistenceFieldDefinition(
                [
                    'fieldTypeConstraints' => $fieldTypeConstraints,
                    'defaultValue' => $defaultValue,
                ]
            ),
            $storageFieldDef
        );
        self::assertSame(
            1,
            $storageFieldDef->dataInt1
        );
        self::assertSame(
            'BE,FR',
            $storageFieldDef->dataText5
        );
    }

    /**
     * @group fieldType
     * @group country
     */
    public function testToStorageFieldDefinitionSingle()
    {
        $fieldTypeConstraints = new FieldTypeConstraints();
        $fieldTypeConstraints->fieldSettings = new FieldSettings(
            [
                'isMultiple' => false,
            ]
        );

        $storageFieldDef = new StorageFieldDefinition();
        $this->converter->toStorageFieldDefinition(
            new PersistenceFieldDefinition(
                [
                    'fieldTypeConstraints' => $fieldTypeConstraints,
                ]
            ),
            $storageFieldDef
        );
        self::assertSame(
            0,
            $storageFieldDef->dataInt1
        );
        self::assertEmpty(
            $storageFieldDef->dataText5
        );
    }

    /**
     * @group fieldType
     * @group country
     */
    public function testToFieldDefinitionMultiple()
    {
        $fieldDef = new PersistenceFieldDefinition();

        $this->converter->toFieldDefinition(
            new StorageFieldDefinition(
                [
                    'dataInt1' => 1,
                    'dataText5' => 'BE,FR',
                ]
            ),
            $fieldDef
        );
        self::assertInstanceOf(FieldSettings::class, $fieldDef->fieldTypeConstraints->fieldSettings);
        self::assertTrue(
            $fieldDef->fieldTypeConstraints->fieldSettings['isMultiple']
        );
        self::assertEquals(
            ['BE', 'FR'],
            $fieldDef->defaultValue->data
        );
    }

    /**
     * @group fieldType
     * @group country
     */
    public function testToFieldDefinitionSingle()
    {
        $fieldDef = new PersistenceFieldDefinition();

        $this->converter->toFieldDefinition(
            new StorageFieldDefinition(
                [
                    'dataInt1' => 0,
                    'dataText5' => '',
                ]
            ),
            $fieldDef
        );
        self::assertInstanceOf(FieldSettings::class, $fieldDef->fieldTypeConstraints->fieldSettings);
        self::assertFalse(
            $fieldDef->fieldTypeConstraints->fieldSettings['isMultiple']
        );
        self::assertNull(
            $fieldDef->defaultValue->data
        );
    }
}

class_alias(CountryTest::class, 'eZ\Publish\Core\Persistence\Legacy\Tests\Content\FieldValue\Converter\CountryTest');
