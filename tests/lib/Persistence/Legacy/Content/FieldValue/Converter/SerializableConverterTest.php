<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Core\Persistence\Legacy\Content\FieldValue\Converter;

use Ibexa\Contracts\Core\FieldType\ValueSerializerInterface;
use Ibexa\Contracts\Core\Persistence\Content\FieldTypeConstraints;
use Ibexa\Contracts\Core\Persistence\Content\FieldValue;
use Ibexa\Contracts\Core\Persistence\Content\Type\FieldDefinition;
use Ibexa\Core\FieldType\FieldSettings;
use Ibexa\Core\Persistence\Legacy\Content\FieldValue\Converter\SerializableConverter;
use Ibexa\Core\Persistence\Legacy\Content\StorageFieldDefinition;
use Ibexa\Core\Persistence\Legacy\Content\StorageFieldValue;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ibexa\Core\Persistence\Legacy\Content\FieldValue\Converter\SerializableConverter
 */
class SerializableConverterTest extends TestCase
{
    private const EXAMPLE_DATA = [
        'foo' => 'foo',
        'bar' => 'bar',
    ];

    private const EXAMPLE_JSON = '{"foo":"foo","bar":"bar"}';

    /** @var \Ibexa\Contracts\Core\FieldType\ValueSerializerInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $serializer;

    /** @var \Ibexa\Core\Persistence\Legacy\Content\FieldValue\Converter\SerializableConverter */
    private $converter;

    protected function setUp(): void
    {
        parent::setUp();

        $this->serializer = $this->createMock(ValueSerializerInterface::class);
        $this->converter = new SerializableConverter($this->serializer);
    }

    public function testToStorageValue(): void
    {
        $fieldValue = new FieldValue();
        $fieldValue->data = self::EXAMPLE_DATA;
        $fieldValue->sortKey = 'key';

        $this->serializer
            ->expects($this->once())
            ->method('encode')
            ->with($fieldValue->data)
            ->willReturn(self::EXAMPLE_JSON);

        $storageValue = new StorageFieldValue();

        $this->converter->toStorageValue($fieldValue, $storageValue);

        $this->assertEquals(self::EXAMPLE_JSON, $storageValue->dataText);
        $this->assertEquals('key', $storageValue->sortKeyString);
    }

    public function testEmptyToStorageValue(): void
    {
        $this->serializer
            ->expects($this->never())
            ->method('encode');

        $storageValue = new StorageFieldValue();

        $this->converter->toStorageValue(new FieldValue(), $storageValue);

        $this->assertNull($storageValue->dataText);
    }

    public function testToFieldValue(): void
    {
        $storageValue = new StorageFieldValue();
        $storageValue->sortKeyString = 'key';
        $storageValue->dataText = self::EXAMPLE_JSON;

        $this->serializer
            ->expects($this->once())
            ->method('decode')
            ->with(self::EXAMPLE_JSON)
            ->willReturn(self::EXAMPLE_DATA);

        $fieldValue = new FieldValue();

        $this->converter->toFieldValue($storageValue, $fieldValue);

        $this->assertEquals('key', $fieldValue->sortKey);
        $this->assertEquals(self::EXAMPLE_DATA, $fieldValue->data);
        $this->assertNull($fieldValue->externalData);
    }

    public function testEmptyToFieldValue(): void
    {
        $this->serializer
            ->expects($this->never())
            ->method('decode');

        $fieldValue = new FieldValue();

        $this->converter->toFieldValue(new StorageFieldValue(), $fieldValue);

        $this->assertNull($fieldValue->data);
    }

    public function testToStorageFieldDefinition(): void
    {
        $fieldTypeConstraints = new FieldTypeConstraints();
        $fieldTypeConstraints->fieldSettings = new FieldSettings(self::EXAMPLE_DATA);

        $fieldDefinition = new FieldDefinition([
            'fieldTypeConstraints' => $fieldTypeConstraints,
        ]);

        $this->serializer
            ->expects($this->once())
            ->method('encode')
            ->with(self::EXAMPLE_DATA)
            ->willReturn(self::EXAMPLE_JSON);

        $storageFieldDefinition = new StorageFieldDefinition();

        $this->converter->toStorageFieldDefinition($fieldDefinition, $storageFieldDefinition);

        $this->assertEquals(self::EXAMPLE_JSON, $storageFieldDefinition->dataText5);
    }

    public function testEmptyToStorageFieldDefinition(): void
    {
        $this->serializer
            ->expects($this->never())
            ->method('encode');

        $storageFieldDefinition = new StorageFieldDefinition();

        $this->converter->toStorageFieldDefinition(new FieldDefinition(), $storageFieldDefinition);

        $this->assertNull($storageFieldDefinition->dataText5);
    }

    public function testToFieldDefinition(): void
    {
        $storageFieldDefinition = new StorageFieldDefinition();
        $storageFieldDefinition->dataText5 = self::EXAMPLE_JSON;

        $this->serializer
            ->expects($this->once())
            ->method('decode')
            ->with(self::EXAMPLE_JSON)
            ->willReturn(self::EXAMPLE_DATA);

        $fieldDefinition = new FieldDefinition();

        $this->converter->toFieldDefinition($storageFieldDefinition, $fieldDefinition);

        $this->assertEquals(
            new FieldSettings(self::EXAMPLE_DATA),
            $fieldDefinition->fieldTypeConstraints->fieldSettings
        );
    }

    public function testEmptyToFieldDefinition(): void
    {
        $this->serializer
            ->expects($this->never())
            ->method('decode');

        $fieldDefinition = new FieldDefinition();

        $this->converter->toFieldDefinition(new StorageFieldDefinition(), $fieldDefinition);

        $this->assertNull($fieldDefinition->fieldTypeConstraints->fieldSettings);
    }
}

class_alias(SerializableConverterTest::class, 'eZ\Publish\Core\Persistence\Legacy\Tests\Content\FieldValue\Converter\SerializableConverterTest');
