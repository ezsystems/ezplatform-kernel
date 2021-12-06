<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Core\Persistence\Legacy\Content\Type;

use Ibexa\Contracts\Core\Persistence\Content\Location;
use Ibexa\Contracts\Core\Persistence\Content\Type;
use Ibexa\Contracts\Core\Persistence\Content\Type\CreateStruct;
use Ibexa\Contracts\Core\Persistence\Content\Type\FieldDefinition;
use Ibexa\Contracts\Core\Persistence\Content\Type\Group;
use Ibexa\Contracts\Core\Persistence\Content\Type\Group\CreateStruct as GroupCreateStruct;
// Needed for $sortOrder and $sortField properties
use Ibexa\Contracts\Core\Persistence\Content\Type\UpdateStruct;
use Ibexa\Core\Persistence\Legacy\Content\FieldValue\Converter;
use Ibexa\Core\Persistence\Legacy\Content\FieldValue\ConverterRegistry;
use Ibexa\Core\Persistence\Legacy\Content\Language\MaskGenerator;
use Ibexa\Core\Persistence\Legacy\Content\StorageFieldDefinition;
use Ibexa\Core\Persistence\Legacy\Content\Type\Mapper;
use Ibexa\Tests\Core\Persistence\Legacy\TestCase;

/**
 * @covers \Ibexa\Core\Persistence\Legacy\Content\Type\Mapper
 */
class MapperTest extends TestCase
{
    public function testCreateGroupFromCreateStruct()
    {
        $createStruct = $this->getGroupCreateStructFixture();

        $mapper = new Mapper($this->getConverterRegistryMock(), $this->getMaskGeneratorMock());

        $group = $mapper->createGroupFromCreateStruct($createStruct);

        $this->assertInstanceOf(
            Group::class,
            $group
        );
        $this->assertPropertiesCorrect(
            [
                'id' => null,
                'name' => [
                    'eng-GB' => 'Media',
                ],
                'description' => [],
                'identifier' => 'Media',
                'created' => 1032009743,
                'modified' => 1033922120,
                'creatorId' => 14,
                'modifierId' => 14,
            ],
            $group
        );
    }

    /**
     * Returns a GroupCreateStruct fixture.
     *
     * @return \Ibexa\Contracts\Core\Persistence\Content\Type\Group\CreateStruct
     */
    protected function getGroupCreateStructFixture()
    {
        $struct = new GroupCreateStruct();

        $struct->name = [
            'eng-GB' => 'Media',
        ];
        $struct->description = [];
        $struct->identifier = 'Media';
        $struct->created = 1032009743;
        $struct->modified = 1033922120;
        $struct->creatorId = 14;
        $struct->modifierId = 14;

        return $struct;
    }

    public function testTypeFromCreateStruct()
    {
        $struct = $this->getContentTypeCreateStructFixture();

        $mapper = new Mapper($this->getConverterRegistryMock(), $this->getMaskGeneratorMock());
        $type = $mapper->createTypeFromCreateStruct($struct);

        foreach ($struct as $propName => $propVal) {
            $this->assertEquals(
                $struct->$propName,
                $type->$propName,
                "Property \${$propName} not equal"
            );
        }
    }

    public function testTypeFromUpdateStruct()
    {
        $struct = $this->getContentTypeUpdateStructFixture();

        $mapper = new Mapper($this->getConverterRegistryMock(), $this->getMaskGeneratorMock());
        $type = $mapper->createTypeFromUpdateStruct($struct);

        $this->assertStructsEqual(
            $struct,
            $type
        );
    }

    /**
     * Returns a CreateStruct fixture.
     *
     * @return \Ibexa\Contracts\Core\Persistence\Content\Type\CreateStruct
     */
    protected function getContentTypeCreateStructFixture()
    {
        // Taken from example DB
        $struct = new CreateStruct();
        $struct->name = [
            'eng-US' => 'Folder',
        ];
        $struct->status = 0;
        $struct->description = [];
        $struct->identifier = 'folder';
        $struct->created = 1024392098;
        $struct->modified = 1082454875;
        $struct->creatorId = 14;
        $struct->modifierId = 14;
        $struct->remoteId = 'a3d405b81be900468eb153d774f4f0d2';
        $struct->urlAliasSchema = '';
        $struct->nameSchema = '<short_name|name>';
        $struct->isContainer = true;
        $struct->initialLanguageId = 2;
        $struct->sortField = Location::SORT_FIELD_MODIFIED_SUBNODE;
        $struct->sortOrder = Location::SORT_ORDER_ASC;
        $struct->defaultAlwaysAvailable = true;

        $struct->groupIds = [
            1,
        ];

        $fieldDefName = new FieldDefinition();

        $fieldDefShortDescription = new FieldDefinition();

        $struct->fieldDefinitions = [
            $fieldDefName,
            $fieldDefShortDescription,
        ];

        return $struct;
    }

    /**
     * Returns a CreateStruct fixture.
     *
     * @return \Ibexa\Contracts\Core\Persistence\Content\Type\UpdateStruct
     */
    protected function getContentTypeUpdateStructFixture(): UpdateStruct
    {
        // Taken from example DB
        $struct = new UpdateStruct();
        $struct->name = [
            'eng-US' => 'Folder',
        ];
        $struct->description = [];
        $struct->identifier = 'folder';
        $struct->modified = 1082454875;
        $struct->modifierId = 14;
        $struct->remoteId = md5(microtime() . uniqid());
        $struct->urlAliasSchema = '';
        $struct->nameSchema = '<short_name|name>';
        $struct->isContainer = true;
        $struct->initialLanguageId = 2;
        $struct->sortField = Location::SORT_FIELD_MODIFIED_SUBNODE;
        $struct->sortOrder = Location::SORT_ORDER_ASC;
        $struct->defaultAlwaysAvailable = true;

        return $struct;
    }

    public function testCreateStructFromType()
    {
        $type = $this->getContentTypeFixture();

        $mapper = new Mapper($this->getConverterRegistryMock(), $this->getMaskGeneratorMock());
        $struct = $mapper->createCreateStructFromType($type);

        // Iterate through struct, since it has fewer props
        foreach ($struct as $propName => $propVal) {
            $this->assertEquals(
                $struct->$propName,
                $type->$propName,
                "Property \${$propName} not equal"
            );
        }
    }

    /**
     * Returns a Type fixture.
     *
     * @return \Ibexa\Contracts\Core\Persistence\Content\Type
     */
    protected function getContentTypeFixture()
    {
        // Taken from example DB
        $type = new Type();
        $type->id = 23;
        $type->name = [
            'eng-US' => 'Folder',
        ];
        $type->status = 0;
        $type->description = [];
        $type->identifier = 'folder';
        $type->created = 1024392098;
        $type->modified = 1082454875;
        $type->creatorId = 14;
        $type->modifierId = 14;
        $type->remoteId = 'a3d405b81be900468eb153d774f4f0d2';
        $type->urlAliasSchema = '';
        $type->nameSchema = '<short_name|name>';
        $type->isContainer = true;
        $type->initialLanguageId = 2;
        $type->sortField = Location::SORT_FIELD_MODIFIED_SUBNODE;
        $type->sortOrder = Location::SORT_ORDER_ASC;
        $type->defaultAlwaysAvailable = true;
        $type->groupIds = [
            1,
        ];

        $fieldDefName = new FieldDefinition();
        $fieldDefName->id = 42;

        $fieldDefShortDescription = new FieldDefinition();
        $fieldDefName->id = 128;

        $type->fieldDefinitions = [
            $fieldDefName,
            $fieldDefShortDescription,
        ];

        return $type;
    }

    public function testExtractGroupsFromRows()
    {
        $rows = $this->getLoadGroupFixture();

        $mapper = new Mapper($this->getConverterRegistryMock(), $this->getMaskGeneratorMock());
        $groups = $mapper->extractGroupsFromRows($rows);

        $groupFixtureMedia = new Group();
        $groupFixtureMedia->created = 1032009743;
        $groupFixtureMedia->creatorId = 14;
        $groupFixtureMedia->id = 3;
        $groupFixtureMedia->modified = 1033922120;
        $groupFixtureMedia->modifierId = 14;
        $groupFixtureMedia->identifier = 'Media';
        $groupFixtureMedia->isSystem = false;

        $groupFixtureSystem = new Group();
        $groupFixtureSystem->created = 1634895910;
        $groupFixtureSystem->creatorId = 14;
        $groupFixtureSystem->id = 4;
        $groupFixtureSystem->modified = 1634895910;
        $groupFixtureSystem->modifierId = 14;
        $groupFixtureSystem->identifier = 'System';
        $groupFixtureSystem->isSystem = true;

        $this->assertEquals(
            [$groupFixtureMedia, $groupFixtureSystem],
            $groups
        );
    }

    public function testExtractTypesFromRowsSingle()
    {
        $rows = $this->getLoadTypeFixture();

        $mapper = $this->getNonConvertingMapper();
        $types = $mapper->extractTypesFromRows($rows);

        $this->assertCount(
            1,
            $types,
            'Incorrect number of types extracted'
        );

        $this->assertPropertiesCorrect(
            [
                'id' => 1,
                'status' => 0,
                'name' => [
                    'eng-US' => 'Folder',
                ],
                'description' => [],
                'created' => 1024392098,
                'creatorId' => 14,
                'modified' => 1082454875,
                'modifierId' => 14,
                'identifier' => 'folder',
                'remoteId' => 'a3d405b81be900468eb153d774f4f0d2',
                'urlAliasSchema' => '',
                'nameSchema' => '<short_name|name>',
                'isContainer' => true,
                'initialLanguageId' => 2,
                'groupIds' => [1],
                'sortField' => 1,
                'sortOrder' => 1,
                'defaultAlwaysAvailable' => true,
            ],
            $types[0]
        );

        $this->assertCount(
            4,
            $types[0]->fieldDefinitions,
            'Incorrect number of field definitions'
        );
        $this->assertPropertiesCorrect(
            [
                'id' => 155,
                'name' => [
                    'eng-US' => 'Short name',
                ],
                'description' => [],
                'identifier' => 'short_name',
                'fieldGroup' => '',
                'fieldType' => 'ezstring',
                'isTranslatable' => true,
                'isRequired' => false,
                'isInfoCollector' => false,
                'isSearchable' => true,
                'position' => 2,
            ],
            $types[0]->fieldDefinitions[1]
        );

        $this->assertPropertiesCorrect(
            [
                'id' => 159,
                'name' => [],
                'description' => [],
                'identifier' => 'show_children',
                'fieldGroup' => '',
                'fieldType' => 'ezboolean',
                'isTranslatable' => false,
                'isRequired' => false,
                'isInfoCollector' => false,
                'isSearchable' => false,
                'position' => 6,
            ],
            $types[0]->fieldDefinitions[3]
        );
    }

    public function testToStorageFieldDefinition()
    {
        $converterMock = $this->createMock(Converter::class);
        $converterMock->expects($this->once())
            ->method('toStorageFieldDefinition')
            ->with(
                $this->isInstanceOf(
                    FieldDefinition::class
                ),
                $this->isInstanceOf(
                    StorageFieldDefinition::class
                )
            );

        $converterRegistry = new ConverterRegistry(['some_type' => $converterMock]);

        $mapper = new Mapper($converterRegistry, $this->getMaskGeneratorMock());

        $fieldDef = new FieldDefinition();
        $fieldDef->fieldType = 'some_type';
        $fieldDef->name = [
            'eng-GB' => 'some name',
        ];

        $storageFieldDef = new StorageFieldDefinition();

        $mapper->toStorageFieldDefinition($fieldDef, $storageFieldDef);
    }

    public function testToFieldDefinition()
    {
        $converterMock = $this->createMock(Converter::class);
        $converterMock->expects($this->once())
            ->method('toFieldDefinition')
            ->with(
                $this->isInstanceOf(
                    StorageFieldDefinition::class
                ),
                $this->isInstanceOf(
                    FieldDefinition::class
                )
            );

        $converterRegistry = new ConverterRegistry(['some_type' => $converterMock]);

        $mapper = new Mapper($converterRegistry, $this->getMaskGeneratorMock());

        $storageFieldDef = new StorageFieldDefinition();

        $fieldDef = new FieldDefinition();
        $fieldDef->fieldType = 'some_type';

        $mapper->toFieldDefinition($storageFieldDef, $fieldDef);
    }

    /**
     * Returns a Mapper with conversion methods mocked.
     *
     * @return \Ibexa\Core\Persistence\Legacy\Content\Type\Mapper
     */
    protected function getNonConvertingMapper()
    {
        $mapper = $this->getMockBuilder(Mapper::class)
            ->setMethods(['toFieldDefinition'])
            ->setConstructorArgs([$this->getConverterRegistryMock(), $this->getMaskGeneratorMock()])
            ->getMock();

        // Dedicatedly tested test
        $mapper->expects($this->atLeastOnce())
            ->method('toFieldDefinition')
            ->with(
                $this->isInstanceOf(
                    StorageFieldDefinition::class
                )
            )->will(
                $this->returnCallback(
                    static function () {
                        return new FieldDefinition();
                    }
                )
            );

        return $mapper;
    }

    /**
     * Returns a converter registry mock.
     *
     * @return \Ibexa\Core\Persistence\Legacy\Content\FieldValue\ConverterRegistry
     */
    protected function getConverterRegistryMock()
    {
        return $this->createMock(ConverterRegistry::class);
    }

    /**
     * Returns fixture for {@link testExtractTypesFromRowsSingle()}.
     *
     * @return array
     */
    protected function getLoadTypeFixture()
    {
        return require __DIR__ . '/_fixtures/map_load_type.php';
    }

    /**
     * Returns fixture for {@link testExtractGroupsFromRows()}.
     *
     * @return array
     */
    protected function getLoadGroupFixture()
    {
        return require __DIR__ . '/_fixtures/map_load_group.php';
    }

    protected function getMaskGeneratorMock()
    {
        return $this->createMock(MaskGenerator::class);
    }
}

class_alias(MapperTest::class, 'eZ\Publish\Core\Persistence\Legacy\Tests\Content\Type\MapperTest');
