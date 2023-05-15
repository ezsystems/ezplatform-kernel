<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Core\Repository\Mapper;

use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\API\Repository\Values\Content\Field;
use eZ\Publish\API\Repository\Values\Content\VersionInfo as APIVersionInfo;
use eZ\Publish\Core\FieldType\FieldTypeRegistry;
use eZ\Publish\Core\FieldType\TextLine;
use eZ\Publish\Core\Persistence\Cache\ContentLanguageHandler;
use eZ\Publish\Core\Repository\Mapper\ContentMapper;
use eZ\Publish\Core\Repository\Values\Content\Content;
use eZ\Publish\Core\Repository\Values\Content\VersionInfo;
use eZ\Publish\Core\Repository\Values\ContentType\ContentType;
use eZ\Publish\Core\Repository\Values\ContentType\FieldDefinition;
use eZ\Publish\Core\Repository\Values\ContentType\FieldDefinitionCollection;
use PHPUnit\Framework\TestCase;

final class ContentMapperTest extends TestCase
{
    /** @var \eZ\Publish\Core\Persistence\Legacy\Content\Language\Handler|\PHPUnit\Framework\MockObject\MockObject */
    private $contentLanguageHandler;

    /** @var \eZ\Publish\Core\FieldType\FieldTypeRegistry|\PHPUnit\Framework\MockObject\MockObject */
    private $fieldTypeRegistry;

    /** @var \eZ\Publish\Core\Repository\Mapper\ContentMapper */
    private $contentMapper;

    protected function setUp(): void
    {
        $this->contentLanguageHandler = $this->createMock(ContentLanguageHandler::class);
        $this->fieldTypeRegistry = $this->createMock(FieldTypeRegistry::class);

        $this->contentMapper = new ContentMapper(
            $this->contentLanguageHandler,
            $this->fieldTypeRegistry
        );
    }

    /**
     * @covers \eZ\Publish\Core\Repository\ContentService::updateContent
     *
     * @throws \eZ\Publish\Core\Base\Exceptions\ContentValidationException
     */
    public function testUpdateContentGetsProperFieldsToUpdate(): void
    {
        $updatedField = new Field(
            [
                'id' => 1234,
                'value' => new TextLine\Value('updated one'),
                'languageCode' => 'fre-FR',
                'fieldDefIdentifier' => 'name',
                'fieldTypeIdentifier' => 'ezstring',
            ]
        );
        $updatedField2 = new Field(
            [
                'id' => 1235,
                'value' => new TextLine\Value('two'),
                'languageCode' => 'fre-FR',
                'fieldDefIdentifier' => 'name',
                'fieldTypeIdentifier' => 'ezstring',
            ]
        );
        $updatedFields = [$updatedField, $updatedField2];

        $versionInfo = new VersionInfo(
            [
                'contentInfo' => new ContentInfo(['id' => 422, 'mainLanguageCode' => 'eng-GB']),
                'versionNo' => 7,
                'status' => APIVersionInfo::STATUS_DRAFT,
            ]
        );

        $content = new Content(
            [
                'versionInfo' => $versionInfo,
                'internalFields' => [
                    new Field(
                        [
                            'value' => new TextLine\Value('one'),
                            'languageCode' => 'eng-GB',
                            'fieldDefIdentifier' => 'name',
                            'fieldTypeIdentifier' => 'ezstring',
                        ]
                    ),
                    $updatedField2,
                ],
                'contentType' => new ContentType([
                    'fieldDefinitions' => new FieldDefinitionCollection([
                        new FieldDefinition([
                            'identifier' => 'name',
                            'fieldTypeIdentifier' => 'ezstring',
                        ]),
                    ]),
                ]),
            ]
        );

        $this->fieldTypeRegistry
            ->expects(self::any())
            ->method('getFieldType')
            ->willReturn(new TextLine\Type());

        $fieldForUpdate = $this->contentMapper->getFieldsForUpdate($updatedFields, $content);

        self::assertSame([$updatedField], $fieldForUpdate);
    }
}
