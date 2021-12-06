<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Core\Helper;

use Ibexa\Contracts\Core\Repository\ContentTypeService;
use Ibexa\Contracts\Core\Repository\FieldTypeService;
use Ibexa\Contracts\Core\Repository\Values\Content\Content as APIContent;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Contracts\Core\Repository\Values\Content\Field;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\Core\FieldType\TextLine\Type as TextLineType;
use Ibexa\Core\FieldType\TextLine\Value;
use Ibexa\Core\Helper\FieldHelper;
use Ibexa\Core\Helper\TranslationHelper;
use Ibexa\Core\Repository\Values\ContentType\FieldType;
use PHPUnit\Framework\TestCase;

class FieldHelperTest extends TestCase
{
    /** @var \Ibexa\Core\Helper\FieldHelper */
    private $fieldHelper;

    /** @var \Ibexa\Contracts\Core\Repository\FieldTypeService|\PHPUnit\Framework\MockObject\MockObject */
    private $fieldTypeServiceMock;

    /** @var \Ibexa\Contracts\Core\Repository\ContentTypeService|\PHPUnit\Framework\MockObject\MockObject */
    private $contentTypeServiceMock;

    /** @var \Ibexa\Core\Helper\TranslationHelper|\PHPUnit\Framework\MockObject\MockObject */
    private $translationHelper;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fieldTypeServiceMock = $this->createMock(FieldTypeService::class);
        $this->contentTypeServiceMock = $this->createMock(ContentTypeService::class);
        $this->translationHelper = $this->createMock(TranslationHelper::class);
        $this->fieldHelper = new FieldHelper($this->translationHelper, $this->contentTypeServiceMock, $this->fieldTypeServiceMock);
    }

    public function testIsFieldEmpty()
    {
        $contentTypeId = 123;
        $contentInfo = new ContentInfo(['contentTypeId' => $contentTypeId]);
        $content = $this->createMock(APIContent::class);
        $content
            ->expects($this->any())
            ->method('__get')
            ->with('contentInfo')
            ->will($this->returnValue($contentInfo));

        $fieldDefIdentifier = 'my_field_definition';
        $textLineFT = new TextLineType();
        $emptyValue = $textLineFT->getEmptyValue();
        $emptyField = new Field(['fieldDefIdentifier' => $fieldDefIdentifier, 'value' => $emptyValue]);

        $contentType = $this->createMock(ContentType::class);
        $fieldDefinition = $this->getMockBuilder(FieldDefinition::class)
            ->setConstructorArgs([['fieldTypeIdentifier' => 'ezstring']])
            ->getMockForAbstractClass();
        $contentType
            ->expects($this->once())
            ->method('getFieldDefinition')
            ->with($fieldDefIdentifier)
            ->will($this->returnValue($fieldDefinition));

        $content
            ->expects($this->any())
            ->method('getContentType')
            ->willReturn($contentType);

        $this->translationHelper
            ->expects($this->once())
            ->method('getTranslatedField')
            ->with($content, $fieldDefIdentifier)
            ->will($this->returnValue($emptyField));

        $this->fieldTypeServiceMock
            ->expects($this->any())
            ->method('getFieldType')
            ->with('ezstring')
            ->will($this->returnValue(new FieldType($textLineFT)));

        $this->assertTrue($this->fieldHelper->isFieldEmpty($content, $fieldDefIdentifier));
    }

    public function testIsFieldNotEmpty()
    {
        $contentTypeId = 123;
        $contentInfo = new ContentInfo(['contentTypeId' => $contentTypeId]);
        $content = $this->createMock(APIContent::class);
        $content
            ->expects($this->any())
            ->method('__get')
            ->with('contentInfo')
            ->will($this->returnValue($contentInfo));

        $fieldDefIdentifier = 'my_field_definition';
        $textLineFT = new TextLineType();
        $nonEmptyValue = new Value('Vive le sucre !!!');
        $emptyField = new Field(['fieldDefIdentifier' => 'ezstring', 'value' => $nonEmptyValue]);

        $contentType = $this->createMock(ContentType::class);
        $fieldDefinition = $this->getMockBuilder(FieldDefinition::class)
            ->setConstructorArgs([['fieldTypeIdentifier' => 'ezstring']])
            ->getMockForAbstractClass();
        $contentType
            ->expects($this->once())
            ->method('getFieldDefinition')
            ->with($fieldDefIdentifier)
            ->will($this->returnValue($fieldDefinition));

        $content
            ->expects($this->any())
            ->method('getContentType')
            ->willReturn($contentType);

        $this->translationHelper
            ->expects($this->once())
            ->method('getTranslatedField')
            ->with($content, $fieldDefIdentifier)
            ->will($this->returnValue($emptyField));

        $this->fieldTypeServiceMock
            ->expects($this->any())
            ->method('getFieldType')
            ->with('ezstring')
            ->will($this->returnValue(new FieldType($textLineFT)));

        $this->assertFalse($this->fieldHelper->isFieldEmpty($content, $fieldDefIdentifier));
    }
}

class_alias(FieldHelperTest::class, 'eZ\Publish\Core\Helper\Tests\FieldHelperTest');
