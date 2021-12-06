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
use Ibexa\Core\FieldType\TextBlock\Value as TextBlockValue;
use Ibexa\Core\Persistence\Legacy\Content\FieldValue\Converter\TextBlockConverter;
use Ibexa\Core\Persistence\Legacy\Content\StorageFieldDefinition;
use Ibexa\Core\Persistence\Legacy\Content\StorageFieldValue;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ibexa\Core\Persistence\Legacy\Content\FieldValue\Converter\TextBlockConverter
 */
class TextBlockTest extends TestCase
{
    /** @var \Ibexa\Core\Persistence\Legacy\Content\FieldValue\Converter\TextBlockConverter */
    protected $converter;

    protected $longText;

    protected function setUp(): void
    {
        parent::setUp();
        $this->converter = new TextBlockConverter();
        $this->longText = <<<EOT
Now that we know who you are, I know who I am.
I'm not a mistake! It all makes sense! In a comic, you know how you can tell who the arch-villain's going to be?
He's the exact opposite of the hero. And most times they're friends, like you and me! I should've known way back when...
You know why, David? Because of the kids.

They called me Mr Glass.
EOT;
    }

    /**
     * @group fieldType
     * @group textBlock
     */
    public function testToStorageValue()
    {
        $value = new FieldValue();
        $value->data = $this->longText;
        $value->sortKey = 'Now that we know who you are';
        $storageFieldValue = new StorageFieldValue();

        $this->converter->toStorageValue($value, $storageFieldValue);
        self::assertSame($value->data, $storageFieldValue->dataText);
        self::assertSame($value->sortKey, $storageFieldValue->sortKeyString);
        self::assertSame(0, $storageFieldValue->sortKeyInt);
    }

    /**
     * @group fieldType
     * @group textBlock
     */
    public function testToFieldValue()
    {
        $storageFieldValue = new StorageFieldValue();
        $storageFieldValue->dataText = $this->longText;
        $storageFieldValue->sortKeyString = 'Now that we know who you are';
        $fieldValue = new FieldValue();

        $this->converter->toFieldValue($storageFieldValue, $fieldValue);
        self::assertSame($storageFieldValue->dataText, $fieldValue->data);
        self::assertSame($storageFieldValue->sortKeyString, $fieldValue->sortKey);
    }

    /**
     * @group fieldType
     * @group textBlock
     */
    public function testToStorageFieldDefinition()
    {
        $storageFieldDef = new StorageFieldDefinition();
        $fieldTypeConstraints = new FieldTypeConstraints();
        $fieldTypeConstraints->fieldSettings = new FieldSettings(
            [
                'textRows' => 15,
            ]
        );
        $fieldDef = new PersistenceFieldDefinition(
            [
                'fieldTypeConstraints' => $fieldTypeConstraints,
                'defaultValue' => new TextBlockValue(),
            ]
        );

        $this->converter->toStorageFieldDefinition($fieldDef, $storageFieldDef);
        self::assertSame(
            15,
            $storageFieldDef->dataInt1
        );
    }

    /**
     * @group fieldType
     * @group textBlock
     */
    public function testToFieldDefinition()
    {
        $fieldDef = new PersistenceFieldDefinition();
        $storageDef = new StorageFieldDefinition(
            [
                'dataInt1' => 20,
            ]
        );

        $this->converter->toFieldDefinition($storageDef, $fieldDef);

        self::assertSame('', $fieldDef->defaultValue->sortKey);
        self::assertNull($fieldDef->fieldTypeConstraints->validators);
        self::assertInstanceOf(FieldSettings::class, $fieldDef->fieldTypeConstraints->fieldSettings);
        self::assertSame(
            ['textRows' => 20],
            $fieldDef->fieldTypeConstraints->fieldSettings->getArrayCopy()
        );
    }
}

class_alias(TextBlockTest::class, 'eZ\Publish\Core\Persistence\Legacy\Tests\Content\FieldValue\Converter\TextBlockTest');
