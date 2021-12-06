<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Core\Persistence\Legacy\Content\FieldValue\Converter;

use DOMDocument;
use Ibexa\Contracts\Core\Persistence\Content\FieldTypeConstraints;
use Ibexa\Contracts\Core\Persistence\Content\FieldValue;
use Ibexa\Contracts\Core\Persistence\Content\Type\FieldDefinition as SPIFieldDefinition;
use Ibexa\Core\FieldType\Author\Type as AuthorType;
use Ibexa\Core\FieldType\FieldSettings;
use Ibexa\Core\Persistence\Legacy\Content\FieldValue\Converter\AuthorConverter;
use Ibexa\Core\Persistence\Legacy\Content\StorageFieldDefinition;
use Ibexa\Core\Persistence\Legacy\Content\StorageFieldValue;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ibexa\Core\Persistence\Legacy\Content\FieldValue\Converter\AuthorConverter
 *
 * @group fieldType
 * @group ezauthor
 */
class AuthorTest extends TestCase
{
    /** @var \Ibexa\Core\Persistence\Legacy\Content\FieldValue\Converter\AuthorConverter */
    protected $converter;

    /** @var \Ibexa\Core\FieldType\Author\Author[] */
    private $authors;

    protected function setUp(): void
    {
        parent::setUp();
        $this->converter = new AuthorConverter();
        $this->authors = [
            ['id' => 21, 'name' => 'Boba Fett', 'email' => 'boba.fett@bountyhunters.com'],
            ['id' => 42, 'name' => 'Darth Vader', 'email' => 'darth.vader@evilempire.biz'],
            ['id' => 63, 'name' => 'Luke Skywalker', 'email' => 'luke@imtheone.net'],
        ];
    }

    protected function tearDown(): void
    {
        unset($this->authors);
        parent::tearDown();
    }

    public function testToStorageValue()
    {
        $value = new FieldValue();
        $value->data = $this->authors;
        $storageFieldValue = new StorageFieldValue();

        $this->converter->toStorageValue($value, $storageFieldValue);
        $doc = new DOMDocument('1.0', 'utf-8');
        self::assertTrue($doc->loadXML($storageFieldValue->dataText));

        $authorsXml = $doc->getElementsByTagName('author');
        self::assertSame(count($this->authors), $authorsXml->length);

        // Loop against XML nodes and compare them to the real Author objects.
        // Then remove Author from $this->authors
        // This way, we can check if all authors have been converted in XML
        foreach ($authorsXml as $authorXml) {
            foreach ($this->authors as $i => $author) {
                if ($authorXml->getAttribute('id') == $author['id']) {
                    self::assertSame($author['name'], $authorXml->getAttribute('name'));
                    self::assertSame($author['email'], $authorXml->getAttribute('email'));
                    unset($this->authors[$i]);
                    break;
                }
            }
        }

        self::assertEmpty($this->authors, 'All authors have not been converted as expected');
    }

    public function testToFieldValue()
    {
        $storageFieldValue = new StorageFieldValue();
        $storageFieldValue->dataText = <<<EOT
<?xml version="1.0" encoding="utf-8"?>
<ezauthor>
    <authors>
        <author id="1" name="Boba Fett" email="boba.fett@bountyhunters.com"/>
        <author id="2" name="Darth Vader" email="darth.vader@evilempire.biz"/>
        <author id="3" name="Luke Skywalker" email="luke@imtheone.net"/>
    </authors>
</ezauthor>
EOT;
        $doc = new DOMDocument('1.0', 'utf-8');
        self::assertTrue($doc->loadXML($storageFieldValue->dataText));
        $authorsXml = $doc->getElementsByTagName('author');
        $fieldValue = new FieldValue();

        $this->converter->toFieldValue($storageFieldValue, $fieldValue);
        self::assertIsArray($fieldValue->data);

        $authorsXml = $doc->getElementsByTagName('author');
        self::assertSame($authorsXml->length, count($fieldValue->data));

        $aAuthors = $fieldValue->data;
        foreach ($fieldValue->data as $i => $author) {
            foreach ($authorsXml as $authorXml) {
                if ($authorXml->getAttribute('id') == $author['id']) {
                    self::assertSame($authorXml->getAttribute('name'), $author['name']);
                    self::assertSame($authorXml->getAttribute('email'), $author['email']);
                    unset($aAuthors[$i]);
                    break;
                }
            }
        }
        self::assertEmpty($aAuthors, 'All authors have not been converted as expected from storage');
    }

    public function testToStorageFieldDefinitionDefaultCurrentUser()
    {
        $storageFieldDef = new StorageFieldDefinition();
        $fieldTypeConstraints = new FieldTypeConstraints();
        $fieldTypeConstraints->fieldSettings = new FieldSettings(
            [
                'defaultAuthor' => AuthorType::DEFAULT_CURRENT_USER,
            ]
        );
        $fieldDef = new SPIFieldDefinition(
            [
                'fieldTypeConstraints' => $fieldTypeConstraints,
            ]
        );

        $this->converter->toStorageFieldDefinition($fieldDef, $storageFieldDef);
        self::assertSame(
            AuthorType::DEFAULT_CURRENT_USER,
            $storageFieldDef->dataInt1
        );
    }

    public function testToStorageFieldDefinitionDefaultEmpty()
    {
        $storageFieldDef = new StorageFieldDefinition();
        $fieldTypeConstraints = new FieldTypeConstraints();
        $fieldTypeConstraints->fieldSettings = new FieldSettings(
            [
                'defaultAuthor' => AuthorType::DEFAULT_VALUE_EMPTY,
            ]
        );
        $fieldDef = new SPIFieldDefinition(
            [
                'fieldTypeConstraints' => $fieldTypeConstraints,
            ]
        );

        $this->converter->toStorageFieldDefinition($fieldDef, $storageFieldDef);
        self::assertSame(
            AuthorType::DEFAULT_VALUE_EMPTY,
            $storageFieldDef->dataInt1
        );
    }
}

class_alias(AuthorTest::class, 'eZ\Publish\Core\Persistence\Legacy\Tests\Content\FieldValue\Converter\AuthorTest');
