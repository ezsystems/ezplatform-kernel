<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Integration\Core\Repository\FieldType;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Field;
use Ibexa\Core\Base\Exceptions\InvalidArgumentType;
use Ibexa\Core\FieldType\RelationList\Type as RelationListType;
use Ibexa\Core\FieldType\RelationList\Value as RelationListValue;
use Ibexa\Core\Repository\Values\Content\Relation;

/**
 * Integration test for use field type.
 *
 * @group integration
 * @group field-type
 */
class RelationListIntegrationTest extends SearchMultivaluedBaseIntegrationTest
{
    use RelationSearchBaseIntegrationTestTrait;

    /**
     * Get name of tested field type.
     *
     * @return string
     */
    public function getTypeName()
    {
        return 'ezobjectrelationlist';
    }

    /**
     * {@inheritdoc}
     */
    protected function supportsLikeWildcard($value)
    {
        parent::supportsLikeWildcard($value);

        return false;
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Content $content
     *
     * @return array|\Ibexa\Contracts\Core\Repository\Values\Content\Relation[]
     */
    public function getCreateExpectedRelations(Content $content)
    {
        $contentService = $this->getRepository()->getContentService();

        return [
            new Relation(
                [
                    'sourceFieldDefinitionIdentifier' => 'data',
                    'type' => Relation::FIELD,
                    'sourceContentInfo' => $content->contentInfo,
                    'destinationContentInfo' => $contentService->loadContentInfo(4),
                ]
            ),
            new Relation(
                [
                    'sourceFieldDefinitionIdentifier' => 'data',
                    'type' => Relation::FIELD,
                    'sourceContentInfo' => $content->contentInfo,
                    'destinationContentInfo' => $contentService->loadContentInfo(49),
                ]
            ),
        ];
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Content $content
     *
     * @return array|\Ibexa\Contracts\Core\Repository\Values\Content\Relation[]
     */
    public function getUpdateExpectedRelations(Content $content)
    {
        $contentService = $this->getRepository()->getContentService();

        return [
            new Relation(
                [
                    'id' => null,
                    'sourceFieldDefinitionIdentifier' => 'data',
                    'type' => Relation::FIELD,
                    'sourceContentInfo' => $content->contentInfo,
                    'destinationContentInfo' => $contentService->loadContentInfo(4),
                ]
            ),
            new Relation(
                [
                    'sourceFieldDefinitionIdentifier' => 'data',
                    'type' => Relation::FIELD,
                    'sourceContentInfo' => $content->contentInfo,
                    'destinationContentInfo' => $contentService->loadContentInfo(49),
                ]
            ),
            new Relation(
                [
                    'id' => null,
                    'sourceFieldDefinitionIdentifier' => 'data',
                    'type' => Relation::FIELD,
                    'sourceContentInfo' => $content->contentInfo,
                    'destinationContentInfo' => $contentService->loadContentInfo(54),
                ]
            ),
        ];
    }

    public function getSettingsSchema()
    {
        return [
            'selectionMethod' => [
                'type' => 'int',
                'default' => RelationListType::SELECTION_BROWSE,
            ],
            'selectionDefaultLocation' => [
                'type' => 'string',
                'default' => null,
            ],
            'selectionContentTypes' => [
                'type' => 'array',
                'default' => [],
            ],
        ];
    }

    public function getValidatorSchema()
    {
        return [
            'RelationListValueValidator' => [
                'selectionLimit' => [
                    'type' => 'int',
                    'default' => 0,
                ],
            ],
        ];
    }

    /**
     * Get a valid $fieldSettings value.
     *
     * @todo Implement correctly
     *
     * @return mixed
     */
    public function getValidFieldSettings()
    {
        return [
            'selectionMethod' => 1,
            'selectionDefaultLocation' => '2',
            'selectionContentTypes' => ['blog_post'],
        ];
    }

    /**
     * Get a valid $validatorConfiguration.
     *
     * @todo Implement correctly
     *
     * @return mixed
     */
    public function getValidValidatorConfiguration()
    {
        return [
            'RelationListValueValidator' => [
                'selectionLimit' => 0,
            ],
        ];
    }

    /**
     * Get $fieldSettings value not accepted by the field type.
     *
     * @todo Implement correctly
     *
     * @return mixed
     */
    public function getInvalidFieldSettings()
    {
        return ['selectionMethod' => 'a', 'selectionDefaultLocation' => true, 'unknownSetting' => false];
    }

    /**
     * Get $validatorConfiguration not accepted by the field type.
     *
     * @todo Implement correctly
     *
     * @return mixed
     */
    public function getInvalidValidatorConfiguration()
    {
        return ['noValidator' => true];
    }

    /**
     * Get initial field data for valid object creation.
     *
     * @return mixed
     */
    public function getValidCreationFieldData()
    {
        return new RelationListValue([4, 49]);
    }

    /**
     * Get name generated by the given field type (via fieldType->getName()).
     *
     * @return string
     */
    public function getFieldName()
    {
        return 'Users' . ' ' . 'Images';
    }

    /**
     * Asserts that the field data was loaded correctly.
     *
     * Asserts that the data provided by {@link getValidCreationFieldData()}
     * was stored and loaded correctly.
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Field $field
     */
    public function assertFieldDataLoadedCorrect(Field $field)
    {
        $this->assertInstanceOf(
            RelationListValue::class,
            $field->value
        );

        $expectedData = [
            'destinationContentIds' => [4, 49],
        ];
        $this->assertPropertiesCorrectUnsorted(
            $expectedData,
            $field->value
        );
    }

    public function provideInvalidCreationFieldData()
    {
        return [
            [
                new RelationListValue([null]),
                InvalidArgumentType::class,
            ],
        ];
    }

    /**
     * Get update field externals data.
     *
     * @return array
     */
    public function getValidUpdateFieldData()
    {
        return new RelationListValue([49, 54, 4]);
    }

    /**
     * Get externals updated field data values.
     *
     * This is a PHPUnit data provider
     *
     * @return array
     */
    public function assertUpdatedFieldDataLoadedCorrect(Field $field)
    {
        self::assertInstanceOf(RelationListValue::class, $field->value);

        $expectedData = [
            'destinationContentIds' => [49, 54, 4],
        ];
        $this->assertPropertiesCorrectUnsorted(
            $expectedData,
            $field->value
        );
    }

    public function provideInvalidUpdateFieldData()
    {
        return $this->provideInvalidCreationFieldData();
    }

    /**
     * Asserts the the field data was loaded correctly.
     *
     * Asserts that the data provided by {@link getValidCreationFieldData()}
     * was copied and loaded correctly.
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Field $field
     */
    public function assertCopiedFieldDataLoadedCorrectly(Field $field)
    {
        $this->assertInstanceOf(
            RelationListValue::class,
            $field->value
        );

        $expectedData = [
            'destinationContentIds' => [4, 49],
        ];
        $this->assertPropertiesCorrectUnsorted(
            $expectedData,
            $field->value
        );
    }

    /**
     * Get data to test to hash method.
     *
     * This is a PHPUnit data provider
     *
     * The returned records must have the the original value assigned to the
     * first index and the expected hash result to the second. For example:
     *
     * <code>
     * array(
     *      array(
     *          new MyValue( true ),
     *          array( 'myValue' => true ),
     *      ),
     *      // ...
     * );
     * </code>
     *
     * @return array
     */
    public function provideToHashData()
    {
        return [
            [
                new RelationListValue([4, 49]),
                [
                    'destinationContentIds' => [4, 49],
                ],
            ],
        ];
    }

    /**
     * Get expectations for the fromHash call on our field value.
     *
     * This is a PHPUnit data provider
     *
     * @return array
     */
    public function provideFromHashData()
    {
        return [
            [
                ['destinationContentIds' => [4, 49]],
                new RelationListValue([4, 49]),
            ],
        ];
    }

    public function providerForTestIsEmptyValue()
    {
        return [
            [new RelationListValue()],
            [new RelationListValue([])],
        ];
    }

    public function providerForTestIsNotEmptyValue()
    {
        return [
            [
                $this->getValidCreationFieldData(),
            ],
        ];
    }

    protected function getValidSearchValueOne()
    {
        return [11];
    }

    protected function getValidSearchValueTwo()
    {
        return [12];
    }

    protected function getSearchTargetValueOne()
    {
        return 11;
    }

    protected function getSearchTargetValueTwo()
    {
        return 12;
    }

    protected function getValidMultivaluedSearchValuesOne()
    {
        return [11, 12];
    }

    protected function getValidMultivaluedSearchValuesTwo()
    {
        return [13, 14];
    }
}

class_alias(RelationListIntegrationTest::class, 'eZ\Publish\API\Repository\Tests\FieldType\RelationListIntegrationTest');
