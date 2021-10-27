<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Integration\Core\Persistence\FieldType;

use Ibexa\Core\Persistence\Legacy;
use Ibexa\Core\FieldType;
use Ibexa\Contracts\Core\Persistence\Content;
use Ibexa\Contracts\Core\Persistence\Content\Field;

/**
 * Integration test for legacy storage field types.
 *
 * This abstract base test case is supposed to be the base for field type
 * integration tests. It basically calls all involved methods in the field type
 * ``Converter`` and ``Storage`` implementations. Fo get it working implement
 * the abstract methods in a sensible way.
 *
 * The following actions are performed by this test using the custom field
 * type:
 *
 * - Create a new content type with the given field type
 * - Load create content type
 * - Create content object of new content type
 * - Load created content
 * - Copy created content
 * - Remove copied content
 *
 * @group integration
 */
class MapLocationIntegrationTest extends BaseIntegrationTest
{
    /**
     * Get name of tested field type.
     *
     * @return string
     */
    public function getTypeName()
    {
        return 'ezgmaplocation';
    }

    /**
     * Get handler with required custom field types registered.
     *
     * @return \Ibexa\Contracts\Core\Persistence\Handler
     */
    public function getCustomHandler()
    {
        $fieldType = new FieldType\MapLocation\Type();
        $fieldType->setTransformationProcessor($this->getTransformationProcessor());

        return $this->getHandler(
            'ezgmaplocation',
            $fieldType,
            new Legacy\Content\FieldValue\Converter\MapLocationConverter(),
            new FieldType\MapLocation\MapLocationStorage(
                new FieldType\MapLocation\MapLocationStorage\Gateway\DoctrineStorage(
                    $this->getDatabaseConnection()
                )
            )
        );
    }

    /**
     * Returns the FieldTypeConstraints to be used to create a field definition
     * of the FieldType under test.
     *
     * @return \Ibexa\Contracts\Core\Persistence\Content\FieldTypeConstraints
     */
    public function getTypeConstraints()
    {
        return new Content\FieldTypeConstraints();
    }

    /**
     * Get field definition data values.
     *
     * This is a PHPUnit data provider
     *
     * @return array
     */
    public function getFieldDefinitionData()
    {
        return [
            // The ezgmaplocation field type does not have any special field definition
            // properties
            ['fieldType', 'ezgmaplocation'],
            ['fieldTypeConstraints', new Content\FieldTypeConstraints()],
        ];
    }

    /**
     * Get initial field value.
     *
     * @return \Ibexa\Contracts\Core\Persistence\Content\FieldValue
     */
    public function getInitialValue()
    {
        return new Content\FieldValue(
            [
                'data' => null,
                'externalData' => [
                    'latitude' => 51.564479,
                    'longitude' => 6.692219,
                    'address' => 'Sindelfingen',
                ],
                'sortKey' => 'Sindelfingen',
            ]
        );
    }

    /**
     * Asserts that the loaded field data is correct.
     *
     * Performs assertions on the loaded field, mainly checking that the
     * $field->value->externalData is loaded correctly. If the loading of
     * external data manipulates other aspects of $field, their correctness
     * also needs to be asserted. Make sure you implement this method agnostic
     * to the used SPI\Persistence implementation!
     */
    public function assertLoadedFieldDataCorrect(Field $field)
    {
        $this->assertEquals(
            $this->getInitialValue()->externalData,
            $field->value->externalData
        );

        $this->assertNull($field->value->data);
        $this->assertNull($field->value->sortKey);
    }

    /**
     * Get update field value.
     *
     * Use to update the field
     *
     * @return \Ibexa\Contracts\Core\Persistence\Content\FieldValue
     */
    public function getUpdatedValue()
    {
        return new Content\FieldValue(
            [
                'data' => null,
                // Empty value
                'externalData' => null,
                'sortKey' => null,
            ]
        );
    }

    /**
     * Asserts that the updated field data is loaded correct.
     *
     * Performs assertions on the loaded field after it has been updated,
     * mainly checking that the $field->value->externalData is loaded
     * correctly. If the loading of external data manipulates other aspects of
     * $field, their correctness also needs to be asserted. Make sure you
     * implement this method agnostic to the used SPI\Persistence
     * implementation!
     */
    public function assertUpdatedFieldDataCorrect(Field $field)
    {
        $this->assertNull($field->value->externalData);
        $this->assertNull($field->value->data);
        $this->assertNull($field->value->sortKey);
    }
}

class_alias(MapLocationIntegrationTest::class, 'eZ\Publish\SPI\Tests\FieldType\MapLocationIntegrationTest');
