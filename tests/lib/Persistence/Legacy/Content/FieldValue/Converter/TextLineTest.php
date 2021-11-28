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
use Ibexa\Core\Persistence\Legacy\Content\FieldValue\Converter\TextLineConverter;
use Ibexa\Core\Persistence\Legacy\Content\StorageFieldDefinition;
use Ibexa\Core\Persistence\Legacy\Content\StorageFieldValue;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ibexa\Core\Persistence\Legacy\Content\FieldValue\Converter\TextLineConverter
 */
class TextLineTest extends TestCase
{
    /** @var \Ibexa\Core\Persistence\Legacy\Content\FieldValue\Converter\TextLineConverter */
    protected $converter;

    protected function setUp(): void
    {
        parent::setUp();
        $this->converter = new TextLineConverter();
    }

    /**
     * @group fieldType
     * @group textLine
     */
    public function testToStorageValue()
    {
        $value = new FieldValue();
        $value->data = "He's holding a thermal detonator!";
        $value->sortKey = "He's holding";
        $storageFieldValue = new StorageFieldValue();

        $this->converter->toStorageValue($value, $storageFieldValue);
        self::assertSame($value->data, $storageFieldValue->dataText);
        self::assertSame($value->sortKey, $storageFieldValue->sortKeyString);
        self::assertSame(0, $storageFieldValue->sortKeyInt);
    }

    /**
     * @group fieldType
     * @group textLine
     */
    public function testToFieldValue()
    {
        $storageFieldValue = new StorageFieldValue();
        $storageFieldValue->dataText = 'When 900 years old, you reach... Look as good, you will not.';
        $storageFieldValue->sortKeyString = 'When 900 years old, you reach...';
        $storageFieldValue->sortKeyInt = 0;
        $fieldValue = new FieldValue();

        $this->converter->toFieldValue($storageFieldValue, $fieldValue);
        self::assertSame($storageFieldValue->dataText, $fieldValue->data);
        self::assertSame($storageFieldValue->sortKeyString, $fieldValue->sortKey);
    }

    /**
     * @group fieldType
     * @group textLine
     */
    public function testToStorageFieldDefinitionWithValidator()
    {
        $defaultText = 'This is a default text';
        $storageFieldDef = new StorageFieldDefinition();
        $defaultValue = new FieldValue();
        $defaultValue->data = $defaultText;
        $fieldTypeConstraints = new FieldTypeConstraints();
        $fieldTypeConstraints->validators = [
            TextLineConverter::STRING_LENGTH_VALIDATOR_IDENTIFIER => ['maxStringLength' => 100],
        ];
        $fieldTypeConstraints->fieldSettings = new FieldSettings(
            [
                'defaultText' => $defaultText,
            ]
        );
        $fieldDef = new PersistenceFieldDefinition(
            [
                'fieldTypeConstraints' => $fieldTypeConstraints,
                'defaultValue' => $defaultValue,
            ]
        );

        $this->converter->toStorageFieldDefinition($fieldDef, $storageFieldDef);
        self::assertSame(
            $fieldDef->fieldTypeConstraints->validators[TextLineConverter::STRING_LENGTH_VALIDATOR_IDENTIFIER],
            ['maxStringLength' => $storageFieldDef->dataInt1]
        );
        self::assertSame(
            $fieldDef->fieldTypeConstraints->fieldSettings['defaultText'],
            $storageFieldDef->dataText1
        );
    }

    /**
     * @group fieldType
     * @group textLine
     */
    public function testToStorageFieldDefinitionNoValidator()
    {
        $defaultText = 'This is a default text';
        $storageFieldDef = new StorageFieldDefinition();
        $defaultValue = new FieldValue();
        $defaultValue->data = $defaultText;
        $fieldTypeConstraints = new FieldTypeConstraints();
        $fieldDef = new PersistenceFieldDefinition(
            [
                'fieldTypeConstraints' => $fieldTypeConstraints,
                'defaultValue' => $defaultValue,
            ]
        );

        $this->converter->toStorageFieldDefinition($fieldDef, $storageFieldDef);
        self::assertSame(
            0,
            $storageFieldDef->dataInt1
        );
        self::assertSame(
            $fieldDef->defaultValue->data,
            $storageFieldDef->dataText1
        );
    }

    /**
     * @group fieldType
     * @group textLine
     */
    public function testToFieldDefinition()
    {
        $defaultText = 'This is a default value';
        $fieldDef = new PersistenceFieldDefinition();
        $storageDef = new StorageFieldDefinition(
            [
                'dataInt1' => 100,
                'dataText1' => $defaultText,
            ]
        );

        $this->converter->toFieldDefinition($storageDef, $fieldDef);
        self::assertSame(
            [
                TextLineConverter::STRING_LENGTH_VALIDATOR_IDENTIFIER => ['maxStringLength' => 100],
            ],
            $fieldDef->fieldTypeConstraints->validators
        );
        self::assertSame($defaultText, $fieldDef->defaultValue->data);
        self::assertSame($defaultText, $fieldDef->defaultValue->sortKey);
    }
}

class_alias(TextLineTest::class, 'eZ\Publish\Core\Persistence\Legacy\Tests\Content\FieldValue\Converter\TextLineTest');
