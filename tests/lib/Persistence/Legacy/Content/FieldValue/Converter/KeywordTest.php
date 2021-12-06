<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Core\Persistence\Legacy\Content\FieldValue\Converter;

use Ibexa\Contracts\Core\Persistence\Content\FieldValue;
use Ibexa\Contracts\Core\Persistence\Content\Type\FieldDefinition as PersistenceFieldDefinition;
use Ibexa\Core\Persistence\Legacy\Content\FieldValue\Converter\KeywordConverter;
use Ibexa\Core\Persistence\Legacy\Content\StorageFieldDefinition;
use Ibexa\Core\Persistence\Legacy\Content\StorageFieldValue;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ibexa\Core\Persistence\Legacy\Content\FieldValue\Converter\KeywordConverter
 */
class KeywordTest extends TestCase
{
    /** @var \Ibexa\Core\Persistence\Legacy\Content\FieldValue\Converter\KeywordConverter */
    protected $converter;

    protected function setUp(): void
    {
        parent::setUp();
        $this->converter = new KeywordConverter();
    }

    /**
     * @group fieldType
     * @group keyword
     */
    public function testToStorageValue()
    {
        $value = new FieldValue();
        $value->data = ['key1', 'key2'];
        $value->sortKey = false;
        $storageFieldValue = new StorageFieldValue();

        $this->converter->toStorageValue($value, $storageFieldValue);
        $this->assertNull($storageFieldValue->dataText);
        $this->assertNull($storageFieldValue->dataInt);
        $this->assertNull($storageFieldValue->dataFloat);
        $this->assertEquals(0, $storageFieldValue->sortKeyInt);
        $this->assertEquals('', $storageFieldValue->sortKeyString);
    }

    /**
     * @group fieldType
     * @group keyword
     */
    public function testToFieldValue()
    {
        $storageFieldValue = new StorageFieldValue();
        $fieldValue = new FieldValue();

        $this->converter->toFieldValue($storageFieldValue, $fieldValue);
        $this->assertSame([], $fieldValue->data);
        $this->assertEquals('', $fieldValue->sortKey);
    }

    /**
     * @group fieldType
     * @group keyword
     */
    public function testToStorageFieldDefinition()
    {
        $this->converter->toStorageFieldDefinition(new PersistenceFieldDefinition(), new StorageFieldDefinition());
    }

    /**
     * @group fieldType
     * @group keyword
     */
    public function testToFieldDefinition()
    {
        $this->converter->toFieldDefinition(new StorageFieldDefinition(), new PersistenceFieldDefinition());
    }
}

class_alias(KeywordTest::class, 'eZ\Publish\Core\Persistence\Legacy\Tests\Content\FieldValue\Converter\KeywordTest');
