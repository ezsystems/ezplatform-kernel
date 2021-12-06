<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Core\Persistence\Legacy\Content\FieldValue\Converter;

use DateTime;
use Ibexa\Contracts\Core\Persistence\Content\FieldTypeConstraints;
use Ibexa\Contracts\Core\Persistence\Content\FieldValue;
use Ibexa\Contracts\Core\Persistence\Content\Type\FieldDefinition as PersistenceFieldDefinition;
use Ibexa\Core\FieldType\Date\Type as DateType;
use Ibexa\Core\FieldType\FieldSettings;
use Ibexa\Core\Persistence\Legacy\Content\FieldValue\Converter\DateConverter;
use Ibexa\Core\Persistence\Legacy\Content\StorageFieldDefinition;
use Ibexa\Core\Persistence\Legacy\Content\StorageFieldValue;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ibexa\Core\Persistence\Legacy\Content\FieldValue\Converter\DateConverter
 *
 * @group fieldType
 * @group date
 */
class DateTest extends TestCase
{
    /** @var \Ibexa\Core\Persistence\Legacy\Content\FieldValue\Converter\DateConverter */
    protected $converter;

    /** @var \DateTime */
    protected $date;

    protected function setUp(): void
    {
        parent::setUp();
        $this->converter = new DateConverter();
        $this->date = new DateTime('@1362614400');
    }

    public function testToStorageValue()
    {
        $value = new FieldValue();
        $value->data = [
            'timestamp' => $this->date->getTimestamp(),
            'rfc850' => $this->date->format(\DateTime::RFC850),
        ];
        $value->sortKey = $this->date->getTimestamp();
        $storageFieldValue = new StorageFieldValue();

        $this->converter->toStorageValue($value, $storageFieldValue);
        self::assertSame($value->data['timestamp'], $storageFieldValue->dataInt);
        self::assertSame($value->sortKey, $storageFieldValue->sortKeyInt);
        self::assertSame('', $storageFieldValue->sortKeyString);
    }

    public function testToFieldValue()
    {
        $storageFieldValue = new StorageFieldValue();
        $storageFieldValue->dataInt = $this->date->getTimestamp();
        $storageFieldValue->sortKeyString = '';
        $storageFieldValue->sortKeyInt = $this->date->getTimestamp();
        $fieldValue = new FieldValue();

        $this->converter->toFieldValue($storageFieldValue, $fieldValue);
        self::assertSame(
            [
                'timestamp' => $this->date->getTimestamp(),
                'rfc850' => null,
            ],
            $fieldValue->data
        );
        self::assertSame($storageFieldValue->dataInt, $fieldValue->data['timestamp']);
        self::assertSame($storageFieldValue->sortKeyInt, $fieldValue->sortKey);
    }

    public function testToStorageFieldDefinitionDefaultEmpty()
    {
        $storageFieldDef = new StorageFieldDefinition();
        $fieldTypeConstraints = new FieldTypeConstraints();
        $fieldTypeConstraints->fieldSettings = new FieldSettings(
            [
                'defaultType' => DateType::DEFAULT_EMPTY,
            ]
        );
        $fieldDef = new PersistenceFieldDefinition(
            [
                'fieldTypeConstraints' => $fieldTypeConstraints,
            ]
        );

        $this->converter->toStorageFieldDefinition($fieldDef, $storageFieldDef);
        self::assertSame(
            DateType::DEFAULT_EMPTY,
            $storageFieldDef->dataInt1
        );
    }

    public function testToStorageFieldDefinitionDefaultCurrentDate()
    {
        $storageFieldDef = new StorageFieldDefinition();
        $fieldTypeConstraints = new FieldTypeConstraints();
        $fieldTypeConstraints->fieldSettings = new FieldSettings(
            [
                'defaultType' => DateType::DEFAULT_CURRENT_DATE,
            ]
        );
        $fieldDef = new PersistenceFieldDefinition(
            [
                'fieldTypeConstraints' => $fieldTypeConstraints,
            ]
        );

        $this->converter->toStorageFieldDefinition($fieldDef, $storageFieldDef);
        self::assertSame(
            DateType::DEFAULT_CURRENT_DATE,
            $storageFieldDef->dataInt1
        );
    }

    public function testToFieldDefinitionDefaultEmpty()
    {
        $fieldDef = new PersistenceFieldDefinition();
        $storageDef = new StorageFieldDefinition(
            [
                'dataInt1' => DateType::DEFAULT_EMPTY,
            ]
        );

        $this->converter->toFieldDefinition($storageDef, $fieldDef);
        self::assertNull($fieldDef->defaultValue->data);
    }

    public function testToFieldDefinitionDefaultCurrentDate()
    {
        $timestamp = time();
        $fieldDef = new PersistenceFieldDefinition();
        $storageDef = new StorageFieldDefinition(
            [
                'dataInt1' => DateType::DEFAULT_CURRENT_DATE,
            ]
        );

        $this->converter->toFieldDefinition($storageDef, $fieldDef);
        self::assertIsArray($fieldDef->defaultValue->data);
        self::assertCount(3, $fieldDef->defaultValue->data);
        self::assertNull($fieldDef->defaultValue->data['rfc850']);
        self::assertSame($timestamp, $fieldDef->defaultValue->data['timestamp']);
        self::assertSame('now', $fieldDef->defaultValue->data['timestring']);
    }
}

class_alias(DateTest::class, 'eZ\Publish\Core\Persistence\Legacy\Tests\Content\FieldValue\Converter\DateTest');
