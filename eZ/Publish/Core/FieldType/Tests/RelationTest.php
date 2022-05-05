<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace eZ\Publish\Core\FieldType\Tests;

use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\API\Repository\Values\Content\Relation;
use eZ\Publish\API\Repository\Values\ContentType\FieldDefinition;
use eZ\Publish\Core\Base\Exceptions\InvalidArgumentException;
use eZ\Publish\Core\FieldType\Relation\Type as RelationType;
use eZ\Publish\Core\FieldType\Relation\Value;
use eZ\Publish\Core\FieldType\ValidationError;
use eZ\Publish\SPI\FieldType\Value as SPIValue;
use eZ\Publish\SPI\Persistence\Content\Handler as SPIContentHandler;
use eZ\Publish\SPI\Persistence\Content\VersionInfo;
use Ibexa\Core\Repository\Validator\TargetContentValidatorInterface;

class RelationTest extends FieldTypeTest
{
    private const DESTINATION_CONTENT_ID = 14;

    private $contentHandler;

    /** @var \Ibexa\Core\Repository\Validator\TargetContentValidatorInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $targetContentValidator;

    protected function setUp(): void
    {
        parent::setUp();

        $versionInfo = new VersionInfo([
            'versionNo' => 24,
            'names' => [
                'en_GB' => 'name_en_GB',
                'de_DE' => 'Name_de_DE',
            ],
        ]);
        $currentVersionNo = 28;
        $destinationContentInfo = $this->createMock(ContentInfo::class);
        $destinationContentInfo
            ->method('__get')
            ->willReturnMap([
                ['currentVersionNo', $currentVersionNo],
                ['mainLanguageCode', 'en_GB'],
            ]);

        $this->contentHandler = $this->createMock(SPIContentHandler::class);
        $this->contentHandler
            ->method('loadContentInfo')
            ->with(self::DESTINATION_CONTENT_ID)
            ->willReturn($destinationContentInfo);

        $this->contentHandler
            ->method('loadVersionInfo')
            ->with(self::DESTINATION_CONTENT_ID, $currentVersionNo)
            ->willReturn($versionInfo);

        $this->targetContentValidator = $this->createMock(TargetContentValidatorInterface::class);
    }

    /**
     * Returns the field type under test.
     *
     * This method is used by all test cases to retrieve the field type under
     * test. Just create the FieldType instance using mocks from the provided
     * get*Mock() methods and/or custom get*Mock() implementations. You MUST
     * NOT take care for test case wide caching of the field type, just return
     * a new instance from this method!
     *
     * @return \eZ\Publish\Core\FieldType\Relation\Type
     */
    protected function createFieldTypeUnderTest()
    {
        $fieldType = new RelationType(
            $this->contentHandler,
            $this->targetContentValidator
        );
        $fieldType->setTransformationProcessor($this->getTransformationProcessorMock());

        return $fieldType;
    }

    /**
     * Returns the validator configuration schema expected from the field type.
     *
     * @return array
     */
    protected function getValidatorConfigurationSchemaExpectation()
    {
        return [];
    }

    /**
     * Returns the settings schema expected from the field type.
     *
     * @return array
     */
    protected function getSettingsSchemaExpectation()
    {
        return [
            'selectionMethod' => [
                'type' => 'int',
                'default' => RelationType::SELECTION_BROWSE,
            ],
            'selectionRoot' => [
                'type' => 'string',
                'default' => null,
            ],
            'selectionContentTypes' => [
                'type' => 'array',
                'default' => [],
            ],
        ];
    }

    /**
     * Returns the empty value expected from the field type.
     *
     * @return \eZ\Publish\Core\FieldType\Relation\Value
     */
    protected function getEmptyValueExpectation()
    {
        return new Value();
    }

    /**
     * Data provider for invalid input to acceptValue().
     *
     * Returns an array of data provider sets with 2 arguments: 1. The invalid
     * input to acceptValue(), 2. The expected exception type as a string. For
     * example:
     *
     * <code>
     *  return array(
     *      array(
     *          new \stdClass(),
     *          'eZ\\Publish\\Core\\Base\\Exceptions\\InvalidArgumentException',
     *      ),
     *      array(
     *          array(),
     *          'eZ\\Publish\\Core\\Base\\Exceptions\\InvalidArgumentException',
     *      ),
     *      // ...
     *  );
     * </code>
     *
     * @return array
     */
    public function provideInvalidInputForAcceptValue()
    {
        return [
            [
                true,
                InvalidArgumentException::class,
            ],
        ];
    }

    /**
     * Data provider for valid input to acceptValue().
     *
     * Returns an array of data provider sets with 2 arguments: 1. The valid
     * input to acceptValue(), 2. The expected return value from acceptValue().
     * For example:
     *
     * <code>
     *  return array(
     *      array(
     *          null,
     *          null
     *      ),
     *      array(
     *          __FILE__,
     *          new BinaryFileValue( array(
     *              'path' => __FILE__,
     *              'fileName' => basename( __FILE__ ),
     *              'fileSize' => filesize( __FILE__ ),
     *              'downloadCount' => 0,
     *              'mimeType' => 'text/plain',
     *          ) )
     *      ),
     *      // ...
     *  );
     * </code>
     *
     * @return array
     */
    public function provideValidInputForAcceptValue()
    {
        return [
            [
                new Value(),
                new Value(),
            ],
            [
                23,
                new Value(23),
            ],
            [
                new ContentInfo(['id' => 23]),
                new Value(23),
            ],
        ];
    }

    /**
     * Provide input for the toHash() method.
     *
     * Returns an array of data provider sets with 2 arguments: 1. The valid
     * input to toHash(), 2. The expected return value from toHash().
     * For example:
     *
     * <code>
     *  return array(
     *      array(
     *          null,
     *          null
     *      ),
     *      array(
     *          new BinaryFileValue( array(
     *              'path' => 'some/file/here',
     *              'fileName' => 'sindelfingen.jpg',
     *              'fileSize' => 2342,
     *              'downloadCount' => 0,
     *              'mimeType' => 'image/jpeg',
     *          ) ),
     *          array(
     *              'path' => 'some/file/here',
     *              'fileName' => 'sindelfingen.jpg',
     *              'fileSize' => 2342,
     *              'downloadCount' => 0,
     *              'mimeType' => 'image/jpeg',
     *          )
     *      ),
     *      // ...
     *  );
     * </code>
     *
     * @return array
     */
    public function provideInputForToHash()
    {
        return [
            [
                new Value(23),
                ['destinationContentId' => 23],
            ],
            [
                new Value(),
                ['destinationContentId' => null],
            ],
        ];
    }

    /**
     * Provide input to fromHash() method.
     *
     * Returns an array of data provider sets with 2 arguments: 1. The valid
     * input to fromHash(), 2. The expected return value from fromHash().
     * For example:
     *
     * <code>
     *  return array(
     *      array(
     *          null,
     *          null
     *      ),
     *      array(
     *          array(
     *              'path' => 'some/file/here',
     *              'fileName' => 'sindelfingen.jpg',
     *              'fileSize' => 2342,
     *              'downloadCount' => 0,
     *              'mimeType' => 'image/jpeg',
     *          ),
     *          new BinaryFileValue( array(
     *              'path' => 'some/file/here',
     *              'fileName' => 'sindelfingen.jpg',
     *              'fileSize' => 2342,
     *              'downloadCount' => 0,
     *              'mimeType' => 'image/jpeg',
     *          ) )
     *      ),
     *      // ...
     *  );
     * </code>
     *
     * @return array
     */
    public function provideInputForFromHash()
    {
        return [
            [
                ['destinationContentId' => 23],
                new Value(23),
            ],
            [
                ['destinationContentId' => null],
                new Value(),
            ],
        ];
    }

    /**
     * Provide data sets with field settings which are considered valid by the
     * {@link validateFieldSettings()} method.
     *
     * Returns an array of data provider sets with a single argument: A valid
     * set of field settings.
     * For example:
     *
     * <code>
     *  return array(
     *      array(
     *          array(),
     *      ),
     *      array(
     *          array( 'rows' => 2 )
     *      ),
     *      // ...
     *  );
     * </code>
     *
     * @return array
     */
    public function provideValidFieldSettings()
    {
        return [
            [
                [
                    'selectionMethod' => RelationType::SELECTION_BROWSE,
                    'selectionRoot' => 42,
                ],
            ],
            [
                [
                    'selectionMethod' => RelationType::SELECTION_DROPDOWN,
                    'selectionRoot' => 'some-key',
                ],
            ],
        ];
    }

    /**
     * Provide data sets with field settings which are considered invalid by the
     * {@link validateFieldSettings()} method. The method must return a
     * non-empty array of validation error when receiving such field settings.
     *
     * Returns an array of data provider sets with a single argument: A valid
     * set of field settings.
     * For example:
     *
     * <code>
     *  return array(
     *      array(
     *          true,
     *      ),
     *      array(
     *          array( 'nonExistentKey' => 2 )
     *      ),
     *      // ...
     *  );
     * </code>
     *
     * @return array
     */
    public function provideInValidFieldSettings()
    {
        return [
            [
                // Unknown key
                [
                    'unknownKey' => 23,
                    'selectionMethod' => RelationType::SELECTION_BROWSE,
                    'selectionRoot' => 42,
                ],
            ],
            [
                // Invalid selectionMethod
                [
                    'selectionMethod' => 2342,
                    'selectionRoot' => 42,
                ],
            ],
            [
                // Invalid selectionRoot
                [
                    'selectionMethod' => RelationType::SELECTION_DROPDOWN,
                    'selectionRoot' => [],
                ],
            ],
        ];
    }

    /**
     * @covers \eZ\Publish\Core\FieldType\Relation\Type::getRelations
     */
    public function testGetRelations()
    {
        $ft = $this->createFieldTypeUnderTest();
        $this->assertEquals(
            [
                Relation::FIELD => [70],
            ],
            $ft->getRelations($ft->acceptValue(70))
        );
    }

    public function testValidateNotExistingContentRelation(): void
    {
        $destinationContentId = 'invalid';

        $this->targetContentValidator
            ->expects(self::once())
            ->method('validate')
            ->with((int) $destinationContentId)
            ->willReturn($this->generateValidationError($destinationContentId));

        $validationErrors = $this->doValidate([], new Value($destinationContentId));

        self::assertIsArray($validationErrors);
        self::assertEquals([$this->generateValidationError($destinationContentId)], $validationErrors);
    }

    public function testValidateInvalidContentType(): void
    {
        $destinationContentId = 12;
        $allowedContentTypes = ['article', 'folder'];

        $this->targetContentValidator
            ->expects(self::once())
            ->method('validate')
            ->with($destinationContentId, $allowedContentTypes)
            ->willReturn($this->generateContentTypeValidationError('test'));

        $validationErrors = $this->doValidate(
            ['fieldSettings' => ['selectionContentTypes' => $allowedContentTypes]],
            new Value($destinationContentId)
        );

        self::assertIsArray($validationErrors);
        self::assertEquals([$this->generateContentTypeValidationError('test')], $validationErrors);
    }

    private function generateValidationError(string $contentId): ValidationError
    {
        return new ValidationError(
            'Content with identifier %contentId% is not a valid relation target',
            null,
            [
                '%contentId%' => $contentId,
            ],
            'targetContentId'
        );
    }

    private function generateContentTypeValidationError(string $contentTypeIdentifier): ValidationError
    {
        return new ValidationError(
            'Content Type %contentTypeIdentifier% is not a valid relation target',
            null,
            [
                '%contentTypeIdentifier%' => $contentTypeIdentifier,
            ],
            'targetContentId'
        );
    }

    protected function provideFieldTypeIdentifier()
    {
        return 'ezobjectrelation';
    }

    /**
     * @dataProvider provideDataForGetName
     */
    public function testGetName(
        SPIValue $value,
        string $expected,
        array $fieldSettings = [],
        string $languageCode = 'en_GB'
    ): void {
        /** @var \eZ\Publish\API\Repository\Values\ContentType\FieldDefinition|\PHPUnit\Framework\MockObject\MockObject $fieldDefinitionMock */
        $fieldDefinitionMock = $this->createMock(FieldDefinition::class);
        $fieldDefinitionMock->method('getFieldSettings')->willReturn($fieldSettings);

        $name = $this->getFieldTypeUnderTest()->getName($value, $fieldDefinitionMock, $languageCode);

        self::assertSame($expected, $name);
    }

    public function provideDataForGetName(): array
    {
        return [
            'empty_destination_content_id' => [
                $this->getEmptyValueExpectation(), '', [], 'en_GB',
            ],
            'destination_content_id' => [
                new Value(self::DESTINATION_CONTENT_ID), 'name_en_GB', [], 'en_GB',
            ],
            'destination_content_id_de_DE' => [
                new Value(self::DESTINATION_CONTENT_ID), 'Name_de_DE', [], 'de_DE',
            ],
        ];
    }

    public function provideValidDataForValidate(): array
    {
        return [
            [[], new Value(5)],
        ];
    }

    public function provideInvalidDataForValidate(): array
    {
        return [
            [[], new Value('invalid'), []],
        ];
    }
}
