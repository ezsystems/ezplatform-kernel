<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Core\Persistence\Legacy\Content\FieldValue\Converter;

use Ibexa\Contracts\Core\Persistence\Content\Type\FieldDefinition as PersistenceFieldDefinition;
use Ibexa\Core\Persistence\Legacy\Content\FieldValue\Converter\ISBNConverter;
use Ibexa\Core\Persistence\Legacy\Content\StorageFieldDefinition;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ibexa\Core\Persistence\Legacy\Content\FieldValue\Converter\ISBNConverter
 */
class ISBNTest extends TestCase
{
    /** @var \Ibexa\Core\Persistence\Legacy\Content\FieldValue\Converter\ISBNConverter */
    protected $converter;

    protected function setUp(): void
    {
        $this->converter = new ISBNConverter();
    }

    /**
     * @dataProvider providerForTestToFieldDefinition
     */
    public function testToFieldDefinition($dataInt, $excpectedIsbn13Value)
    {
        $fieldDef = new PersistenceFieldDefinition();
        $storageDefinition = new StorageFieldDefinition([
            'dataInt1' => $dataInt,
        ]);

        $this->converter->toFieldDefinition($storageDefinition, $fieldDef);

        /** @var \Ibexa\Core\FieldType\FieldSettings $fieldSettings */
        $fieldSettings = $fieldDef->fieldTypeConstraints->fieldSettings;
        self::assertSame($excpectedIsbn13Value, $fieldSettings['isISBN13']);
    }

    public function providerForTestToFieldDefinition()
    {
        return [
            [1, true],
            [0, false],
            [null, false],
        ];
    }
}

class_alias(ISBNTest::class, 'eZ\Publish\Core\Persistence\Legacy\Tests\Content\FieldValue\Converter\ISBNTest');
