<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Core\Repository\Values\Content;

use eZ\Publish\API\Repository\Values\Content\Field;
use eZ\Publish\Core\FieldType\TextLine\Value as TextLineValue;
use eZ\Publish\Core\Repository\Values\Content\Content;
use PHPUnit\Framework\TestCase;

/**
 * @see \eZ\Publish\Core\Repository\Tests\Values\Content\ContentTest for Legacy set of unit tests.
 *
 * @covers \eZ\Publish\Core\Repository\Values\Content\Content
 */
final class ContentTest extends TestCase
{
    /** @var \eZ\Publish\API\Repository\Values\Content\Field[] */
    private $internalFields;

    /** @var \eZ\Publish\Core\Repository\Values\Content\Content */
    private $content;

    protected function setUp(): void
    {
        $this->internalFields = [
            new Field(
                [
                    'fieldDefIdentifier' => 'foo',
                    'languageCode' => 'pol-PL',
                    'value' => new TextLineValue('Foo'),
                    'fieldTypeIdentifier' => 'string',
                ]
            ),
            new Field(
                [
                    'fieldDefIdentifier' => 'foo',
                    'languageCode' => 'eng-GB',
                    'value' => new TextLineValue('English Foo'),
                    'fieldTypeIdentifier' => 'string',
                ]
            ),
            new Field(
                [
                    'fieldDefIdentifier' => 'bar',
                    'languageCode' => 'pol-PL',
                    'value' => new TextLineValue('Bar'),
                    'fieldTypeIdentifier' => 'custom_type',
                ]
            ),
        ];

        $this->content = new Content(
            [
                'internalFields' => $this->internalFields,
                'prioritizedFieldLanguageCode' => 'pol-PL',
            ]
        );
    }

    public function testGetFields(): void
    {
        self::assertSame($this->internalFields, $this->content->getFields());
    }

    public function testGetField(): void
    {
        self::assertSame($this->internalFields[0], $this->content->getField('foo'));
        self::assertSame($this->internalFields[1], $this->content->getField('foo', 'eng-GB'));
    }

    public function testGetFieldValue(): void
    {
        self::assertEquals(new TextLineValue('Bar'), $this->content->getFieldValue('bar', 'pol-PL'));
        self::assertNull($this->content->getFieldValue('bar', 'eng-GB'));
    }

    public function testGetFieldsByLanguage(): void
    {
        self::assertSame(
            [
                'foo' => $this->internalFields[0],
                'bar' => $this->internalFields[2],
            ],
            $this->content->getFieldsByLanguage('pol-PL')
        );
    }
}
