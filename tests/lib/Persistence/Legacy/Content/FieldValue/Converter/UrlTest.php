<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Core\Persistence\Legacy\Content\FieldValue\Converter;

use Ibexa\Contracts\Core\Persistence\Content\FieldValue;
use Ibexa\Contracts\Core\Persistence\Content\Type\FieldDefinition as PersistenceFieldDefinition;
use Ibexa\Core\Persistence\Legacy\Content\FieldValue\Converter\UrlConverter;
use Ibexa\Core\Persistence\Legacy\Content\StorageFieldDefinition;
use Ibexa\Core\Persistence\Legacy\Content\StorageFieldValue;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ibexa\Core\Persistence\Legacy\Content\FieldValue\Converter\UrlConverter
 */
class UrlTest extends TestCase
{
    /** @var \Ibexa\Core\Persistence\Legacy\Content\FieldValue\Converter\UrlConverter */
    protected $converter;

    protected function setUp(): void
    {
        parent::setUp();
        $this->converter = new UrlConverter();
    }

    /**
     * @group fieldType
     * @group url
     */
    public function testToStorageValue()
    {
        $value = new FieldValue();
        $text = 'eZ Systems';
        $value->data = ['text' => $text];
        $value->externalData = 'http://ez.no/';
        $value->sortKey = false;
        $storageFieldValue = new StorageFieldValue();

        $this->converter->toStorageValue($value, $storageFieldValue);
        self::assertSame($text, $storageFieldValue->dataText);
    }

    /**
     * @group fieldType
     * @group url
     */
    public function testToFieldValue()
    {
        $text = "A link's text";
        $urlId = 842;
        $storageFieldValue = new StorageFieldValue();
        $storageFieldValue->dataText = $text;
        $storageFieldValue->dataInt = $urlId;
        $storageFieldValue->sortKeyString = false;
        $storageFieldValue->sortKeyInt = false;
        $fieldValue = new FieldValue();

        $this->converter->toFieldValue($storageFieldValue, $fieldValue);
        self::assertIsArray($fieldValue->data);
        self::assertFalse($fieldValue->sortKey);
        self::assertSame($text, $fieldValue->data['text']);
        self::assertEquals($urlId, $fieldValue->data['urlId']);
    }

    /**
     * @group fieldType
     * @group url
     */
    public function testToStorageFieldDefinition()
    {
        $this->converter->toStorageFieldDefinition(new PersistenceFieldDefinition(), new StorageFieldDefinition());
    }

    /**
     * @group fieldType
     * @group url
     */
    public function testToFieldDefinition()
    {
        $this->converter->toFieldDefinition(new StorageFieldDefinition(), new PersistenceFieldDefinition());
    }
}

class_alias(UrlTest::class, 'eZ\Publish\Core\Persistence\Legacy\Tests\Content\FieldValue\Converter\UrlTest');
