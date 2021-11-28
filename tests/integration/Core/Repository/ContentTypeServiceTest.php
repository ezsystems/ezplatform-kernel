<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Integration\Core\Repository;

use Exception;
use Ibexa\Contracts\Core\FieldType\ValidationError;
use Ibexa\Contracts\Core\Repository\Exceptions\BadStateException;
use Ibexa\Contracts\Core\Repository\Exceptions\ContentTypeFieldDefinitionValidationException;
use Ibexa\Contracts\Core\Repository\Exceptions\ContentTypeValidationException;
use Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeCreateStruct;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeDraft;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeGroup;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeGroupCreateStruct;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeGroupUpdateStruct;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeUpdateStruct;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinitionCollection as APIFieldDefinitionCollection;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinitionCreateStruct;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinitionUpdateStruct;
use Ibexa\Contracts\Core\Repository\Values\Translation\Message;
use Ibexa\Core\FieldType\TextLine\Value as TextLineValue;

/**
 * Test case for operations in the ContentTypeService using in memory storage.
 *
 * @covers \Ibexa\Contracts\Core\Repository\ContentTypeService
 * @group integration
 * @group content-type
 */
class ContentTypeServiceTest extends BaseContentTypeServiceTest
{
    /**
     * Test for the newContentTypeGroupCreateStruct() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentTypeService::newContentTypeGroupCreateStruct()
     * @group user
     */
    public function testNewContentTypeGroupCreateStruct()
    {
        $repository = $this->getRepository();

        /* BEGIN: Use Case */
        $contentTypeService = $repository->getContentTypeService();

        $groupCreate = $contentTypeService->newContentTypeGroupCreateStruct(
            'new-group'
        );
        /* END: Use Case */

        $this->assertInstanceOf(
            ContentTypeGroupCreateStruct::class,
            $groupCreate
        );

        return $groupCreate;
    }

    /**
     * Test for the newContentTypeGroupCreateStruct() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentTypeService::newContentTypeGroupCreateStruct()
     * @depends testNewContentTypeGroupCreateStruct
     */
    public function testNewContentTypeGroupCreateStructValues($createStruct)
    {
        $this->assertPropertiesCorrect(
            [
                'identifier' => 'new-group',
                'creatorId' => null,
                'creationDate' => null,
                /* @todo uncomment when support for multilingual names and descriptions is added
                'mainLanguageCode' => null,
                */
            ],
            $createStruct
        );
    }

    /**
     * Test for the createContentTypeGroup() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentTypeService::createContentTypeGroup()
     * @depends testNewContentTypeGroupCreateStruct
     * @group user
     */
    public function testCreateContentTypeGroup()
    {
        $repository = $this->getRepository();

        /* BEGIN: Use Case */
        $contentTypeService = $repository->getContentTypeService();
        $permissionResolver = $repository->getPermissionResolver();

        $groupCreate = $contentTypeService->newContentTypeGroupCreateStruct(
            'new-group'
        );
        $groupCreate->creatorId = $this->generateId('user', $permissionResolver->getCurrentUserReference()->getUserId());
        $groupCreate->creationDate = $this->createDateTime();
        /* @todo uncomment when support for multilingual names and descriptions is added
        $groupCreate->mainLanguageCode = 'ger-DE';
        $groupCreate->names = array( 'eng-GB' => 'A name.' );
        $groupCreate->descriptions = array( 'eng-GB' => 'A description.' );
        */

        $groupCreate->isSystem = true;
        $group = $contentTypeService->createContentTypeGroup($groupCreate);
        /* END: Use Case */

        $this->assertInstanceOf(
            ContentTypeGroup::class,
            $group
        );

        $reloadedGroup = $contentTypeService->loadContentTypeGroup($group->id);

        return [
            'createStruct' => $groupCreate,
            'group' => $reloadedGroup,
        ];
    }

    /**
     * Test for the createContentTypeGroup() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentTypeService::createContentTypeGroup()
     * @depends testCreateContentTypeGroup
     */
    public function testCreateContentTypeGroupStructValues(array $data)
    {
        $createStruct = $data['createStruct'];
        $group = $data['group'];

        $this->assertEquals(
            [
                'identifier' => $group->identifier,
                'creatorId' => $group->creatorId,
                'creationDate' => $group->creationDate->getTimestamp(),
                'isSystem' => $group->isSystem,
            ],
            [
                'identifier' => $createStruct->identifier,
                'creatorId' => $createStruct->creatorId,
                'creationDate' => $createStruct->creationDate->getTimestamp(),
                'isSystem' => $createStruct->isSystem,
            ]
        );
        $this->assertNotNull(
            $group->id
        );

        return $data;
    }

    /**
     * Test for the createContentTypeGroup() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentTypeService::createContentTypeGroup()
     * @depends testCreateContentTypeGroupStructValues
     */
    public function testCreateContentTypeGroupStructLanguageDependentValues(array $data)
    {
        $createStruct = $data['createStruct'];
        $group = $data['group'];

        $this->assertStructPropertiesCorrect(
            $createStruct,
            $group,
            /* @todo uncomment when support for multilingual names and descriptions is added
            array( 'names', 'descriptions', 'mainLanguageCode' )
            */
        );
    }

    /**
     * Test for the createContentTypeGroup() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentTypeService::createContentTypeGroup
     * @depends testCreateContentTypeGroup
     */
    public function testCreateContentTypeGroupThrowsInvalidArgumentException()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Argument \'$contentTypeGroupCreateStruct\' is invalid: A group with the identifier \'Content\' already exists');

        $repository = $this->getRepository();

        /* BEGIN: Use Case */
        $contentTypeService = $repository->getContentTypeService();

        $groupCreate = $contentTypeService->newContentTypeGroupCreateStruct(
            'Content'
        );

        // Throws an Exception, since group "Content" already exists
        $contentTypeService->createContentTypeGroup($groupCreate);
        /* END: Use Case */
    }

    /**
     * Test for the loadContentTypeGroup() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentTypeService::loadContentTypeGroup()
     * @depends testCreateContentTypeGroup
     * @group user
     */
    public function testLoadContentTypeGroup()
    {
        $repository = $this->getRepository();

        $contentTypeGroupId = $this->generateId('typegroup', 2);
        /* BEGIN: Use Case */
        $contentTypeService = $repository->getContentTypeService();

        // Loads the "Users" group
        // $contentTypeGroupId is the ID of an existing content type group
        $loadedGroup = $contentTypeService->loadContentTypeGroup($contentTypeGroupId);
        /* END: Use Case */

        $this->assertInstanceOf(
            ContentTypeGroup::class,
            $loadedGroup
        );

        return $loadedGroup;
    }

    public function testLoadSystemContentTypeGroup(): void
    {
        $contentTypeService = $this->getRepository()->getContentTypeService();

        // Loads the "System" group
        $systemGroup = $contentTypeService->loadContentTypeGroup($this->generateId('typegroup', 5));

        self::assertSame(
            'System',
            $systemGroup->identifier
        );
        self::assertTrue(
            $systemGroup->isSystem
        );
    }

    /**
     * Test for the loadContentTypeGroup() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentTypeService::loadContentTypeGroup()
     * @depends testLoadContentTypeGroup
     */
    public function testLoadContentTypeGroupStructValues(ContentTypeGroup $group)
    {
        $this->assertPropertiesCorrect(
            [
                'id' => $this->generateId('typegroup', 2),
                'identifier' => 'Users',
                'creationDate' => $this->createDateTime(1031216941),
                'modificationDate' => $this->createDateTime(1033922113),
                'creatorId' => $this->generateId('user', 14),
                'modifierId' => $this->generateId('user', 14),
            ],
            $group
        );
    }

    /**
     * Test for the loadContentTypeGroup() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentTypeService::loadContentTypeGroup()
     */
    public function testLoadContentTypeGroupThrowsNotFoundException()
    {
        $this->expectException(NotFoundException::class);

        $repository = $this->getRepository();

        $contentTypeService = $repository->getContentTypeService();
        $loadedGroup = $contentTypeService->loadContentTypeGroup($this->generateId('typegroup', 2342));
    }

    /**
     * Test for the loadContentTypeGroupByIdentifier() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentTypeService::loadContentTypeGroupByIdentifier()
     * @group user
     * @group field-type
     */
    public function testLoadContentTypeGroupByIdentifier()
    {
        $repository = $this->getRepository();

        /* BEGIN: Use Case */
        $contentTypeService = $repository->getContentTypeService();

        $loadedGroup = $contentTypeService->loadContentTypeGroupByIdentifier(
            'Media'
        );
        /* END: Use Case */

        $this->assertInstanceOf(
            ContentTypeGroup::class,
            $loadedGroup
        );

        return $loadedGroup;
    }

    /**
     * Test for the loadContentTypeGroupByIdentifier() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentTypeService::loadContentTypeGroupByIdentifier()
     * @depends testLoadContentTypeGroupByIdentifier
     */
    public function testLoadContentTypeGroupByIdentifierStructValues(ContentTypeGroup $group)
    {
        $repository = $this->getRepository();
        $contentTypeService = $repository->getContentTypeService();

        $this->assertEquals(
            $contentTypeService->loadContentTypeGroup($this->generateId('typegroup', 3)),
            $group
        );
    }

    /**
     * Test for the loadContentTypeGroupByIdentifier() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentTypeService::loadContentTypeGroupByIdentifier()
     * @depends testLoadContentTypeGroupByIdentifier
     */
    public function testLoadContentTypeGroupByIdentifierThrowsNotFoundException()
    {
        $this->expectException(NotFoundException::class);

        $repository = $this->getRepository();

        /* BEGIN: Use Case */
        $contentTypeService = $repository->getContentTypeService();

        // Throws exception
        $loadedGroup = $contentTypeService->loadContentTypeGroupByIdentifier(
            'not-exists'
        );
        /* END: Use Case */
    }

    /**
     * Test for the loadContentTypeGroups() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentTypeService::loadContentTypeGroups()
     * @depends testCreateContentTypeGroup
     */
    public function testLoadContentTypeGroups()
    {
        $repository = $this->getRepository();

        /* BEGIN: Use Case */
        $contentTypeService = $repository->getContentTypeService();

        // Loads an array with all content type groups
        $loadedGroups = $contentTypeService->loadContentTypeGroups();
        /* END: Use Case */

        self::assertIsArray($loadedGroups);

        foreach ($loadedGroups as $loadedGroup) {
            $this->assertStructPropertiesCorrect(
                $contentTypeService->loadContentTypeGroup($loadedGroup->id),
                $loadedGroup,
                [
                    'id',
                    'identifier',
                    'creationDate',
                    'modificationDate',
                    'creatorId',
                    'modifierId',
                    'isSystem',
                ]
            );
        }

        return $loadedGroups;
    }

    /**
     * Test for the loadContentTypeGroups() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentTypeService::loadContentTypeGroups()
     * @depends testLoadContentTypeGroups
     */
    public function testLoadContentTypeGroupsIdentifiers($groups)
    {
        $this->assertCount(4, $groups);

        $expectedIdentifiers = [
            'Content' => true,
            'Users' => true,
            'Media' => true,
            'Setup' => true,
        ];

        $actualIdentifiers = [];
        foreach ($groups as $group) {
            $actualIdentifiers[$group->identifier] = true;
        }

        ksort($expectedIdentifiers);
        ksort($actualIdentifiers);

        $this->assertEquals(
            $expectedIdentifiers,
            $actualIdentifiers,
            'Identifier missmatch in loaded groups.'
        );
    }

    /**
     * Test for the newContentTypeGroupUpdateStruct() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentTypeService::newContentTypeGroupUpdateStruct()
     */
    public function testNewContentTypeGroupUpdateStruct()
    {
        $repository = $this->getRepository();

        /* BEGIN: Use Case */
        $contentTypeService = $repository->getContentTypeService();

        $groupUpdate = $contentTypeService->newContentTypeGroupUpdateStruct();
        /* END: Use Case */

        self::assertInstanceOf(
            ContentTypeGroupUpdateStruct::class,
            $groupUpdate
        );
    }

    /**
     * Test for the updateContentTypeGroup() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentTypeService::updateContentTypeGroup()
     * @depends testCreateContentTypeGroup
     */
    public function testUpdateContentTypeGroup()
    {
        $repository = $this->getRepository();

        $modifierId = $this->generateId('user', 42);
        /* BEGIN: Use Case */
        $contentTypeService = $repository->getContentTypeService();

        $group = $contentTypeService->loadContentTypeGroupByIdentifier('Setup');

        $groupUpdate = $contentTypeService->newContentTypeGroupUpdateStruct();

        $groupUpdate->identifier = 'Teardown';
        $groupUpdate->modifierId = $modifierId;
        $groupUpdate->modificationDate = $this->createDateTime();
        $groupUpdate->isSystem = true;
        /* @todo uncomment when support for multilingual names and descriptions is added
        $groupUpdate->mainLanguageCode = 'eng-GB';

        $groupUpdate->names = array(
            'eng-GB' => 'A name',
            'eng-US' => 'A name',
        );
        $groupUpdate->descriptions = array(
            'eng-GB' => 'A description',
            'eng-US' => 'A description',
        );
        */

        $contentTypeService->updateContentTypeGroup($group, $groupUpdate);
        /* END: Use Case */

        $updatedGroup = $contentTypeService->loadContentTypeGroup($group->id);

        self::assertInstanceOf(
            ContentTypeGroupUpdateStruct::class,
            $groupUpdate
        );

        return [
            'originalGroup' => $group,
            'updateStruct' => $groupUpdate,
            'updatedGroup' => $updatedGroup,
        ];
    }

    /**
     * Test for the updateContentTypeGroup() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentTypeService::updateContentTypeGroup()
     * @depends testUpdateContentTypeGroup
     */
    public function testUpdateContentTypeGroupStructValues(array $data)
    {
        $expectedValues = [
            'identifier' => $data['updateStruct']->identifier,
            'creationDate' => $data['originalGroup']->creationDate,
            'modificationDate' => $data['updateStruct']->modificationDate,
            'creatorId' => $data['originalGroup']->creatorId,
            'modifierId' => $data['updateStruct']->modifierId,
            'isSystem' => $data['updateStruct']->isSystem,
        ];

        $this->assertPropertiesCorrect($expectedValues, $data['updatedGroup']);

        return $data;
    }

    /**
     * Test for the updateContentTypeGroup() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentTypeService::updateContentTypeGroup()
     * @depends testUpdateContentTypeGroupStructValues
     */
    public function testUpdateContentTypeGroupStructLanguageDependentValues(array $data)
    {
        $expectedValues = [
            'identifier' => $data['updateStruct']->identifier,
            'creationDate' => $data['originalGroup']->creationDate,
            'modificationDate' => $data['updateStruct']->modificationDate,
            'creatorId' => $data['originalGroup']->creatorId,
            'modifierId' => $data['updateStruct']->modifierId,
            /* @todo uncomment when support for multilingual names and descriptions is added
            'mainLanguageCode' => $data['updateStruct']->mainLanguageCode,
            'names' => $data['updateStruct']->names,
            'descriptions' => $data['updateStruct']->descriptions,
            */
        ];

        $this->assertPropertiesCorrect($expectedValues, $data['updatedGroup']);
    }

    /**
     * Test for the updateContentTypeGroup() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentTypeService::updateContentTypeGroup
     * @depends testUpdateContentTypeGroup
     */
    public function testUpdateContentTypeGroupThrowsInvalidArgumentException()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Argument \'$contentTypeGroupUpdateStruct->identifier\' is invalid: given identifier already exists');

        $repository = $this->getRepository();

        /* BEGIN: Use Case */
        $contentTypeService = $repository->getContentTypeService();

        $group = $contentTypeService->loadContentTypeGroupByIdentifier(
            'Media'
        );

        $groupUpdate = $contentTypeService->newContentTypeGroupUpdateStruct();
        $groupUpdate->identifier = 'Users';

        // Exception, because group with identifier "Users" exists
        $contentTypeService->updateContentTypeGroup($group, $groupUpdate);
        /* END: Use Case */
    }

    /**
     * Test for the deleteContentTypeGroup() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentTypeService::deleteContentTypeGroup
     * @depends testLoadContentTypeGroup
     */
    public function testDeleteContentTypeGroup()
    {
        $this->expectException(NotFoundException::class);

        $repository = $this->getRepository();

        /* BEGIN: Use Case */
        $contentTypeService = $repository->getContentTypeService();

        $groupCreate = $contentTypeService->newContentTypeGroupCreateStruct(
            'new-group'
        );
        $contentTypeService->createContentTypeGroup($groupCreate);

        $group = $contentTypeService->loadContentTypeGroupByIdentifier('new-group');

        $contentTypeService->deleteContentTypeGroup($group);
        /* END: Use Case */

        // loadContentTypeGroup should throw NotFoundException
        $contentTypeService->loadContentTypeGroup($group->id);

        $this->fail('Content type group not deleted.');
    }

    /**
     * Test for the newContentTypeCreateStruct() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentTypeService::newContentTypeCreateStruct()
     * @group user
     * @group field-type
     */
    public function testNewContentTypeCreateStruct()
    {
        $repository = $this->getRepository();

        /* BEGIN: Use Case */
        $contentTypeService = $repository->getContentTypeService();

        $typeCreate = $contentTypeService->newContentTypeCreateStruct(
            'new-type'
        );
        /* END: Use Case */

        self::assertInstanceOf(
            ContentTypeCreateStruct::class,
            $typeCreate
        );

        return $typeCreate;
    }

    /**
     * Test for the newContentTypeCreateStruct() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentTypeService::newContentTypeCreateStruct()
     * @depends testNewContentTypeCreateStruct
     */
    public function testNewContentTypeCreateStructValues($createStruct)
    {
        $this->assertPropertiesCorrect(
            [
                'identifier' => 'new-type',
                'mainLanguageCode' => null,
                'remoteId' => null,
                'urlAliasSchema' => null,
                'nameSchema' => null,
                'isContainer' => false,
                'defaultSortField' => Location::SORT_FIELD_PUBLISHED,
                'defaultSortOrder' => Location::SORT_ORDER_DESC,
                'defaultAlwaysAvailable' => true,
                'names' => null,
                'descriptions' => null,
                'creatorId' => null,
                'creationDate' => null,
            ],
            $createStruct
        );
    }

    /**
     * Test for the newFieldDefinitionCreateStruct() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentTypeService::newFieldDefinitionCreateStruct()
     * @group user
     * @group field-type
     */
    public function testNewFieldDefinitionCreateStruct()
    {
        $repository = $this->getRepository();

        /* BEGIN: Use Case */
        $contentTypeService = $repository->getContentTypeService();

        $fieldDefinitionCreate = $contentTypeService->newFieldDefinitionCreateStruct('title', 'ezstring');
        /* END: Use Case */

        $this->assertInstanceOf(
            FieldDefinitionCreateStruct::class,
            $fieldDefinitionCreate
        );

        return $fieldDefinitionCreate;
    }

    /**
     * Test for the newFieldDefinitionCreateStruct() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentTypeService::newFieldDefinitionCreateStruct()
     * @depends testNewFieldDefinitionCreateStruct
     */
    public function testNewFieldDefinitionCreateStructValues($createStruct)
    {
        $this->assertPropertiesCorrect(
            [
                'fieldTypeIdentifier' => 'ezstring',
                'identifier' => 'title',
                'names' => null,
                'descriptions' => null,
                'fieldGroup' => null,
                'position' => null,
                'isTranslatable' => null,
                'isRequired' => null,
                'isInfoCollector' => null,
                'validatorConfiguration' => null,
                'fieldSettings' => null,
                'defaultValue' => null,
                'isSearchable' => null,
            ],
            $createStruct
        );
    }

    /**
     * Test for the deleteContentTypeGroup() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentTypeService::deleteContentTypeGroup()
     * @depends testDeleteContentTypeGroup
     */
    public function testDeleteContentTypeGroupThrowsInvalidArgumentException()
    {
        $this->expectException(InvalidArgumentException::class);

        $repository = $this->getRepository();

        /* BEGIN: Use Case */
        $contentTypeService = $repository->getContentTypeService();

        $contentGroup = $contentTypeService->loadContentTypeGroupByIdentifier('Content');

        // Throws exception, since group contains types
        $contentTypeService->deleteContentTypeGroup($contentGroup);
        /* END: Use Case */
    }

    /**
     * Test for the createContentType() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentTypeService::createContentType()
     * @depends testNewContentTypeCreateStruct
     * @depends testNewFieldDefinitionCreateStruct
     * @depends testLoadContentTypeGroupByIdentifier
     * @group user
     * @group field-type
     */
    public function testCreateContentType()
    {
        $repository = $this->getRepository();

        /* BEGIN: Use Case */
        $contentTypeService = $repository->getContentTypeService();
        $permissionResolver = $repository->getPermissionResolver();

        $typeCreate = $contentTypeService->newContentTypeCreateStruct('blog-post');
        $typeCreate->mainLanguageCode = 'eng-GB';
        $typeCreate->remoteId = '384b94a1bd6bc06826410e284dd9684887bf56fc';
        $typeCreate->urlAliasSchema = 'url|scheme';
        $typeCreate->nameSchema = 'name|scheme';
        $typeCreate->names = [
            'eng-GB' => 'Blog post',
            'ger-DE' => 'Blog-Eintrag',
        ];
        $typeCreate->descriptions = [
            'eng-GB' => 'A blog post',
            'ger-DE' => 'Ein Blog-Eintrag',
        ];
        $typeCreate->creatorId = $this->generateId('user', $permissionResolver->getCurrentUserReference()->getUserId());
        $typeCreate->creationDate = $this->createDateTime();

        $titleFieldCreate = $contentTypeService->newFieldDefinitionCreateStruct('title', 'ezstring');
        $titleFieldCreate->names = [
            'eng-GB' => 'Title',
            'ger-DE' => 'Titel',
        ];
        $titleFieldCreate->descriptions = [
            'eng-GB' => 'Title of the blog post',
            'ger-DE' => 'Titel des Blog-Eintrages',
        ];
        $titleFieldCreate->fieldGroup = 'blog-content';
        $titleFieldCreate->position = 1;
        $titleFieldCreate->isTranslatable = true;
        $titleFieldCreate->isRequired = true;
        $titleFieldCreate->isInfoCollector = false;
        $titleFieldCreate->validatorConfiguration = [
            'StringLengthValidator' => [
                'minStringLength' => 0,
                'maxStringLength' => 0,
            ],
        ];
        $titleFieldCreate->fieldSettings = [];
        $titleFieldCreate->isSearchable = true;
        $titleFieldCreate->defaultValue = 'default title';

        $typeCreate->addFieldDefinition($titleFieldCreate);

        $bodyFieldCreate = $contentTypeService->newFieldDefinitionCreateStruct('body', 'ezstring');
        $bodyFieldCreate->names = [
            'eng-GB' => 'Body',
            'ger-DE' => 'Textkörper',
        ];
        $bodyFieldCreate->descriptions = [
            'eng-GB' => 'Body of the blog post',
            'ger-DE' => 'Textkörper des Blog-Eintrages',
        ];
        $bodyFieldCreate->fieldGroup = 'blog-content';
        $bodyFieldCreate->position = 2;
        $bodyFieldCreate->isTranslatable = true;
        $bodyFieldCreate->isRequired = true;
        $bodyFieldCreate->isInfoCollector = false;
        $bodyFieldCreate->validatorConfiguration = [
            'StringLengthValidator' => [
                'minStringLength' => 0,
                'maxStringLength' => 0,
            ],
        ];
        $bodyFieldCreate->fieldSettings = [];
        $bodyFieldCreate->isSearchable = true;
        $bodyFieldCreate->defaultValue = 'default content';

        $typeCreate->addFieldDefinition($bodyFieldCreate);

        $groups = [
            $contentTypeService->loadContentTypeGroupByIdentifier('Media'),
            $contentTypeService->loadContentTypeGroupByIdentifier('Setup'),
        ];

        $contentTypeDraft = $contentTypeService->createContentType(
            $typeCreate,
            $groups
        );
        /* END: Use Case */

        $this->assertInstanceOf(
            ContentType::class,
            $contentTypeDraft
        );

        return [
            'typeCreate' => $typeCreate,
            'contentType' => $contentTypeDraft,
            'groups' => $groups,
        ];
    }

    /**
     * Test for the createContentType() method struct values.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentTypeService::createContentType
     * @depends testCreateContentType
     *
     * @param array $data
     */
    public function testCreateContentTypeStructValues(array $data)
    {
        $typeCreate = $data['typeCreate'];
        $contentType = $data['contentType'];
        $groups = $data['groups'];

        foreach ($typeCreate as $propertyName => $propertyValue) {
            switch ($propertyName) {
                case 'fieldDefinitions':
                    $this->assertFieldDefinitionsCorrect(
                        $typeCreate->fieldDefinitions,
                        $contentType->fieldDefinitions->toArray()
                    );
                    break;

                case 'creationDate':
                case 'modificationDate':
                    $this->assertEquals(
                        $typeCreate->$propertyName->getTimestamp(),
                        $contentType->$propertyName->getTimestamp()
                    );
                    break;

                default:
                    $this->assertEquals(
                        $typeCreate->$propertyName,
                        $contentType->$propertyName,
                        "Did not assert that property '${propertyName}' is equal on struct and resulting value object"
                    );
                    break;
            }
        }

        $this->assertContentTypeGroupsCorrect(
            $groups,
            $contentType->contentTypeGroups
        );

        $this->assertNotNull(
            $contentType->id
        );
    }

    /**
     * Asserts field definition creation.
     *
     * Asserts that all field definitions defined through created structs in
     * $expectedDefinitionCreates have been correctly created in
     * $actualDefinitions.
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinitionCreateStruct[] $expectedDefinitionCreates
     * @param \Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition[] $actualDefinitions
     */
    protected function assertFieldDefinitionsCorrect(
        array $expectedDefinitionCreates,
        array $actualDefinitions
    ): void {
        $this->assertSameSize(
            $expectedDefinitionCreates,
            $actualDefinitions,
            'Count of field definition creates did not match count of field definitions.'
        );

        $sorter = static function ($a, $b) {
            return strcmp($a->identifier, $b->identifier);
        };

        usort($expectedDefinitionCreates, $sorter);
        usort($actualDefinitions, $sorter);

        foreach ($expectedDefinitionCreates as $key => $expectedCreate) {
            $this->assertFieldDefinitionsEqual(
                $expectedCreate,
                $actualDefinitions[$key]
            );
        }
    }

    /**
     * Asserts that a field definition has been correctly created.
     *
     * Asserts that the given $actualDefinition is correctly created from the
     * create struct in $expectedCreate.
     */
    protected function assertFieldDefinitionsEqual(
        FieldDefinitionCreateStruct $expectedCreate,
        FieldDefinition $actualDefinition
    ): void {
        foreach ($expectedCreate as $propertyName => $propertyValue) {
            $this->assertEquals(
                $expectedCreate->$propertyName,
                $actualDefinition->$propertyName
            );
        }
    }

    /**
     * Asserts that two sets of ContentTypeGroups are equal.
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeGroup[] $expectedGroups
     * @param \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeGroup[] $actualGroups
     */
    protected function assertContentTypeGroupsCorrect($expectedGroups, $actualGroups)
    {
        $sorter = static function ($a, $b) {
            return strcmp($a->id, $b->id);
        };

        usort($expectedGroups, $sorter);
        usort($actualGroups, $sorter);

        foreach ($expectedGroups as $key => $expectedGroup) {
            $this->assertStructPropertiesCorrect(
                $expectedGroup,
                $actualGroups[$key],
                [
                    'id',
                    'identifier',
                    'creationDate',
                    'modificationDate',
                    'creatorId',
                    'modifierId',
                ]
            );
        }
    }

    /**
     * Test for the createContentType() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentTypeService::createContentType()
     * @depends testCreateContentType
     */
    public function testCreateContentTypeThrowsInvalidArgumentExceptionDuplicateIdentifier()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Argument \'$contentTypeCreateStruct\' is invalid: Another Content Type with identifier \'folder\' exists');

        $repository = $this->getRepository();

        /* BEGIN: Use Case */
        $contentTypeService = $repository->getContentTypeService();

        $typeCreate = $contentTypeService->newContentTypeCreateStruct('folder');
        $typeCreate->mainLanguageCode = 'eng-GB';
        $typeCreate->names = ['eng-GB' => 'Article'];

        $firstFieldCreate = $contentTypeService->newFieldDefinitionCreateStruct('title', 'ezstring');
        $typeCreate->addFieldDefinition($firstFieldCreate);

        $groups = [
            $contentTypeService->loadContentTypeGroupByIdentifier('Media'),
            $contentTypeService->loadContentTypeGroupByIdentifier('Setup'),
        ];

        // Throws exception, since type "folder" exists
        $contentTypeService->createContentType($typeCreate, $groups);
        /* END: Use Case */
    }

    /**
     * Test for the createContentType() method trying to create Content Type with already existing
     * remoteId.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentTypeService::createContentType()
     * @depends testCreateContentType
     */
    public function testCreateContentTypeThrowsInvalidArgumentExceptionDuplicateRemoteId()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Another Content Type with remoteId \'a3d405b81be900468eb153d774f4f0d2\' exists');

        $repository = $this->getRepository();

        /* BEGIN: Use Case */
        $contentTypeService = $repository->getContentTypeService();

        $typeCreate = $contentTypeService->newContentTypeCreateStruct('news-article');
        $typeCreate->remoteId = 'a3d405b81be900468eb153d774f4f0d2';
        $typeCreate->mainLanguageCode = 'eng-GB';
        $typeCreate->names = ['eng-GB' => 'Article'];

        $firstFieldCreate = $contentTypeService->newFieldDefinitionCreateStruct('title', 'ezstring');
        $typeCreate->addFieldDefinition($firstFieldCreate);

        $groups = [
            $contentTypeService->loadContentTypeGroupByIdentifier('Media'),
            $contentTypeService->loadContentTypeGroupByIdentifier('Setup'),
        ];

        // Throws exception, since "folder" type has this remote ID
        $contentTypeService->createContentType($typeCreate, $groups);
        /* END: Use Case */
    }

    /**
     * Test for the createContentType() method creating content with duplicate field identifiers.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentTypeService::createContentType
     * @depends testCreateContentType
     */
    public function testCreateContentTypeThrowsInvalidArgumentExceptionDuplicateFieldIdentifier()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Argument \'$contentTypeCreateStruct\' is invalid: The argument contains duplicate Field definition identifier \'title\'');

        $repository = $this->getRepository();

        /* BEGIN: Use Case */
        $contentTypeService = $repository->getContentTypeService();

        $typeCreate = $contentTypeService->newContentTypeCreateStruct('blog-post');
        $typeCreate->mainLanguageCode = 'eng-GB';
        $typeCreate->names = ['eng-GB' => 'Blog post'];

        $firstFieldCreate = $contentTypeService->newFieldDefinitionCreateStruct('title', 'ezstring');
        $typeCreate->addFieldDefinition($firstFieldCreate);

        $secondFieldCreate = $contentTypeService->newFieldDefinitionCreateStruct('title', 'ezstring');
        $typeCreate->addFieldDefinition($secondFieldCreate);

        $groups = [
            $contentTypeService->loadContentTypeGroupByIdentifier('Media'),
            $contentTypeService->loadContentTypeGroupByIdentifier('Setup'),
        ];

        // Throws exception, due to duplicate "title" field
        $contentTypeService->createContentType($typeCreate, $groups);
        /* END: Use Case */
    }

    /**
     * Test for the createContentTypeGroup() method trying to create a content type with already
     * existing identifier.
     *
     * @depends testCreateContentType
     * @covers \Ibexa\Contracts\Core\Repository\ContentTypeService::createContentType
     */
    public function testCreateContentTypeThrowsInvalidArgumentExceptionDuplicateContentTypeIdentifier()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Another Content Type with identifier \'blog-post\' exists');

        $repository = $this->getRepository();
        $contentTypeService = $repository->getContentTypeService();

        // create published content type with identifier "blog-post"
        $contentTypeDraft = $this->createContentTypeDraft();
        $contentTypeService->publishContentTypeDraft($contentTypeDraft);

        $typeCreateStruct = $contentTypeService->newContentTypeCreateStruct('blog-post');
        $typeCreateStruct->remoteId = 'other-remote-id';
        $typeCreateStruct->creatorId = $repository->getPermissionResolver()->getCurrentUserReference()->getUserId();
        $typeCreateStruct->creationDate = new \DateTime();
        $typeCreateStruct->mainLanguageCode = 'eng-US';
        $typeCreateStruct->names = ['eng-US' => 'A name.'];
        $typeCreateStruct->descriptions = ['eng-US' => 'A description.'];

        $fieldCreate = $contentTypeService->newFieldDefinitionCreateStruct('test', 'eztext');
        $typeCreateStruct->addFieldDefinition($fieldCreate);

        // Throws an exception because content type with identifier "blog-post" already exists
        $contentTypeService->createContentType(
            $typeCreateStruct,
            [
                $contentTypeService->loadContentTypeGroupByIdentifier('Content'),
            ]
        );
    }

    /**
     * Test for the createContentType() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentTypeService::createContentType()
     * @depends testCreateContentType
     */
    public function testCreateContentTypeThrowsContentTypeFieldDefinitionValidationException()
    {
        $repository = $this->getRepository();

        /* BEGIN: Use Case */
        $contentTypeService = $repository->getContentTypeService();

        $typeCreate = $contentTypeService->newContentTypeCreateStruct('blog-post');
        $typeCreate->mainLanguageCode = 'eng-GB';
        $typeCreate->names = ['eng-GB' => 'Blog post'];

        $fieldCreate = $contentTypeService->newFieldDefinitionCreateStruct('temperature', 'ezinteger');
        $fieldCreate->isSearchable = true;
        $fieldCreate->validatorConfiguration = [
            'IntegerValueValidator' => [
                'minIntegerValue' => 'forty two point one',
                'maxIntegerValue' => 75,
            ],
        ];
        $typeCreate->addFieldDefinition($fieldCreate);

        $groups = [
            $contentTypeService->loadContentTypeGroupByIdentifier('Media'),
            $contentTypeService->loadContentTypeGroupByIdentifier('Setup'),
        ];

        try {
            // Throws validation exception, because field's validator configuration is invalid
            $contentType = $contentTypeService->createContentType($typeCreate, $groups);
        } catch (ContentTypeFieldDefinitionValidationException $e) {
            $validationErrors = $e->getFieldErrors();
        }
        /* END: Use Case */

        /* @var $validationErrors */
        $this->assertTrue(isset($validationErrors));
        $this->assertIsArray($validationErrors);
        $this->assertCount(1, $validationErrors);
        $this->assertArrayHasKey('temperature', $validationErrors);
        $this->assertIsArray($validationErrors['temperature']);
        $this->assertCount(1, $validationErrors['temperature']);
        $this->assertInstanceOf(ValidationError::class, $validationErrors['temperature'][0]);

        $this->assertEquals(
            new Message(
                "Validator parameter '%parameter%' value must be of integer type",
                ['%parameter%' => 'minIntegerValue']
            ),
            $validationErrors['temperature'][0]->getTranslatableMessage()
        );
    }

    /**
     * Test for the createContentTypeGroup() method called with no groups.
     *
     * @depends testCreateContentType
     * @covers \Ibexa\Contracts\Core\Repository\ContentTypeService::createContentTypeGroup
     */
    public function testCreateContentTypeThrowsInvalidArgumentExceptionGroupsEmpty()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Argument \'$contentTypeGroups\' is invalid: The argument must contain at least one Content Type group');

        $repository = $this->getRepository();

        $contentTypeService = $repository->getContentTypeService();

        $contentTypeCreateStruct = $contentTypeService->newContentTypeCreateStruct(
            'new-type'
        );
        $contentTypeCreateStruct->mainLanguageCode = 'eng-GB';
        $contentTypeCreateStruct->names = ['eng-GB' => 'Test type'];

        // Thrown an exception because array of content type groups is empty
        $contentTypeService->createContentType($contentTypeCreateStruct, []);
    }

    /**
     * Test for the newContentTypeUpdateStruct() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentTypeService::newContentTypeUpdateStruct()
     */
    public function testNewContentTypeUpdateStruct()
    {
        $repository = $this->getRepository();

        /* BEGIN: Use Case */
        $contentTypeService = $repository->getContentTypeService();

        $typeUpdate = $contentTypeService->newContentTypeUpdateStruct();
        /* END: Use Case */

        $this->assertInstanceOf(
            ContentTypeUpdateStruct::class,
            $typeUpdate
        );

        return $typeUpdate;
    }

    /**
     * Test for the newContentTypeUpdateStruct() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentTypeService::newContentTypeUpdateStruct()
     * @depends testNewContentTypeUpdateStruct
     */
    public function testNewContentTypeUpdateStructValues($typeUpdate)
    {
        foreach ($typeUpdate as $propertyName => $propertyValue) {
            $this->assertNull(
                $propertyValue,
                "Property '$propertyName' is not null."
            );
        }
    }

    /**
     * Test for the loadContentTypeDraft() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentTypeService::loadContentTypeDraft()
     * @depends testCreateContentType
     */
    public function testLoadContentTypeDraft()
    {
        $repository = $this->getRepository();
        $contentTypeService = $repository->getContentTypeService();

        /* BEGIN: Use Case */
        $contentTypeDraft = $this->createContentTypeDraft();

        $contentTypeDraftReloaded = $contentTypeService->loadContentTypeDraft(
            $contentTypeDraft->id
        );
        /* END: Use Case */

        $this->assertEquals(
            $contentTypeDraft,
            $contentTypeDraftReloaded
        );
    }

    /**
     * Test for the loadContentTypeDraft() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentTypeService::loadContentTypeDraft()
     * @depends testLoadContentTypeDraft
     */
    public function testLoadContentTypeDraftThrowsNotFoundException()
    {
        $this->expectException(NotFoundException::class);

        $repository = $this->getRepository();

        $nonExistingContentTypeId = $this->generateId('type', 2342);
        /* BEGIN: Use Case */
        $contentTypeService = $repository->getContentTypeService();

        // Throws exception, since 2342 does not exist
        $contentTypeDraft = $contentTypeService->loadContentTypeDraft($nonExistingContentTypeId);
        /* END: Use Case */
    }

    /**
     * Test for the loadContentTypeDraft() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentTypeService::loadContentTypeDraft()
     */
    public function testLoadContentTypeDraftThrowsNotFoundExceptionIfDiffrentOwner()
    {
        $this->expectException(NotFoundException::class);

        $repository = $this->getRepository();
        $permissionResolver = $repository->getPermissionResolver();
        $contentTypeService = $repository->getContentTypeService();

        $draft = $this->createContentTypeDraft();

        $anotherUser = $this->createUserVersion1('anotherUser');
        $permissionResolver->setCurrentUserReference($anotherUser);

        $contentTypeDraft = $contentTypeService->loadContentTypeDraft($draft->id);
    }

    /**
     * Test for the loadContentTypeDraft() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentTypeService::loadContentTypeDraft()
     */
    public function testCanLoadContentTypeDraftEvenIfDiffrentOwner()
    {
        $repository = $this->getRepository();
        $userService = $repository->getUserService();
        $permissionResolver = $repository->getPermissionResolver();
        $contentTypeService = $repository->getContentTypeService();

        $draft = $this->createContentTypeDraft();

        $anotherUser = $this->createUserVersion1('anotherUser');
        $permissionResolver->setCurrentUserReference($anotherUser);

        $loadedDraft = $contentTypeService->loadContentTypeDraft($draft->id, true);

        $this->assertSame((int)$loadedDraft->id, (int)$draft->id);
    }

    /**
     * Test for the updateContentTypeDraft() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentTypeService::updateContentTypeDraft()
     */
    public function testUpdateContentTypeDraft()
    {
        $repository = $this->getRepository();
        $contentTypeService = $repository->getContentTypeService();

        $modifierId = $this->generateId('user', 14);
        /* BEGIN: Use Case */
        $contentTypeDraft = $this->createContentTypeDraft();

        $typeUpdate = $contentTypeService->newContentTypeUpdateStruct();
        $typeUpdate->identifier = 'news-article';
        $typeUpdate->remoteId = '4cf35f5166fd31bf0cda859dc837e095daee9833';
        $typeUpdate->urlAliasSchema = 'url@alias|scheme';
        $typeUpdate->nameSchema = '@name@scheme@';
        $typeUpdate->isContainer = true;
        $typeUpdate->mainLanguageCode = 'eng-US';
        $typeUpdate->defaultAlwaysAvailable = false;
        $typeUpdate->modifierId = $modifierId;
        $typeUpdate->modificationDate = $this->createDateTime();
        $typeUpdate->names = [
            'eng-GB' => 'News article',
            'ger-DE' => 'Nachrichten-Artikel',
        ];
        $typeUpdate->descriptions = [
            'eng-GB' => 'A news article',
            'ger-DE' => 'Ein Nachrichten-Artikel',
        ];

        $contentTypeService->updateContentTypeDraft($contentTypeDraft, $typeUpdate);
        /* END: Use Case */

        $updatedType = $contentTypeService->loadContentTypeDraft(
            $contentTypeDraft->id
        );

        $this->assertInstanceOf(
            ContentTypeDraft::class,
            $updatedType
        );

        return [
            'originalType' => $contentTypeDraft,
            'updateStruct' => $typeUpdate,
            'updatedType' => $updatedType,
        ];
    }

    /**
     * Test for the updateContentTypeDraft() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentTypeService::updateContentTypeDraft()
     * @depends testUpdateContentTypeDraft
     */
    public function testUpdateContentTypeDraftStructValues($data)
    {
        $originalType = $data['originalType'];
        $updateStruct = $data['updateStruct'];
        $updatedType = $data['updatedType'];

        $expectedValues = [
            'id' => $originalType->id,
            'names' => $updateStruct->names,
            'descriptions' => $updateStruct->descriptions,
            'identifier' => $updateStruct->identifier,
            'creationDate' => $originalType->creationDate,
            'modificationDate' => $updateStruct->modificationDate,
            'creatorId' => $originalType->creatorId,
            'modifierId' => $updateStruct->modifierId,
            'urlAliasSchema' => $updateStruct->urlAliasSchema,
            'nameSchema' => $updateStruct->nameSchema,
            'isContainer' => $updateStruct->isContainer,
            'mainLanguageCode' => $updateStruct->mainLanguageCode,
            'contentTypeGroups' => $originalType->contentTypeGroups,
            'fieldDefinitions' => $originalType->fieldDefinitions,
        ];

        $this->assertPropertiesCorrect(
            $expectedValues,
            $updatedType
        );

        foreach ($originalType->fieldDefinitions as $index => $expectedFieldDefinition) {
            $actualFieldDefinition = $updatedType->fieldDefinitions[$index];
            $this->assertInstanceOf(
                FieldDefinition::class,
                $actualFieldDefinition
            );
            $this->assertEquals($expectedFieldDefinition, $actualFieldDefinition);
        }
    }

    /**
     * @covers \Ibexa\Contracts\Core\Repository\ContentTypeService::updateContentTypeDraft
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\ForbiddenException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function testUpdateContentTypeDraftWithNewTranslation()
    {
        $repository = $this->getRepository();
        $contentTypeService = $repository->getContentTypeService();

        $contentTypeDraft = $this->createContentTypeDraft();
        $contentTypeService->publishContentTypeDraft($contentTypeDraft);

        $contentType = $contentTypeService->loadContentType($contentTypeDraft->id);
        // sanity check
        self::assertEquals(
            ['eng-US', 'ger-DE'],
            array_keys($contentType->getNames())
        );

        $contentTypeDraft = $contentTypeService->createContentTypeDraft($contentType);
        $updateStruct = $contentTypeService->newContentTypeUpdateStruct();
        $updateStruct->names = [
            'eng-GB' => 'BrE blog post',
        ];
        $contentTypeService->updateContentTypeDraft($contentTypeDraft, $updateStruct);
        $contentTypeService->publishContentTypeDraft($contentTypeDraft);

        self::assertEquals(
            [
                'eng-US' => 'Blog post',
                'ger-DE' => 'Blog-Eintrag',
                'eng-GB' => 'BrE blog post',
            ],
            $contentTypeService->loadContentType($contentType->id)->getNames()
        );
    }

    /**
     * Test for the updateContentTypeDraft() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentTypeService::updateContentTypeDraft()
     * @depends testUpdateContentTypeDraft
     */
    public function testUpdateContentTypeDraftThrowsInvalidArgumentExceptionDuplicateIdentifier()
    {
        $this->expectException(InvalidArgumentException::class);

        $repository = $this->getRepository();
        $contentTypeService = $repository->getContentTypeService();

        /* BEGIN: Use Case */
        $contentTypeDraft = $this->createContentTypeDraft();

        $typeUpdate = $contentTypeService->newContentTypeUpdateStruct();
        $typeUpdate->identifier = 'folder';

        // Throws exception, since type "folder" already exists
        $contentTypeService->updateContentTypeDraft($contentTypeDraft, $typeUpdate);
        /* END: Use Case */
    }

    /**
     * Test for the updateContentTypeDraft() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentTypeService::updateContentTypeDraft()
     * @depends testUpdateContentTypeDraft
     */
    public function testUpdateContentTypeDraftThrowsInvalidArgumentExceptionDuplicateRemoteId()
    {
        $this->expectException(InvalidArgumentException::class);

        $repository = $this->getRepository();
        $contentTypeService = $repository->getContentTypeService();

        /* BEGIN: Use Case */
        $contentTypeDraft = $this->createContentTypeDraft();

        $typeUpdate = $contentTypeService->newContentTypeUpdateStruct();
        $typeUpdate->remoteId = 'a3d405b81be900468eb153d774f4f0d2';

        // Throws exception, since remote ID of type "folder" is used
        $contentTypeService->updateContentTypeDraft($contentTypeDraft, $typeUpdate);
        /* END: Use Case */
    }

    /**
     * Test for the updateContentTypeDraft() method.
     *
     * @depends testUpdateContentTypeDraft
     * @covers \Ibexa\Contracts\Core\Repository\ContentTypeService::updateContentTypeDraft
     */
    public function testUpdateContentTypeDraftThrowsInvalidArgumentExceptionNoDraftForAuthenticatedUser()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Argument \'$contentTypeDraft\' is invalid: There is no Content Type draft assigned to the authenticated user');

        $repository = $this->getRepository();
        $contentTypeService = $repository->getContentTypeService();
        $roleService = $repository->getRoleService();

        $contentTypeDraft = $this->createContentTypeDraft();
        $typeUpdate = $contentTypeService->newContentTypeUpdateStruct();

        // create Role allowing Content Type updates
        $roleCreateStruct = $roleService->newRoleCreateStruct('ContentTypeUpdaters');
        $policyCreateStruct = $roleService->newPolicyCreateStruct('class', 'update');
        $roleDraft = $roleService->createRole($roleCreateStruct);
        $roleService->addPolicyByRoleDraft($roleDraft, $policyCreateStruct);
        $roleService->publishRoleDraft($roleDraft);

        $user = $this->createUserVersion1();
        $roleService->assignRoleToUser(
            $roleService->loadRoleByIdentifier('ContentTypeUpdaters'),
            $user
        );
        $repository->getPermissionResolver()->setCurrentUserReference($user);

        // Throws exception, since draft belongs to another user
        $contentTypeService->updateContentTypeDraft($contentTypeDraft, $typeUpdate);
    }

    /**
     * Test for the addFieldDefinition() method.
     *
     * @return array
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentTypeService::addFieldDefinition()
     * @depends testCreateContentType
     */
    public function testAddFieldDefinition()
    {
        $repository = $this->getRepository();
        $contentTypeService = $repository->getContentTypeService();

        /* BEGIN: Use Case */
        $contentTypeDraft = $this->createContentTypeDraft();

        $fieldDefCreate = $contentTypeService->newFieldDefinitionCreateStruct('tags', 'ezstring');
        $fieldDefCreate->names = [
            'eng-GB' => 'Tags',
            'ger-DE' => 'Schlagworte',
        ];
        $fieldDefCreate->descriptions = [
            'eng-GB' => 'Tags of the blog post',
            'ger-DE' => 'Schlagworte des Blog-Eintrages',
        ];
        $fieldDefCreate->fieldGroup = 'blog-meta';
        $fieldDefCreate->position = 1;
        $fieldDefCreate->isTranslatable = true;
        $fieldDefCreate->isRequired = true;
        $fieldDefCreate->isInfoCollector = false;
        $fieldDefCreate->validatorConfiguration = [
            'StringLengthValidator' => [
                'minStringLength' => 0,
                'maxStringLength' => 0,
            ],
        ];
        $fieldDefCreate->fieldSettings = [];
        $fieldDefCreate->isSearchable = true;
        $fieldDefCreate->defaultValue = 'default tags';

        $contentTypeService->addFieldDefinition($contentTypeDraft, $fieldDefCreate);
        /* END: Use Case */

        $loadedType = $contentTypeService->loadContentTypeDraft($contentTypeDraft->id);

        $this->assertInstanceOf(
            ContentTypeDraft::class,
            $loadedType
        );

        return [
            'loadedType' => $loadedType,
            'fieldDefCreate' => $fieldDefCreate,
        ];
    }

    /**
     * Test for the addFieldDefinition() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentTypeService::addFieldDefinition()
     * @depends testAddFieldDefinition
     */
    public function testAddFieldDefinitionStructValues(array $data)
    {
        $loadedType = $data['loadedType'];
        $fieldDefCreate = $data['fieldDefCreate'];

        foreach ($loadedType->fieldDefinitions as $fieldDefinition) {
            if ($fieldDefinition->identifier == $fieldDefCreate->identifier) {
                $this->assertFieldDefinitionsEqual($fieldDefCreate, $fieldDefinition);

                return;
            }
        }

        $this->fail(
            sprintf(
                'Could not create Field definition with identifier "%s".',
                $fieldDefCreate->identifier
            )
        );
    }

    /**
     * Test for the addFieldDefinition() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentTypeService::addFieldDefinition()
     * @depends testAddFieldDefinition
     */
    public function testAddFieldDefinitionThrowsInvalidArgumentExceptionDuplicateFieldIdentifier()
    {
        $this->expectException(InvalidArgumentException::class);

        $repository = $this->getRepository();
        $contentTypeService = $repository->getContentTypeService();

        /* BEGIN: Use Case */
        $contentTypeDraft = $this->createContentTypeDraft();

        $fieldDefCreate = $contentTypeService->newFieldDefinitionCreateStruct('title', 'ezstring');

        // Throws an exception
        $contentTypeService->addFieldDefinition($contentTypeDraft, $fieldDefCreate);
        /* END: Use Case */
    }

    /**
     * Test for the addFieldDefinition() method.
     *
     * Testing that field definition of non-repeatable field type can not be added multiple
     * times to the same ContentType.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentTypeService::addFieldDefinition()
     * @depends testAddFieldDefinition
     */
    public function testAddFieldDefinitionThrowsContentTypeFieldDefinitionValidationException()
    {
        $repository = $this->getRepository();
        $contentTypeService = $repository->getContentTypeService();

        /* BEGIN: Use Case */
        $userContentType = $contentTypeService->loadContentTypeByIdentifier('user');
        $userContentTypeDraft = $contentTypeService->createContentTypeDraft($userContentType);

        $fieldDefCreate = $contentTypeService->newFieldDefinitionCreateStruct('temperature', 'ezinteger');
        $fieldDefCreate->isSearchable = true;
        $fieldDefCreate->validatorConfiguration = [
            'IntegerValueValidator' => [
                'minIntegerValue' => 42,
                'maxIntegerValue' => 75.3,
            ],
        ];
        $fieldDefCreate->fieldGroup = 'blog-meta';
        $fieldDefCreate->position = 1;
        $fieldDefCreate->isTranslatable = false;
        $fieldDefCreate->isRequired = true;
        $fieldDefCreate->isInfoCollector = false;
        $fieldDefCreate->fieldSettings = [];

        try {
            // Throws an exception because field's validator configuration is invalid
            $contentTypeService->addFieldDefinition($userContentTypeDraft, $fieldDefCreate);
        } catch (ContentTypeFieldDefinitionValidationException $e) {
            $validationErrors = $e->getFieldErrors();
        }
        /* END: Use Case */

        /* @var $validationErrors */
        $this->assertTrue(isset($validationErrors));
        $this->assertIsArray($validationErrors);
        $this->assertCount(1, $validationErrors);
        $this->assertArrayHasKey('temperature', $validationErrors);
        $this->assertIsArray($validationErrors['temperature']);
        $this->assertCount(1, $validationErrors['temperature']);
        $this->assertInstanceOf(ValidationError::class, $validationErrors['temperature'][0]);

        $this->assertEquals(
            new Message(
                "Validator parameter '%parameter%' value must be of integer type",
                ['%parameter%' => 'maxIntegerValue']
            ),
            $validationErrors['temperature'][0]->getTranslatableMessage()
        );
    }

    /**
     * Test for the addFieldDefinition() method.
     *
     * Testing that field definition of non-repeatable field type can not be added multiple
     * times to the same ContentType.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentTypeService::addFieldDefinition()
     * @depends testAddFieldDefinition
     */
    public function testAddFieldDefinitionThrowsBadStateExceptionNonRepeatableField()
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('The Content Type already contains a Field definition of the singular Field Type \'ezuser\'');

        $repository = $this->getRepository();
        $contentTypeService = $repository->getContentTypeService();

        /* BEGIN: Use Case */
        $userContentType = $contentTypeService->loadContentTypeByIdentifier('user');
        $userContentTypeDraft = $contentTypeService->createContentTypeDraft($userContentType);

        $fieldDefCreate = $contentTypeService->newFieldDefinitionCreateStruct('second_user_account', 'ezuser');
        $fieldDefCreate->names = [
            'eng-GB' => 'Second user account',
        ];
        $fieldDefCreate->descriptions = [
            'eng-GB' => 'Second user account for the ContentType',
        ];
        $fieldDefCreate->fieldGroup = 'users';
        $fieldDefCreate->position = 1;
        $fieldDefCreate->isTranslatable = false;
        $fieldDefCreate->isRequired = true;
        $fieldDefCreate->isInfoCollector = false;
        $fieldDefCreate->validatorConfiguration = [];
        $fieldDefCreate->fieldSettings = [];
        $fieldDefCreate->isSearchable = false;

        // Throws an exception because $userContentTypeDraft already contains non-repeatable field type definition 'ezuser'
        $contentTypeService->addFieldDefinition($userContentTypeDraft, $fieldDefCreate);
        /* END: Use Case */
    }

    /**
     * Test for the ContentTypeService::createContentType() method.
     *
     * Testing that field definition of non-repeatable field type can not be added multiple
     * times to the same ContentTypeCreateStruct.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentTypeService::createContentType()
     */
    public function testCreateContentThrowsContentTypeValidationException()
    {
        $this->expectException(ContentTypeValidationException::class);
        $this->expectExceptionMessage('Field Type \'ezuser\' is singular and cannot be used more than once in a Content Type');

        $repository = $this->getRepository();

        /* BEGIN: Use Case */
        $contentTypeService = $repository->getContentTypeService();
        $contentTypeCreateStruct = $contentTypeService->newContentTypeCreateStruct('this_is_new');
        $contentTypeCreateStruct->names = ['eng-GB' => 'This is new'];
        $contentTypeCreateStruct->mainLanguageCode = 'eng-GB';

        // create first field definition
        $firstFieldDefinition = $contentTypeService->newFieldDefinitionCreateStruct(
            'first_user',
            'ezuser'
        );
        $firstFieldDefinition->names = [
            'eng-GB' => 'First user account',
        ];
        $firstFieldDefinition->position = 1;

        $contentTypeCreateStruct->addFieldDefinition($firstFieldDefinition);

        // create second field definition
        $secondFieldDefinition = $contentTypeService->newFieldDefinitionCreateStruct(
            'second_user',
            'ezuser'
        );
        $secondFieldDefinition->names = [
            'eng-GB' => 'Second user account',
        ];
        $secondFieldDefinition->position = 2;

        $contentTypeCreateStruct->addFieldDefinition($secondFieldDefinition);

        // Throws an exception because the ContentTypeCreateStruct has a singular field repeated
        $contentTypeService->createContentType(
            $contentTypeCreateStruct,
            [$contentTypeService->loadContentTypeGroupByIdentifier('Content')]
        );
        /* END: Use Case */
    }

    /**
     * Test for the addFieldDefinition() method.
     *
     * Testing adding field definition of the field type that can not be added to the ContentType that
     * already has Content instances.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentTypeService::addFieldDefinition()
     * @depends testAddFieldDefinition
     */
    public function testAddFieldDefinitionThrowsBadStateExceptionContentInstances()
    {
        $this->expectException(BadStateException::class);
        $this->expectExceptionMessage('A Field definition of the \'ezuser\' Field Type cannot be added because the Content Type already has Content items');

        $repository = $this->getRepository();
        $contentTypeService = $repository->getContentTypeService();

        /* BEGIN: Use Case */
        $folderContentType = $contentTypeService->loadContentTypeByIdentifier('folder');
        $folderContentTypeDraft = $contentTypeService->createContentTypeDraft($folderContentType);

        $fieldDefCreate = $contentTypeService->newFieldDefinitionCreateStruct('user_account', 'ezuser');
        $fieldDefCreate->names = [
            'eng-GB' => 'User account',
        ];
        $fieldDefCreate->descriptions = [
            'eng-GB' => 'User account field definition for ContentType that has Content instances',
        ];
        $fieldDefCreate->fieldGroup = 'users';
        $fieldDefCreate->position = 1;
        $fieldDefCreate->isTranslatable = false;
        $fieldDefCreate->isRequired = true;
        $fieldDefCreate->isInfoCollector = false;
        $fieldDefCreate->validatorConfiguration = [];
        $fieldDefCreate->fieldSettings = [];
        $fieldDefCreate->isSearchable = false;

        // Throws an exception because 'ezuser' type field definition can't be added to ContentType that already has Content instances
        $contentTypeService->addFieldDefinition($folderContentTypeDraft, $fieldDefCreate);
        /* END: Use Case */
    }

    /**
     * Test for the removeFieldDefinition() method.
     *
     * @return array
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentTypeService::removeFieldDefinition()
     * @depends testCreateContentType
     */
    public function testRemoveFieldDefinition()
    {
        $repository = $this->getRepository();
        $contentTypeService = $repository->getContentTypeService();

        /* BEGIN: Use Case */
        $contentTypeDraft = $this->createContentTypeDraft();

        $bodyField = $contentTypeDraft->getFieldDefinition('body');

        $contentTypeService->removeFieldDefinition($contentTypeDraft, $bodyField);
        /* END: Use Case */

        $loadedType = $contentTypeService->loadContentTypeDraft($contentTypeDraft->id);

        $this->assertInstanceOf(
            ContentTypeDraft::class,
            $loadedType
        );

        return [
            'removedFieldDefinition' => $bodyField,
            'loadedType' => $loadedType,
        ];
    }

    /**
     * Test for the removeFieldDefinition() method.
     *
     * @param array $data
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentTypeService::removeFieldDefinition()
     * @depends testRemoveFieldDefinition
     */
    public function testRemoveFieldDefinitionRemoved(array $data)
    {
        $removedFieldDefinition = $data['removedFieldDefinition'];
        $loadedType = $data['loadedType'];

        foreach ($loadedType->fieldDefinitions as $fieldDefinition) {
            if ($fieldDefinition->identifier == $removedFieldDefinition->identifier) {
                $this->fail(
                    sprintf(
                        'Field definition with identifier "%s" not removed.',
                        $removedFieldDefinition->identifier
                    )
                );
            }
        }
    }

    /**
     * Test for the removeFieldDefinition() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentTypeService::removeFieldDefinition()
     * @depends testRemoveFieldDefinition
     */
    public function testRemoveFieldDefinitionThrowsInvalidArgumentException()
    {
        $this->expectException(InvalidArgumentException::class);

        $repository = $this->getRepository();
        $contentTypeService = $repository->getContentTypeService();

        /* BEGIN: Use Case */
        $contentTypeDraft = $this->createContentTypeDraft();

        $bodyField = $contentTypeDraft->getFieldDefinition('body');
        $contentTypeService->removeFieldDefinition($contentTypeDraft, $bodyField);

        $loadedDraft = $contentTypeService->loadContentTypeDraft($contentTypeDraft->id);

        // Throws exception, sine "body" has already been removed
        $contentTypeService->removeFieldDefinition($loadedDraft, $bodyField);
        /* END: Use Case */
    }

    /**
     * Test removeFieldDefinition() method for field in a different draft throws an exception.
     *
     * @depends testRemoveFieldDefinition
     * @covers \Ibexa\Contracts\Core\Repository\ContentTypeService::removeFieldDefinition
     */
    public function testRemoveFieldDefinitionThrowsInvalidArgumentExceptionOnWrongDraft()
    {
        $this->expectException(InvalidArgumentException::class);

        $repository = $this->getRepository();
        $contentTypeService = $repository->getContentTypeService();

        $contentTypeDraft01 = $this->createContentTypeDraft();
        $contentTypeDraft02 = $this->createContentTypeDraft();

        $bodyField = $contentTypeDraft02->getFieldDefinition('body');

        // Throws an exception because $bodyField field belongs to another draft
        $contentTypeService->removeFieldDefinition($contentTypeDraft01, $bodyField);
    }

    /**
     * Test for the removeFieldDefinition() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentTypeService::removeFieldDefinition()
     * @depends testRemoveFieldDefinition
     */
    public function testRemoveFieldDefinitionRemovesFieldFromContent()
    {
        $repository = $this->getRepository();

        $contentTypeService = $repository->getContentTypeService();
        $contentService = $repository->getContentService();

        // Create ContentType
        $contentTypeDraft = $this->createContentTypeDraft();
        $contentTypeService->publishContentTypeDraft($contentTypeDraft);
        $publishedType = $contentTypeService->loadContentType($contentTypeDraft->id);

        // Create multi-language Content in all 3 possible versions
        $contentDraft = $this->createContentDraft();
        $archivedContent = $contentService->publishVersion($contentDraft->versionInfo);
        $contentDraft = $contentService->createContentDraft($archivedContent->contentInfo);
        $publishedContent = $contentService->publishVersion($contentDraft->versionInfo);
        $draftContent = $contentService->createContentDraft($publishedContent->contentInfo);

        // Remove field definition from ContentType
        $contentTypeDraft = $contentTypeService->createContentTypeDraft($publishedType);
        $bodyField = $contentTypeDraft->getFieldDefinition('body');
        $contentTypeService->removeFieldDefinition($contentTypeDraft, $bodyField);
        $contentTypeService->publishContentTypeDraft($contentTypeDraft);

        // Reload all versions
        $contentVersion1Archived = $contentService->loadContent(
            $archivedContent->contentInfo->id,
            null,
            $archivedContent->versionInfo->versionNo
        );
        $contentVersion2Published = $contentService->loadContent(
            $publishedContent->contentInfo->id,
            null,
            $publishedContent->versionInfo->versionNo
        );
        $contentVersion3Draft = $contentService->loadContent(
            $draftContent->contentInfo->id,
            null,
            $draftContent->versionInfo->versionNo
        );

        $this->assertInstanceOf(
            Content::class,
            $contentVersion1Archived
        );
        $this->assertInstanceOf(
            Content::class,
            $contentVersion2Published
        );
        $this->assertInstanceOf(
            Content::class,
            $contentVersion3Draft
        );

        return [
            $contentVersion1Archived,
            $contentVersion2Published,
            $contentVersion3Draft,
        ];
    }

    /**
     * Test for the removeFieldDefinition() method.
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Content[] $data
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentTypeService::removeFieldDefinition
     * @depends testRemoveFieldDefinitionRemovesFieldFromContent
     */
    public function testRemoveFieldDefinitionRemovesFieldFromContentRemoved($data)
    {
        list(
            $contentVersion1Archived,
            $contentVersion1Published,
            $contentVersion2Draft
        ) = $data;

        $this->assertFalse(
            isset($contentVersion1Archived->fields['body']),
            'The field was not removed from archived version.'
        );
        $this->assertFalse(
            isset($contentVersion1Published->fields['body']),
            'The field was not removed from published version.'
        );
        $this->assertFalse(
            isset($contentVersion2Draft->fields['body']),
            'The field was not removed from draft version.'
        );
    }

    /**
     * @covers \Ibexa\Contracts\Core\Repository\ContentTypeService::removeFieldDefinition()
     */
    public function testRemoveFieldDefinitionRemovesOrphanedRelations(): void
    {
        $repository = $this->getRepository();

        $contentTypeService = $repository->getContentTypeService();
        $contentService = $repository->getContentService();

        // Create ContentType
        $contentTypeDraft = $this->createContentTypeDraft([$this->getRelationFieldDefinition()]);
        $contentTypeService->publishContentTypeDraft($contentTypeDraft);
        $publishedType = $contentTypeService->loadContentType($contentTypeDraft->id);

        // Create Content with Relation
        $contentDraft = $this->createContentDraft();
        $publishedVersion = $contentService->publishVersion($contentDraft->versionInfo);

        $newDraft = $contentService->createContentDraft($publishedVersion->contentInfo);
        $updateStruct = $contentService->newContentUpdateStruct();
        $updateStruct->setField('relation', 14, 'eng-US');
        $contentDraft = $contentService->updateContent($newDraft->versionInfo, $updateStruct);
        $publishedContent = $contentService->publishVersion($contentDraft->versionInfo);

        // Remove field definition from ContentType
        $contentTypeDraft = $contentTypeService->createContentTypeDraft($publishedType);
        $relationField = $contentTypeDraft->getFieldDefinition('relation');
        $contentTypeService->removeFieldDefinition($contentTypeDraft, $relationField);
        $contentTypeService->publishContentTypeDraft($contentTypeDraft);

        // Load Content
        $content = $contentService->loadContent($publishedContent->contentInfo->id);

        $this->assertCount(0, $contentService->loadRelations($content->versionInfo));
    }

    private function getRelationFieldDefinition(): FieldDefinitionCreateStruct
    {
        $repository = $this->getRepository();

        $contentTypeService = $repository->getContentTypeService();

        $relationFieldCreate = $contentTypeService->newFieldDefinitionCreateStruct(
            'relation',
            'ezobjectrelation'
        );
        $relationFieldCreate->names = ['eng-US' => 'Relation'];
        $relationFieldCreate->descriptions = ['eng-US' => 'Relation to any Content'];
        $relationFieldCreate->fieldGroup = 'blog-content';
        $relationFieldCreate->position = 3;
        $relationFieldCreate->isTranslatable = false;
        $relationFieldCreate->isRequired = false;
        $relationFieldCreate->isInfoCollector = false;
        $relationFieldCreate->validatorConfiguration = [];
        $relationFieldCreate->isSearchable = false;

        return $relationFieldCreate;
    }

    /**
     * Test for the addFieldDefinition() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentTypeService::addFieldDefinition()
     * @depends testAddFieldDefinition
     */
    public function testAddFieldDefinitionAddsFieldToContent()
    {
        $repository = $this->getRepository();

        $contentTypeService = $repository->getContentTypeService();
        $contentService = $repository->getContentService();

        // Create ContentType
        $contentTypeDraft = $this->createContentTypeDraft();
        $contentTypeService->publishContentTypeDraft($contentTypeDraft);
        $publishedType = $contentTypeService->loadContentType($contentTypeDraft->id);

        // Create multi-language Content in all 3 possible versions
        $contentDraft = $this->createContentDraft();
        $archivedContent = $contentService->publishVersion($contentDraft->versionInfo);
        $contentDraft = $contentService->createContentDraft($archivedContent->contentInfo);
        $publishedContent = $contentService->publishVersion($contentDraft->versionInfo);
        $draftContent = $contentService->createContentDraft($publishedContent->contentInfo);

        // Add field definition to ContentType
        $contentTypeDraft = $contentTypeService->createContentTypeDraft($publishedType);

        $fieldDefinitionCreateStruct = $contentTypeService->newFieldDefinitionCreateStruct('byline', 'ezstring');
        $fieldDefinitionCreateStruct->names = [
            'eng-US' => 'Byline',
        ];
        $fieldDefinitionCreateStruct->descriptions = [
            'eng-US' => 'Byline of the blog post',
        ];
        $fieldDefinitionCreateStruct->fieldGroup = 'blog-meta';
        $fieldDefinitionCreateStruct->position = 1;
        $fieldDefinitionCreateStruct->isTranslatable = true;
        $fieldDefinitionCreateStruct->isRequired = true;
        $fieldDefinitionCreateStruct->isInfoCollector = false;
        $fieldDefinitionCreateStruct->validatorConfiguration = [
            'StringLengthValidator' => [
                'minStringLength' => 0,
                'maxStringLength' => 0,
            ],
        ];
        $fieldDefinitionCreateStruct->fieldSettings = [];
        $fieldDefinitionCreateStruct->isSearchable = true;

        $contentTypeService->addFieldDefinition($contentTypeDraft, $fieldDefinitionCreateStruct);
        $contentTypeService->publishContentTypeDraft($contentTypeDraft);

        // Reload all versions
        $contentVersion1Archived = $contentService->loadContent(
            $archivedContent->contentInfo->id,
            null,
            $archivedContent->versionInfo->versionNo
        );
        $contentVersion2Published = $contentService->loadContent(
            $publishedContent->contentInfo->id,
            null,
            $publishedContent->versionInfo->versionNo
        );
        $contentVersion3Draft = $contentService->loadContent(
            $draftContent->contentInfo->id,
            null,
            $draftContent->versionInfo->versionNo
        );

        $this->assertInstanceOf(
            Content::class,
            $contentVersion1Archived
        );
        $this->assertInstanceOf(
            Content::class,
            $contentVersion2Published
        );
        $this->assertInstanceOf(
            Content::class,
            $contentVersion3Draft
        );

        return [
            $contentVersion1Archived,
            $contentVersion2Published,
            $contentVersion3Draft,
        ];
    }

    /**
     * Test for the addFieldDefinition() method.
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Content[] $data
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentTypeService::addFieldDefinition()
     * @depends testAddFieldDefinitionAddsFieldToContent
     */
    public function testAddFieldDefinitionAddsFieldToContentAdded(array $data)
    {
        list(
            $contentVersion1Archived,
            $contentVersion1Published,
            $contentVersion2Draft
            ) = $data;

        $this->assertTrue(
            isset($contentVersion1Archived->fields['byline']),
            'New field was not added to archived version.'
        );
        $this->assertTrue(
            isset($contentVersion1Published->fields['byline']),
            'New field was not added to published version.'
        );
        $this->assertTrue(
            isset($contentVersion2Draft->fields['byline']),
            'New field was not added to draft version.'
        );

        $this->assertEquals(
            $contentVersion1Archived->getField('byline')->id,
            $contentVersion1Published->getField('byline')->id
        );
        $this->assertEquals(
            $contentVersion1Published->getField('byline')->id,
            $contentVersion2Draft->getField('byline')->id
        );
    }

    /**
     * Test for the newFieldDefinitionUpdateStruct() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentTypeService::newFieldDefinitionUpdateStruct()
     */
    public function testNewFieldDefinitionUpdateStruct()
    {
        $repository = $this->getRepository();
        /* BEGIN: Use Case */
        // $draftId contains the ID of a content type draft
        $contentTypeService = $repository->getContentTypeService();

        $updateStruct = $contentTypeService->newFieldDefinitionUpdateStruct();
        /* END: Use Case */

        self::assertInstanceOf(
            FieldDefinitionUpdateStruct::class,
            $updateStruct
        );

        return $updateStruct;
    }

    /**
     * Test for the newFieldDefinitionUpdateStruct() method.
     *
     * @depends testNewFieldDefinitionUpdateStruct
     * @covers \Ibexa\Contracts\Core\Repository\ContentTypeService::newContentTypeUpdateStruct
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinitionUpdateStruct $fieldDefinitionUpdateStruct
     */
    public function testNewFieldDefinitionUpdateStructValues($fieldDefinitionUpdateStruct)
    {
        foreach ($fieldDefinitionUpdateStruct as $propertyName => $propertyValue) {
            $this->assertNull(
                $propertyValue,
                "Property '$propertyName' is not null."
            );
        }
    }

    /**
     * Test for the updateFieldDefinition() method.
     *
     * @return array
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentTypeService::updateFieldDefinition()
     * @depends testLoadContentTypeDraft
     */
    public function testUpdateFieldDefinition()
    {
        $repository = $this->getRepository();
        $contentTypeService = $repository->getContentTypeService();

        /* BEGIN: Use Case */
        $contentTypeDraft = $this->createContentTypeDraft();

        $bodyField = $contentTypeDraft->getFieldDefinition('body');

        $bodyUpdateStruct = $contentTypeService->newFieldDefinitionUpdateStruct();
        $bodyUpdateStruct->identifier = 'blog-body';
        $bodyUpdateStruct->names = [
            'eng-GB' => 'Blog post body',
            'ger-DE' => 'Blog-Eintrags-Textkörper',
        ];
        $bodyUpdateStruct->descriptions = [
            'eng-GB' => 'Blog post body of the blog post',
            'ger-DE' => 'Blog-Eintrags-Textkörper des Blog-Eintrages',
        ];
        $bodyUpdateStruct->fieldGroup = 'updated-blog-content';
        $bodyUpdateStruct->position = 3;
        $bodyUpdateStruct->isTranslatable = false;
        $bodyUpdateStruct->isRequired = false;
        $bodyUpdateStruct->isInfoCollector = true;
        $bodyUpdateStruct->validatorConfiguration = [];
        $bodyUpdateStruct->fieldSettings = [
            'textRows' => 60,
        ];
        $bodyUpdateStruct->isSearchable = false;

        $contentTypeService->updateFieldDefinition(
            $contentTypeDraft,
            $bodyField,
            $bodyUpdateStruct
        );
        /* END: Use Case */

        $loadedDraft = $contentTypeService->loadContentTypeDraft($contentTypeDraft->id);
        $this->assertInstanceOf(
            FieldDefinition::class,
            ($loadedField = $loadedDraft->getFieldDefinition('blog-body'))
        );

        return [
            'originalField' => $bodyField,
            'updatedField' => $loadedField,
            'updateStruct' => $bodyUpdateStruct,
        ];
    }

    /**
     * @covers \Ibexa\Contracts\Core\Repository\ContentTypeService::updateFieldDefinition
     */
    public function testUpdateFieldDefinitionWithNewTranslation()
    {
        $repository = $this->getRepository();
        $contentTypeService = $repository->getContentTypeService();

        /* BEGIN: Use Case */
        $contentTypeDraft = $this->createContentTypeDraft();

        $bodyField = $contentTypeDraft->getFieldDefinition('body');

        self::assertEquals(
            ['eng-US', 'ger-DE'],
            array_keys($bodyField->getNames())
        );

        $bodyUpdateStruct = $contentTypeService->newFieldDefinitionUpdateStruct();
        $bodyUpdateStruct->identifier = 'blog-body';
        $bodyUpdateStruct->names = [
            'eng-GB' => 'New blog post body',
        ];
        $bodyUpdateStruct->descriptions = [
            'eng-GB' => null,
        ];
        $bodyUpdateStruct->fieldGroup = 'updated-blog-content';
        $bodyUpdateStruct->position = 3;
        $bodyUpdateStruct->isTranslatable = false;
        $bodyUpdateStruct->isRequired = false;
        $bodyUpdateStruct->isInfoCollector = true;
        $bodyUpdateStruct->validatorConfiguration = [];
        $bodyUpdateStruct->fieldSettings = [
            'textRows' => 60,
        ];
        $bodyUpdateStruct->isSearchable = false;

        $contentTypeService->updateFieldDefinition(
            $contentTypeDraft,
            $bodyField,
            $bodyUpdateStruct
        );
        /* END: Use Case */

        $contentType = $contentTypeService->loadContentTypeDraft($contentTypeDraft->id);

        self::assertEquals(
            [
                'eng-GB' => 'New blog post body',
                'eng-US' => 'Body',
                'ger-DE' => 'Textkörper',
            ],
            $contentType->getFieldDefinition('blog-body')->getNames()
        );
        self::assertEquals(
            [
                'eng-GB' => null,
                'eng-US' => 'Body of the blog post',
                'ger-DE' => 'Textkörper des Blog-Eintrages',
            ],
            $contentType->getFieldDefinition('blog-body')->getDescriptions()
        );
    }

    /**
     * Test for the updateFieldDefinition() method.
     *
     * @param array $data
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentTypeService::updateFieldDefinition()
     * @depends testUpdateFieldDefinition
     */
    public function testUpdateFieldDefinitionStructValues(array $data)
    {
        $originalField = $data['originalField'];
        $updatedField = $data['updatedField'];
        $updateStruct = $data['updateStruct'];

        $this->assertPropertiesCorrect(
            [
                'id' => $originalField->id,
                'identifier' => $updateStruct->identifier,
                'names' => $updateStruct->names,
                'descriptions' => $updateStruct->descriptions,
                'fieldGroup' => $updateStruct->fieldGroup,
                'position' => $updateStruct->position,
                'fieldTypeIdentifier' => $originalField->fieldTypeIdentifier,
                'isTranslatable' => $updateStruct->isTranslatable,
                'isRequired' => $updateStruct->isRequired,
                'isInfoCollector' => $updateStruct->isInfoCollector,
                'validatorConfiguration' => $updateStruct->validatorConfiguration,
                'defaultValue' => $originalField->defaultValue,
                'isSearchable' => $updateStruct->isSearchable,
            ],
            $updatedField
        );
    }

    /**
     * Test for the updateFieldDefinition() method using an empty FieldDefinitionUpdateStruct.
     *
     * @covers \Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinitionUpdateStruct
     * @covers \Ibexa\Contracts\Core\Repository\ContentTypeService::updateFieldDefinition
     */
    public function testUpdateFieldDefinitionWithEmptyStruct()
    {
        $repository = $this->getRepository();
        $contentTypeService = $repository->getContentTypeService();

        $contentTypeDraft = $this->createContentTypeDraft();
        $fieldDefinition = $contentTypeDraft->getFieldDefinition('body');
        $fieldDefinitionUpdateStruct = $contentTypeService->newFieldDefinitionUpdateStruct();

        $contentTypeService->updateFieldDefinition(
            $contentTypeDraft,
            $fieldDefinition,
            $fieldDefinitionUpdateStruct
        );
        $contentTypeDraft = $contentTypeService->loadContentTypeDraft($contentTypeDraft->id);
        $updatedFieldDefinition = $contentTypeDraft->getFieldDefinition('body');

        self::assertEquals(
            $fieldDefinition,
            $updatedFieldDefinition
        );
    }

    /**
     * Test for the updateFieldDefinition() method with already defined field identifier.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentTypeService::updateFieldDefinition
     */
    public function testUpdateFieldDefinitionThrowsInvalidArgumentExceptionFieldIdentifierExists()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Argument \'$fieldDefinitionUpdateStruct\' is invalid: Another Field definition with identifier \'title\' exists in the Content Type');

        $repository = $this->getRepository();
        $contentTypeService = $repository->getContentTypeService();

        /* BEGIN: Use Case */
        $contentTypeDraft = $this->createContentTypeDraft();

        $bodyField = $contentTypeDraft->getFieldDefinition('body');
        $titleField = $contentTypeDraft->getFieldDefinition('title');

        $bodyUpdateStruct = $contentTypeService->newFieldDefinitionUpdateStruct();
        $bodyUpdateStruct->identifier = 'title';

        // Throws exception, since "title" field already exists
        $contentTypeService->updateFieldDefinition(
            $contentTypeDraft,
            $bodyField,
            $bodyUpdateStruct
        );
        /* END: Use Case */
    }

    /**
     * Test for the updateFieldDefinition() method trying to update non-existent field.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentTypeService::updateFieldDefinition()
     * @depends testLoadContentTypeDraft
     */
    public function testUpdateFieldDefinitionThrowsInvalidArgumentExceptionForUndefinedField()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Argument \'$fieldDefinition\' is invalid: The given Field definition does not belong to the Content Type');

        $repository = $this->getRepository();
        $contentTypeService = $repository->getContentTypeService();

        /* BEGIN: Use Case */
        $contentTypeDraft = $this->createContentTypeDraft();

        $bodyField = $contentTypeDraft->getFieldDefinition('body');
        $contentTypeService->removeFieldDefinition($contentTypeDraft, $bodyField);

        $loadedDraft = $contentTypeService->loadContentTypeDraft($contentTypeDraft->id);

        $bodyUpdateStruct = $contentTypeService->newFieldDefinitionUpdateStruct();

        // Throws exception, since field "body" is already deleted
        $contentTypeService->updateFieldDefinition(
            $loadedDraft,
            $bodyField,
            $bodyUpdateStruct
        );
        /* END: Use Case */
    }

    /**
     * Test for the publishContentTypeDraft() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentTypeService::publishContentTypeDraft()
     * @depends testLoadContentTypeDraft
     */
    public function testPublishContentTypeDraft()
    {
        $repository = $this->getRepository();
        $contentTypeService = $repository->getContentTypeService();

        /* BEGIN: Use Case */
        $contentTypeDraft = $this->createContentTypeDraft();

        $contentTypeService->publishContentTypeDraft($contentTypeDraft);
        /* END: Use Case */

        $publishedType = $contentTypeService->loadContentType($contentTypeDraft->id);

        $this->assertInstanceOf(
            ContentType::class,
            $publishedType
        );
        $this->assertNotInstanceOf(
            ContentTypeDraft::class,
            $publishedType
        );
    }

    /**
     * Test for the publishContentTypeDraft() method setting proper ContentType nameSchema.
     *
     * @depends testPublishContentTypeDraft
     * @covers \Ibexa\Contracts\Core\Repository\ContentTypeService::publishContentTypeDraft
     */
    public function testPublishContentTypeDraftSetsNameSchema()
    {
        $repository = $this->getRepository();
        $contentTypeService = $repository->getContentTypeService();

        $typeCreateStruct = $contentTypeService->newContentTypeCreateStruct(
            'new-type'
        );
        $typeCreateStruct->names = [
            'eng-GB' => 'Type title',
        ];
        $typeCreateStruct->mainLanguageCode = 'eng-GB';

        $titleFieldCreate = $contentTypeService->newFieldDefinitionCreateStruct('title', 'ezstring');
        $titleFieldCreate->position = 1;
        $typeCreateStruct->addFieldDefinition($titleFieldCreate);

        $type = $contentTypeService->createContentType(
            $typeCreateStruct,
            [
                $contentTypeService->loadContentTypeGroupByIdentifier('Content'),
            ]
        );

        $contentTypeService->publishContentTypeDraft($type);

        $loadedContentType = $contentTypeService->loadContentType($type->id);

        $this->assertEquals('<title>', $loadedContentType->nameSchema);
    }

    /**
     * Test that publishing Content Type Draft refreshes list of Content Types in Content Type Groups.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentTypeService::publishContentTypeDraft
     */
    public function testPublishContentTypeDraftRefreshesContentTypesList()
    {
        $repository = $this->getRepository();
        $contentTypeService = $repository->getContentTypeService();

        $contentTypeDraft = $this->createContentTypeDraft();

        // Make sure to 1. check draft is not part of lists, and 2. warm cache to make sure it invalidates
        $contentTypes = $contentTypeService->loadContentTypeList([1, $contentTypeDraft->id]);
        self::assertArrayNotHasKey($contentTypeDraft->id, $contentTypes);
        self::assertCount(1, $contentTypes);

        $contentTypeGroups = $contentTypeDraft->getContentTypeGroups();
        foreach ($contentTypeGroups as $contentTypeGroup) {
            $contentTypes = $contentTypeService->loadContentTypes($contentTypeGroup);
            // check if not published Content Type does not exist on published Content Types list
            self::assertNotContains(
                $contentTypeDraft->id,
                array_map(
                    static function (ContentType $contentType) {
                        return $contentType->id;
                    },
                    $contentTypes
                )
            );
        }

        $contentTypeService->publishContentTypeDraft($contentTypeDraft);

        // After publishing it should be part of lists
        $contentTypes = $contentTypeService->loadContentTypeList([1, $contentTypeDraft->id]);
        self::assertArrayHasKey($contentTypeDraft->id, $contentTypes);
        self::assertCount(2, $contentTypes);

        foreach ($contentTypeGroups as $contentTypeGroup) {
            $contentTypes = $contentTypeService->loadContentTypes($contentTypeGroup);
            // check if published Content is available in published Content Types list
            self::assertContains(
                $contentTypeDraft->id,
                array_map(
                    static function (ContentType $contentType) {
                        return $contentType->id;
                    },
                    $contentTypes
                )
            );
        }
    }

    /**
     * Test for the publishContentTypeDraft() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentTypeService::publishContentTypeDraft()
     * @depends testPublishContentTypeDraft
     */
    public function testPublishContentTypeDraftThrowsBadStateException()
    {
        $this->expectException(BadStateException::class);

        $repository = $this->getRepository();
        $contentTypeService = $repository->getContentTypeService();

        /* BEGIN: Use Case */
        $contentTypeDraft = $this->createContentTypeDraft();

        $contentTypeService->publishContentTypeDraft($contentTypeDraft);

        // Throws exception, since no draft exists anymore
        $contentTypeService->publishContentTypeDraft($contentTypeDraft);
        /* END: Use Case */
    }

    /**
     * Test for the createContentTypeGroup() method trying to create Content Type without any fields.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentTypeService::publishContentTypeDraft()
     * @depends testPublishContentTypeDraft
     */
    public function testPublishContentTypeDraftThrowsInvalidArgumentExceptionWithoutFields()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Argument \'$contentTypeDraft\' is invalid: The Content Type draft should have at least one Field definition');

        $repository = $this->getRepository();
        $contentTypeService = $repository->getContentTypeService();

        $typeCreateStruct = $contentTypeService->newContentTypeCreateStruct(
            'no-fields-type'
        );
        $typeCreateStruct->remoteId = 'new-unique-remoteid';
        $typeCreateStruct->creatorId = $repository->getPermissionResolver()->getCurrentUserReference()->getUserId();
        $typeCreateStruct->creationDate = new \DateTime();
        $typeCreateStruct->mainLanguageCode = 'eng-US';
        $typeCreateStruct->names = ['eng-US' => 'A name.'];
        $typeCreateStruct->descriptions = ['eng-US' => 'A description.'];

        $contentTypeDraft = $contentTypeService->createContentType(
            $typeCreateStruct,
            [
                $contentTypeService->loadContentTypeGroupByIdentifier('Content'),
            ]
        );
        // Throws an exception because Content Type draft should have at least one field definition.
        $contentTypeService->publishContentTypeDraft($contentTypeDraft);
    }

    /**
     * Test for the loadContentType() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentTypeService::loadContentType()
     * @depends testCreateContentType
     * @group user
     * @group field-type
     */
    public function testLoadContentType()
    {
        $repository = $this->getRepository();

        $userGroupId = $this->generateId('type', 3);
        /* BEGIN: Use Case */
        // $userGroupId is the ID of the "user_group" type
        $contentTypeService = $repository->getContentTypeService();
        // Loads the standard "user_group" type
        $userGroupType = $contentTypeService->loadContentType($userGroupId);
        /* END: Use Case */

        $this->assertInstanceOf(
            ContentType::class,
            $userGroupType
        );

        return $userGroupType;
    }

    /**
     * Test that multi-language logic respects prioritized language list.
     *
     * @dataProvider getPrioritizedLanguageList
     *
     * @param string[] $languageCodes
     */
    public function testLoadContentTypeWithPrioritizedLanguagesList(array $languageCodes)
    {
        $repository = $this->getRepository();
        $contentTypeService = $repository->getContentTypeService();

        $contentType = $this->createContentTypeDraft();
        $contentTypeService->publishContentTypeDraft($contentType);
        $contentType = $contentTypeService->loadContentType($contentType->id, $languageCodes);

        $language = isset($languageCodes[0]) ? $languageCodes[0] : 'eng-US';
        /** @var \Ibexa\Core\FieldType\TextLine\Value $nameValue */
        self::assertEquals(
            $contentType->getName($language),
            $contentType->getName()
        );
        self::assertEquals(
            $contentType->getDescription($language),
            $contentType->getDescription()
        );

        foreach ($contentType->getFieldDefinitions() as $fieldDefinition) {
            self::assertEquals(
                $fieldDefinition->getName($language),
                $fieldDefinition->getName()
            );
            self::assertEquals(
                $fieldDefinition->getDescription($language),
                $fieldDefinition->getDescription()
            );
        }
    }

    /**
     * @return array
     */
    public function getPrioritizedLanguageList()
    {
        return [
            [[]],
            [['eng-US']],
            [['ger-DE']],
            [['eng-US', 'ger-DE']],
            [['ger-DE', 'eng-US']],
        ];
    }

    /**
     * Test for the loadContentType() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentTypeService::loadContentType()
     * @depends testLoadContentType
     */
    public function testLoadContentTypeStructValues($userGroupType)
    {
        $repository = $this->getRepository();
        $contentTypeService = $repository->getContentTypeService();

        $this->assertPropertiesCorrect(
            [
                'id' => $this->generateId('type', 3),
                'status' => 0,
                'identifier' => 'user_group',
                'creationDate' => $this->createDateTime(1024392098),
                'modificationDate' => $this->createDateTime(1048494743),
                'creatorId' => $this->generateId('user', 14),
                'modifierId' => $this->generateId('user', 14),
                'remoteId' => '25b4268cdcd01921b808a0d854b877ef',
                'names' => [
                    'eng-US' => 'User group',
                ],
                'descriptions' => [],
                'nameSchema' => '<name>',
                'isContainer' => true,
                'mainLanguageCode' => 'eng-US',
                'defaultAlwaysAvailable' => true,
                'defaultSortField' => 1,
                'defaultSortOrder' => 1,
                'contentTypeGroups' => [
                    0 => $contentTypeService->loadContentTypeGroup($this->generateId('typegroup', 2)),
                ],
            ],
            $userGroupType
        );

        return $userGroupType->fieldDefinitions;
    }

    /**
     * Test for the loadContentType() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentTypeService::loadContentType()
     * @depends testLoadContentTypeStructValues
     */
    public function testLoadContentTypeFieldDefinitions(APIFieldDefinitionCollection $fieldDefinitions)
    {
        $expectedFieldDefinitions = [
            'name' => [
                'identifier' => 'name',
                'fieldGroup' => '',
                'position' => 1,
                'fieldTypeIdentifier' => 'ezstring',
                'isTranslatable' => true,
                'isRequired' => true,
                'isInfoCollector' => false,
                'isSearchable' => true,
                'defaultValue' => new TextLineValue(),
                'names' => [
                    'eng-US' => 'Name',
                ],
                'descriptions' => [],
            ],
            'description' => [
                'identifier' => 'description',
                'fieldGroup' => '',
                'position' => 2,
                'fieldTypeIdentifier' => 'ezstring',
                'isTranslatable' => true,
                'isRequired' => false,
                'isInfoCollector' => false,
                'isSearchable' => true,
                'defaultValue' => new TextLineValue(),
                'names' => [
                    'eng-US' => 'Description',
                ],
                'descriptions' => [],
            ],
        ];

        $fieldDefinitions = $fieldDefinitions->toArray();
        foreach ($fieldDefinitions as $index => $fieldDefinition) {
            $this->assertInstanceOf(
                FieldDefinition::class,
                $fieldDefinition
            );

            $this->assertNotNull($fieldDefinition->id);

            if (!isset($expectedFieldDefinitions[$fieldDefinition->identifier])) {
                $this->fail(
                    sprintf(
                        'Unexpected Field definition loaded: "%s" (%s)',
                        $fieldDefinition->identifier,
                        $fieldDefinition->id
                    )
                );
            }

            $this->assertPropertiesCorrect(
                $expectedFieldDefinitions[$fieldDefinition->identifier],
                $fieldDefinition
            );
            unset($expectedFieldDefinitions[$fieldDefinition->identifier]);
            unset($fieldDefinitions[$index]);
        }

        if (0 !== count($expectedFieldDefinitions)) {
            $this->fail(
                sprintf(
                    'Missing expected Field definitions: %s',
                    implode(',', array_column($expectedFieldDefinitions, 'identifier'))
                )
            );
        }

        if (0 !== count($fieldDefinitions)) {
            $this->fail(
                sprintf(
                    'Loaded unexpected Field definitions: %s',
                    implode(
                        ',',
                        array_map(
                            static function ($fieldDefinition) {
                                return $fieldDefinition->identifier;
                            },
                            $fieldDefinitions
                        )
                    )
                )
            );
        }
    }

    /**
     * Test for the loadContentType() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentTypeService::loadContentType()
     * @depends testLoadContentType
     */
    public function testLoadContentTypeThrowsNotFoundException()
    {
        $this->expectException(NotFoundException::class);

        $repository = $this->getRepository();

        $nonExistentTypeId = $this->generateId('type', 2342);
        /* BEGIN: Use Case */
        $contentTypeService = $repository->getContentTypeService();

        // Throws exception, since type with ID 2342 does not exist
        $contentTypeService->loadContentType($nonExistentTypeId);
        /* END: Use Case */
    }

    /**
     * Test for the loadContentTypeByIdentifier() method.
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentTypeService::loadContentTypeByIdentifier()
     * @depends testLoadContentType
     * @group user
     */
    public function testLoadContentTypeByIdentifier()
    {
        $repository = $this->getRepository();

        /* BEGIN: Use Case */
        $contentTypeService = $repository->getContentTypeService();

        $articleType = $contentTypeService->loadContentTypeByIdentifier('article');
        /* END: Use Case */

        $this->assertInstanceOf(
            ContentType::class,
            $articleType
        );

        return $articleType;
    }

    /**
     * Test for the loadContentTypeByIdentifier() method.
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType $contentType
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentTypeService::loadContentTypeByIdentifier()
     * @depends testLoadContentTypeByIdentifier
     */
    public function testLoadContentTypeByIdentifierReturnsCorrectInstance($contentType)
    {
        $repository = $this->getRepository();
        $contentTypeService = $repository->getContentTypeService();

        $this->assertEquals(
            $contentTypeService->loadContentType($contentType->id),
            $contentType
        );
    }

    /**
     * Test for the loadContentTypeByIdentifier() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentTypeService::loadContentTypeByIdentifier()
     * @depends testLoadContentTypeByIdentifier
     */
    public function testLoadContentTypeByIdentifierThrowsNotFoundException()
    {
        $this->expectException(NotFoundException::class);

        $repository = $this->getRepository();

        /* BEGIN: Use Case */
        $contentTypeService = $repository->getContentTypeService();

        // Throws an exception, since no type with this identifier exists
        $contentTypeService->loadContentTypeByIdentifier('sindelfingen');
        /* END: Use Case */
    }

    /**
     * Test for the loadContentTypeByRemoteId() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentTypeService::loadContentTypeByRemoteId()
     * @depends testLoadContentType
     */
    public function testLoadContentTypeByRemoteId()
    {
        $repository = $this->getRepository();

        /* BEGIN: Use Case */
        $contentTypeService = $repository->getContentTypeService();

        // Loads the standard "user_group" type
        $userGroupType = $contentTypeService->loadContentTypeByRemoteId(
            '25b4268cdcd01921b808a0d854b877ef'
        );
        /* END: Use Case */

        $this->assertInstanceOf(
            ContentType::class,
            $userGroupType
        );

        return $userGroupType;
    }

    /**
     * Test for the loadContentTypeByRemoteId() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentTypeService::loadContentTypeByRemoteId()
     * @depends testLoadContentTypeByRemoteId
     */
    public function testLoadContentTypeByRemoteIdReturnsCorrectInstance($contentType)
    {
        $repository = $this->getRepository();
        $contentTypeService = $repository->getContentTypeService();

        $this->assertEquals(
            $contentTypeService->loadContentType($contentType->id),
            $contentType
        );
    }

    /**
     * Test for the loadContentTypeByRemoteId() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentTypeService::loadContentTypeByRemoteId()
     * @depends testLoadContentType
     */
    public function testLoadContentTypeByRemoteIdThrowsNotFoundException()
    {
        $this->expectException(NotFoundException::class);

        $repository = $this->getRepository();

        /* BEGIN: Use Case */
        $contentTypeService = $repository->getContentTypeService();

        // Throws an exception, since no type with this remote ID exists
        $contentTypeService->loadContentTypeByRemoteId('not-exists');
        /* END: Use Case */
    }

    /**
     * Test for the loadContentTypeList() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentTypeService::loadContentTypeList()
     * @depends testLoadContentType
     */
    public function testLoadContentTypeList()
    {
        $repository = $this->getRepository();
        $contentTypeService = $repository->getContentTypeService();

        $types = $contentTypeService->loadContentTypeList([3, 4]);

        $this->assertIsIterable($types);

        $this->assertEquals(
            [
                3 => $contentTypeService->loadContentType(3),
                4 => $contentTypeService->loadContentType(4),
            ],
            $types
        );
    }

    /**
     * Test for the loadContentTypes() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentTypeService::loadContentTypes()
     * @depends testLoadContentType
     */
    public function testLoadContentTypes()
    {
        $repository = $this->getRepository();

        $typeGroupId = $this->generateId('typegroup', 2);
        /* BEGIN: Use Case */
        // $typeGroupId is a valid ID of a content type group
        $contentTypeService = $repository->getContentTypeService();

        $contentTypeGroup = $contentTypeService->loadContentTypeGroup($typeGroupId);

        // Loads all types from content type group "Users"
        $types = $contentTypeService->loadContentTypes($contentTypeGroup);
        /* END: Use Case */

        $this->assertIsArray($types);

        return $types;
    }

    /**
     * Test for the loadContentTypes() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentTypeService::loadContentTypes()
     * @depends testLoadContentTypes
     */
    public function testLoadContentTypesContent(array $types)
    {
        $repository = $this->getRepository();
        $contentTypeService = $repository->getContentTypeService();

        usort(
            $types,
            static function ($a, $b) {
                if ($a->id == $b->id) {
                    return 0;
                }

                return ($a->id < $b->id) ? -1 : 1;
            }
        );
        $this->assertEquals(
            [
                $contentTypeService->loadContentType($this->generateId('type', 3)),
                $contentTypeService->loadContentType($this->generateId('type', 4)),
            ],
            $types
        );
    }

    /**
     * Test for the createContentTypeDraft() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentTypeService::createContentTypeDraft()
     * @depends testLoadContentType
     */
    public function testCreateContentTypeDraft()
    {
        $repository = $this->getRepository();

        /* BEGIN: Use Case */
        $contentTypeService = $repository->getContentTypeService();

        $commentType = $contentTypeService->loadContentTypeByIdentifier('comment', Language::ALL);

        $commentTypeDraft = $contentTypeService->createContentTypeDraft($commentType);
        /* END: Use Case */

        $this->assertInstanceOf(
            ContentTypeDraft::class,
            $commentTypeDraft
        );

        return [
            'originalType' => $commentType,
            'typeDraft' => $commentTypeDraft,
        ];
    }

    /**
     * Test for the createContentTypeDraft() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentTypeService::createContentTypeDraft()
     * @depends testCreateContentTypeDraft
     */
    public function testCreateContentTypeDraftStructValues(array $data)
    {
        $originalType = $data['originalType'];
        $typeDraft = $data['typeDraft'];

        // Names and descriptions tested in corresponding language test
        $this->assertPropertiesCorrect(
            [
                'id' => $originalType->id,
                'names' => $originalType->names,
                'descriptions' => $originalType->descriptions,
                'identifier' => $originalType->identifier,
                'creatorId' => $originalType->creatorId,
                'modifierId' => $originalType->modifierId,
                'remoteId' => $originalType->remoteId,
                'urlAliasSchema' => $originalType->urlAliasSchema,
                'nameSchema' => $originalType->nameSchema,
                'isContainer' => $originalType->isContainer,
                'mainLanguageCode' => $originalType->mainLanguageCode,
                'defaultAlwaysAvailable' => $originalType->defaultAlwaysAvailable,
                'defaultSortField' => $originalType->defaultSortField,
                'defaultSortOrder' => $originalType->defaultSortOrder,
                'contentTypeGroups' => $originalType->contentTypeGroups,
                'fieldDefinitions' => $originalType->fieldDefinitions,
            ],
            $typeDraft
        );

        $this->assertInstanceOf(
            'DateTime',
            $typeDraft->modificationDate
        );
        $modificationDifference = $originalType->modificationDate->diff(
            $typeDraft->modificationDate
        );
        // No modification date is newer, interval is not inverted
        $this->assertEquals(0, $modificationDifference->invert);

        $this->assertEquals(
            ContentType::STATUS_DRAFT,
            $typeDraft->status
        );

        return $data;
    }

    /**
     * Test for the createContentTypeDraft() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentTypeService::createContentTypeDraft()
     * @depends testCreateContentTypeDraftStructValues
     */
    public function testCreateContentTypeDraftStructLanguageDependentValues(array $data)
    {
        $originalType = $data['originalType'];
        $typeDraft = $data['typeDraft'];

        $this->assertEquals(
            [
                'names' => $originalType->names,
                'descriptions' => $originalType->descriptions,
            ],
            [
                'names' => $typeDraft->names,
                'descriptions' => $typeDraft->descriptions,
            ]
        );
    }

    /**
     * Test for the createContentTypeDraft() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentTypeService::createContentTypeDraft()
     * @depends testCreateContentTypeDraft
     */
    public function testCreateContentTypeDraftThrowsBadStateException()
    {
        $this->expectException(BadStateException::class);

        $repository = $this->getRepository();

        /* BEGIN: Use Case */
        $contentTypeService = $repository->getContentTypeService();

        $commentType = $contentTypeService->loadContentTypeByIdentifier('comment');

        $contentTypeService->createContentTypeDraft($commentType);

        // Throws exception, since type draft already exists
        $contentTypeService->createContentTypeDraft($commentType);
        /* END: Use Case */
    }

    /**
     * Test for the deleteContentType() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentTypeService::deleteContentType()
     * @depends testLoadContentTypeByIdentifier
     */
    public function testDeleteContentType()
    {
        $this->expectException(NotFoundException::class);

        $repository = $this->getRepository();

        /* BEGIN: Use Case */
        $contentTypeService = $repository->getContentTypeService();

        $commentType = $contentTypeService->loadContentTypeByIdentifier('comment');

        $contentTypeService->deleteContentType($commentType);
        /* END: Use Case */

        $contentTypeService->loadContentType($commentType->id);
        $this->fail('Content type could be loaded after delete.');
    }

    /**
     * Test for the deleteContentType() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentTypeService::deleteContentType()
     * @depends testDeleteContentType
     */
    public function testDeleteContentTypeThrowsBadStateException()
    {
        $this->expectException(BadStateException::class);

        $repository = $this->getRepository();

        /* BEGIN: Use Case */
        $contentTypeService = $repository->getContentTypeService();

        $contentType = $contentTypeService->loadContentTypeByIdentifier('user');

        // This call will fail with a "BadStateException" because there is at
        // least on content object of type "user" in an eZ Publish demo
        $contentTypeService->deleteContentType($contentType);
        /* END: Use Case */
    }

    /**
     * Test for the copyContentType() method.
     *
     * @return array
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentTypeService::copyContentType()
     * @depends testLoadContentTypeByIdentifier
     */
    public function testCopyContentType()
    {
        $repository = $this->getRepository();

        /* BEGIN: Use Case */
        $contentTypeService = $repository->getContentTypeService();

        $commentType = $contentTypeService->loadContentTypeByIdentifier('comment');
        $contentTypeGroup = $commentType->contentTypeGroups[0];
        $contentTypes = $contentTypeService->loadContentTypes($contentTypeGroup);
        $contentTypesCount = count($contentTypes);

        // Complete copy of the "comment" type
        $copiedType = $contentTypeService->copyContentType($commentType);

        $contentTypes = $contentTypeService->loadContentTypes($contentTypeGroup);
        $contentTypeIdentifiers = array_map(static function (ContentType $contentType) {
            return $contentType->identifier;
        }, $contentTypes);
        /* END: Use Case */

        $this->assertInstanceOf(
            ContentType::class,
            $copiedType
        );

        $this->assertContains($commentType->identifier, $contentTypeIdentifiers);
        $this->assertContains($copiedType->identifier, $contentTypeIdentifiers);
        $this->assertCount($contentTypesCount + 1, $contentTypes);

        $originalType = $contentTypeService->loadContentTypeByIdentifier('comment');

        return [
            'originalType' => $originalType,
            'copiedType' => $copiedType,
        ];
    }

    /**
     * Test for the copyContentType() method.
     *
     * @param array $data
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentTypeService::copyContentType()
     * @depends testCopyContentType
     */
    public function testCopyContentTypeStructValues(array $data)
    {
        $originalType = $data['originalType'];
        $copiedType = $data['copiedType'];

        $this->assertCopyContentTypeValues($originalType, $copiedType);
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType $originalType
     * @param \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType $copiedType
     * @param array $excludedProperties
     */
    private function assertCopyContentTypeValues($originalType, $copiedType, $excludedProperties = [])
    {
        $allProperties = [
            'names',
            'descriptions',
            'creatorId',
            'modifierId',
            'urlAliasSchema',
            'nameSchema',
            'isContainer',
            'mainLanguageCode',
            'contentTypeGroups',
        ];
        $properties = array_diff($allProperties, $excludedProperties);
        $this->assertStructPropertiesCorrect(
            $originalType,
            $copiedType,
            $properties
        );

        $this->assertNotEquals(
            $originalType->id,
            $copiedType->id
        );
        $this->assertNotEquals(
            $originalType->remoteId,
            $copiedType->remoteId
        );
        $this->assertNotEquals(
            $originalType->identifier,
            $copiedType->identifier
        );
        $this->assertNotEquals(
            $originalType->creationDate,
            $copiedType->creationDate
        );
        $this->assertNotEquals(
            $originalType->modificationDate,
            $copiedType->modificationDate
        );

        foreach ($originalType->fieldDefinitions as $originalFieldDefinition) {
            $copiedFieldDefinition = $copiedType->getFieldDefinition(
                $originalFieldDefinition->identifier
            );

            $this->assertStructPropertiesCorrect(
                $originalFieldDefinition,
                $copiedFieldDefinition,
                [
                    'identifier',
                    'names',
                    'descriptions',
                    'fieldGroup',
                    'position',
                    'fieldTypeIdentifier',
                    'isTranslatable',
                    'isRequired',
                    'isInfoCollector',
                    'validatorConfiguration',
                    'defaultValue',
                    'isSearchable',
                ]
            );
            $this->assertNotEquals(
                $originalFieldDefinition->id,
                $copiedFieldDefinition->id
            );
        }
    }

    /**
     * Test for the copyContentType() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentTypeService::copyContentType($contentType, $user)
     * @depends testCopyContentType
     */
    public function testCopyContentTypeWithSecondParameter()
    {
        $repository = $this->getRepository();

        /* BEGIN: Use Case */
        $contentTypeService = $repository->getContentTypeService();

        $user = $this->createUserVersion1();

        $commentType = $contentTypeService->loadContentTypeByIdentifier('comment');

        // Complete copy of the "comment" type
        $copiedType = $contentTypeService->copyContentType($commentType, $user);
        /* END: Use Case */

        $this->assertPropertiesCorrect(
            [
                'creatorId' => $user->id,
                'modifierId' => $user->id,
            ],
            $copiedType
        );
        $this->assertCopyContentTypeValues($commentType, $copiedType, ['creatorId', 'modifierId']);
    }

    /**
     * Test for the assignContentTypeGroup() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentTypeService::assignContentTypeGroup()
     * @depends testLoadContentTypeGroupByIdentifier
     * @depends testLoadContentTypeByIdentifier
     * @depends testLoadContentType
     */
    public function testAssignContentTypeGroup()
    {
        $repository = $this->getRepository();

        /* BEGIN: Use Case */
        $contentTypeService = $repository->getContentTypeService();

        $mediaGroup = $contentTypeService->loadContentTypeGroupByIdentifier('Media');
        $folderType = $contentTypeService->loadContentTypeByIdentifier('folder');

        $contentTypeService->assignContentTypeGroup($folderType, $mediaGroup);
        /* END: Use Case */

        $loadedType = $contentTypeService->loadContentType($folderType->id);

        foreach ($loadedType->contentTypeGroups as $loadedGroup) {
            if ($mediaGroup->id == $loadedGroup->id) {
                return;
            }
        }
        $this->fail(
            sprintf(
                'Group with ID "%s" not assigned to Content Type.',
                $mediaGroup->id
            )
        );
    }

    /**
     * Test for the assignContentTypeGroup() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentTypeService::assignContentTypeGroup()
     * @depends testAssignContentTypeGroup
     */
    public function testAssignContentTypeGroupThrowsInvalidArgumentException()
    {
        $this->expectException(InvalidArgumentException::class);

        $repository = $this->getRepository();

        /* BEGIN: Use Case */
        $contentTypeService = $repository->getContentTypeService();

        $folderType = $contentTypeService->loadContentTypeByIdentifier('folder');
        $assignedGroups = $folderType->contentTypeGroups;

        foreach ($assignedGroups as $assignedGroup) {
            // Throws an exception, since group is already assigned
            $contentTypeService->assignContentTypeGroup($folderType, $assignedGroup);
        }
        /* END: Use Case */
    }

    /**
     * Test for the unassignContentTypeGroup() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentTypeService::unassignContentTypeGroup()
     * @depends testAssignContentTypeGroup
     */
    public function testUnassignContentTypeGroup()
    {
        $repository = $this->getRepository();

        /* BEGIN: Use Case */
        $contentTypeService = $repository->getContentTypeService();

        $folderType = $contentTypeService->loadContentTypeByIdentifier('folder');

        $mediaGroup = $contentTypeService->loadContentTypeGroupByIdentifier('Media');
        $contentGroup = $contentTypeService->loadContentTypeGroupByIdentifier('Content');

        // May not unassign last group
        $contentTypeService->assignContentTypeGroup($folderType, $mediaGroup);

        $contentTypeService->unassignContentTypeGroup($folderType, $contentGroup);
        /* END: Use Case */

        $loadedType = $contentTypeService->loadContentType($folderType->id);

        foreach ($loadedType->contentTypeGroups as $assignedGroup) {
            if ($assignedGroup->id == $contentGroup->id) {
                $this->fail(
                    sprintf(
                        'Could not unassign group with ID "%s".',
                        $assignedGroup->id
                    )
                );
            }
        }
    }

    /**
     * Test for the unassignContentTypeGroup() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentTypeService::unassignContentTypeGroup()
     * @depends testUnassignContentTypeGroup
     */
    public function testUnassignContentTypeGroupThrowsInvalidArgumentException()
    {
        $this->expectException(InvalidArgumentException::class);

        $repository = $this->getRepository();

        /* BEGIN: Use Case */
        $contentTypeService = $repository->getContentTypeService();

        $folderType = $contentTypeService->loadContentTypeByIdentifier('folder');
        $notAssignedGroup = $contentTypeService->loadContentTypeGroupByIdentifier('Media');

        // Throws an exception, since "Media" group is not assigned to "folder"
        $contentTypeService->unassignContentTypeGroup($folderType, $notAssignedGroup);
        /* END: Use Case */
    }

    /**
     * Test for the unassignContentTypeGroup() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentTypeService::unassignContentTypeGroup()
     * @depends testUnassignContentTypeGroup
     */
    public function testUnassignContentTypeGroupThrowsBadStateException()
    {
        $this->expectException(BadStateException::class);

        $repository = $this->getRepository();

        /* BEGIN: Use Case */
        $contentTypeService = $repository->getContentTypeService();

        $folderType = $contentTypeService->loadContentTypeByIdentifier('folder');
        $assignedGroups = $folderType->contentTypeGroups;

        foreach ($assignedGroups as $assignedGroup) {
            // Throws an exception, when last group is to be removed
            $contentTypeService->unassignContentTypeGroup($folderType, $assignedGroup);
        }
        /* END: Use Case */
    }

    /**
     * Test for the createContentTypeGroup() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentTypeService::createContentTypeGroup()
     * @depends testLoadContentTypeGroup
     * @depends testCreateContentTypeGroup
     */
    public function testCreateContentTypeGroupInTransactionWithRollback()
    {
        $repository = $this->getRepository();

        /* BEGIN: Use Case */
        $contentTypeService = $repository->getContentTypeService();

        // Get create struct and set language property
        $groupCreate = $contentTypeService->newContentTypeGroupCreateStruct('new-group');
        /* @todo uncomment when support for multilingual names and descriptions is added
        $groupCreate->mainLanguageCode = 'eng-GB';
        */

        // Start a new transaction
        $repository->beginTransaction();

        try {
            // Create the new content type group
            $groupId = $contentTypeService->createContentTypeGroup($groupCreate)->id;
        } catch (Exception $e) {
            // Cleanup hanging transaction on error
            $repository->rollback();
            throw $e;
        }

        // Rollback all changes
        $repository->rollback();

        try {
            // This call will fail with a "NotFoundException"
            $contentTypeService->loadContentTypeGroup($groupId);
        } catch (NotFoundException $e) {
            return;
        }
        /* END: Use Case */

        $this->fail('Can still load content type group after rollback');
    }

    /**
     * Test for the createContentTypeGroup() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentTypeService::createContentTypeGroup()
     * @depends testLoadContentTypeGroup
     * @depends testCreateContentTypeGroup
     */
    public function testCreateContentTypeGroupInTransactionWithCommit()
    {
        $repository = $this->getRepository();

        /* BEGIN: Use Case */
        $contentTypeService = $repository->getContentTypeService();

        // Get create struct and set language property
        $groupCreate = $contentTypeService->newContentTypeGroupCreateStruct('new-group');
        /* @todo uncomment when support for multilingual names and descriptions is added
        $groupCreate->mainLanguageCode = 'eng-GB';
        */

        // Start a new transaction
        $repository->beginTransaction();

        try {
            // Create the new content type group
            $groupId = $contentTypeService->createContentTypeGroup($groupCreate)->id;

            // Rollback all changes
            $repository->commit();
        } catch (Exception $e) {
            // Cleanup hanging transaction on error
            $repository->rollback();
            throw $e;
        }

        // Load created content type group
        $group = $contentTypeService->loadContentTypeGroup($groupId);
        /* END: Use Case */

        $this->assertEquals($groupId, $group->id);
    }

    /**
     * Test for the updateContentTypeGroup() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentTypeService::updateContentTypeGroup()
     * @depends testUpdateContentTypeGroup
     * @depends testLoadContentTypeGroupByIdentifier
     */
    public function testUpdateContentTypeGroupInTransactionWithRollback()
    {
        $repository = $this->getRepository();

        /* BEGIN: Use Case */
        $contentTypeService = $repository->getContentTypeService();

        // Load an existing group
        $group = $contentTypeService->loadContentTypeGroupByIdentifier('Setup');

        // Get an update struct and change the identifier
        $groupUpdate = $contentTypeService->newContentTypeGroupUpdateStruct();
        $groupUpdate->identifier = 'Teardown';

        // Start a new transaction
        $repository->beginTransaction();

        try {
            // Apply update to group
            $contentTypeService->updateContentTypeGroup($group, $groupUpdate);
        } catch (Exception $e) {
            // Cleanup hanging transaction on error
            $repository->rollback();
            throw $e;
        }

        // Rollback all changes
        $repository->rollback();

        // Load updated group, it will be unchanged
        $updatedGroup = $contentTypeService->loadContentTypeGroupByIdentifier('Setup');
        /* END: Use Case */

        $this->assertEquals('Setup', $updatedGroup->identifier);
    }

    /**
     * Test for the updateContentTypeGroup() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentTypeService::updateContentTypeGroup()
     * @depends testUpdateContentTypeGroup
     * @depends testLoadContentTypeGroupByIdentifier
     */
    public function testUpdateContentTypeGroupInTransactionWithCommit()
    {
        $repository = $this->getRepository();

        /* BEGIN: Use Case */
        $contentTypeService = $repository->getContentTypeService();

        // Load an existing group
        $group = $contentTypeService->loadContentTypeGroupByIdentifier('Setup');

        // Get an update struct and change the identifier
        $groupUpdate = $contentTypeService->newContentTypeGroupUpdateStruct();
        $groupUpdate->identifier = 'Teardown';

        // Start a new transaction
        $repository->beginTransaction();

        try {
            // Apply update to group
            $contentTypeService->updateContentTypeGroup($group, $groupUpdate);

            // Commit all changes
            $repository->commit();
        } catch (Exception $e) {
            // Cleanup hanging transaction on error
            $repository->rollback();
            throw $e;
        }

        // Load updated group by it's new identifier "Teardown"
        $updatedGroup = $contentTypeService->loadContentTypeGroupByIdentifier(
            'Teardown'
        );
        /* END: Use Case */

        $this->assertEquals('Teardown', $updatedGroup->identifier);
    }

    /**
     * Test for the deleteContentTypeGroup() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentTypeService::deleteContentTypeGroup()
     * @depends testDeleteContentTypeGroup
     * @depends testLoadContentTypeGroupByIdentifierThrowsNotFoundException
     */
    public function testDeleteContentTypeGroupWithRollback()
    {
        $repository = $this->getRepository();

        /* BEGIN: Use Case */
        $contentTypeService = $repository->getContentTypeService();

        // Get a group create struct
        $groupCreate = $contentTypeService->newContentTypeGroupCreateStruct(
            'new-group'
        );

        // Start a new transaction
        $repository->beginTransaction();

        try {
            // Create the new group
            $group = $contentTypeService->createContentTypeGroup($groupCreate);

            // Delete the currently created group
            $contentTypeService->deleteContentTypeGroup($group);
        } catch (Exception $e) {
            // Cleanup hanging transaction on error
            $repository->rollback();
            throw $e;
        }

        // Rollback all changes
        $repository->rollback();

        try {
            // This call will fail with an "NotFoundException"
            $contentTypeService->loadContentTypeGroupByIdentifier('new-group');
        } catch (NotFoundException $e) {
            // Expected error path
        }
        /* END: Use Case */

        $this->assertTrue(isset($e), 'Group not deleted after rollback');
    }

    /**
     * Test for the deleteContentTypeGroup() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentTypeService::deleteContentTypeGroup()
     * @depends testDeleteContentTypeGroup
     * @depends testLoadContentTypeGroupByIdentifierThrowsNotFoundException
     */
    public function testDeleteContentTypeGroupWithCommit()
    {
        $repository = $this->getRepository();

        /* BEGIN: Use Case */
        $contentTypeService = $repository->getContentTypeService();

        // Get a group create struct
        $groupCreate = $contentTypeService->newContentTypeGroupCreateStruct(
            'new-group'
        );

        // Start a new transaction
        $repository->beginTransaction();

        try {
            // Create the new group
            $group = $contentTypeService->createContentTypeGroup($groupCreate);

            // Delete the currently created group
            $contentTypeService->deleteContentTypeGroup($group);

            // Commit all changes
            $repository->commit();
        } catch (Exception $e) {
            // Cleanup hanging transaction on error
            $repository->rollback();
            throw $e;
        }

        try {
            // This call will fail with an "NotFoundException"
            $contentTypeService->loadContentTypeGroupByIdentifier('new-group');
        } catch (NotFoundException $e) {
            // Expected error path
        }
        /* END: Use Case */

        $this->assertTrue(isset($e), 'Group not deleted after commit.');
    }

    /**
     * Test for the createContentType() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentTypeService::createContentType()
     * @depends testCreateContentType
     * @depends testLoadContentTypeByIdentifierThrowsNotFoundException
     */
    public function testCreateContentTypeInTransactionWithRollback()
    {
        $repository = $this->getRepository();

        /* BEGIN: Use Case */
        $contentTypeService = $repository->getContentTypeService();

        // Start a new transaction
        $repository->beginTransaction();

        try {
            // Get create struct and set some properties
            $typeCreate = $contentTypeService->newContentTypeCreateStruct('blog-post');
            $typeCreate->mainLanguageCode = 'eng-GB';
            $typeCreate->names = ['eng-GB' => 'Blog post'];

            $titleFieldCreate = $contentTypeService->newFieldDefinitionCreateStruct('title', 'ezstring');
            $titleFieldCreate->names = ['eng-GB' => 'Title'];
            $titleFieldCreate->position = 1;
            $typeCreate->addFieldDefinition($titleFieldCreate);

            $groups = [
                $contentTypeService->loadContentTypeGroupByIdentifier('Setup'),
            ];

            // Create content type
            $contentTypeDraft = $contentTypeService->createContentType(
                $typeCreate,
                $groups
            );

            // Publish the content type draft
            $contentTypeService->publishContentTypeDraft($contentTypeDraft);
        } catch (Exception $e) {
            // Cleanup hanging transaction on error
            $repository->rollback();
            throw $e;
        }

        // Rollback all changes.
        $repository->rollback();

        try {
            // This call will fail with a "NotFoundException"
            $contentTypeService->loadContentTypeByIdentifier('blog-post');
        } catch (NotFoundException $e) {
            // Expected execution path
        }
        /* END: Use Case */

        $this->assertTrue(isset($e), 'Can still load content type after rollback.');
    }

    /**
     * Test for the createContentType() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentTypeService::createContentType()
     * @depends testCreateContentType
     * @depends testLoadContentTypeByIdentifierThrowsNotFoundException
     */
    public function testCreateContentTypeInTransactionWithCommit()
    {
        $repository = $this->getRepository();

        /* BEGIN: Use Case */
        $contentTypeService = $repository->getContentTypeService();

        // Start a new transaction
        $repository->beginTransaction();

        try {
            // Get create struct and set some properties
            $typeCreate = $contentTypeService->newContentTypeCreateStruct('blog-post');
            $typeCreate->mainLanguageCode = 'eng-GB';
            $typeCreate->names = ['eng-GB' => 'Blog post'];

            $titleFieldCreate = $contentTypeService->newFieldDefinitionCreateStruct('title', 'ezstring');
            $titleFieldCreate->names = ['eng-GB' => 'Title'];
            $titleFieldCreate->position = 1;
            $typeCreate->addFieldDefinition($titleFieldCreate);

            $groups = [
                $contentTypeService->loadContentTypeGroupByIdentifier('Setup'),
            ];

            // Create content type
            $contentTypeDraft = $contentTypeService->createContentType(
                $typeCreate,
                $groups
            );

            // Publish the content type draft
            $contentTypeService->publishContentTypeDraft($contentTypeDraft);

            // Commit all changes.
            $repository->commit();
        } catch (Exception $e) {
            // Cleanup hanging transaction on error
            $repository->rollback();
            throw $e;
        }

        // Load the newly created content type
        $contentType = $contentTypeService->loadContentTypeByIdentifier('blog-post');
        /* END: Use Case */

        $this->assertEquals($contentTypeDraft->id, $contentType->id);
    }

    /**
     * Test for the copyContentType() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentTypeService::copyContentType()
     * @depends testCopyContentType
     * @depends testLoadContentTypeByIdentifier
     * @depends testLoadContentTypeThrowsNotFoundException
     */
    public function testCopyContentTypeInTransactionWithRollback()
    {
        $repository = $this->getRepository();

        /* BEGIN: Use Case */
        $contentTypeService = $repository->getContentTypeService();

        // Load content type to copy
        $contentType = $contentTypeService->loadContentTypeByIdentifier('comment');

        // Start a new transaction
        $repository->beginTransaction();

        try {
            // Complete copy of the content type
            $copiedType = $contentTypeService->copyContentType($contentType);
        } catch (Exception $e) {
            // Cleanup hanging transaction on error
            $repository->rollback();
            throw $e;
        }

        // Rollback all changes
        $repository->rollback();

        try {
            // This call will fail with a "NotFoundException"
            $contentTypeService->loadContentType($copiedType->id);
        } catch (NotFoundException $e) {
            // Expected execution path
        }
        /* END: Use Case */

        $this->assertTrue(isset($e), 'Can still load copied content type after rollback.');
    }

    /**
     * Test for the copyContentType() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentTypeService::copyContentType()
     * @depends testCopyContentType
     * @depends testLoadContentTypeByIdentifier
     * @depends testLoadContentTypeThrowsNotFoundException
     */
    public function testCopyContentTypeInTransactionWithCommit()
    {
        $repository = $this->getRepository();

        /* BEGIN: Use Case */
        $contentTypeService = $repository->getContentTypeService();

        // Load content type to copy
        $contentType = $contentTypeService->loadContentTypeByIdentifier('comment');

        // Start a new transaction
        $repository->beginTransaction();

        try {
            // Complete copy of the content type
            $contentTypeId = $contentTypeService->copyContentType($contentType)->id;

            // Commit all changes
            $repository->commit();
        } catch (Exception $e) {
            // Cleanup hanging transaction on error
            $repository->rollback();
            throw $e;
        }

        // Load the new content type copy.
        $copiedContentType = $contentTypeService->loadContentType($contentTypeId);
        /* END: Use Case */

        $this->assertEquals($contentTypeId, $copiedContentType->id);
    }

    /**
     * Test for the deleteContentType() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentTypeService::deleteContentType()
     * @depends testCopyContentType
     * @depends testLoadContentTypeByIdentifierThrowsNotFoundException
     */
    public function testDeleteContentTypeInTransactionWithRollback()
    {
        $repository = $this->getRepository();

        /* BEGIN: Use Case */
        $contentTypeService = $repository->getContentTypeService();

        // Load content type to copy
        $contentType = $contentTypeService->loadContentTypeByIdentifier('comment');

        // Start a new transaction
        $repository->beginTransaction();

        try {
            // Delete the "comment" content type.
            $contentTypeService->deleteContentType($contentType);
        } catch (Exception $e) {
            // Cleanup hanging transaction on error
            $repository->rollback();
            throw $e;
        }

        // Rollback all changes
        $repository->rollback();

        // Load currently deleted and rollbacked content type
        $commentType = $contentTypeService->loadContentTypeByIdentifier('comment');
        /* END: Use Case */

        $this->assertEquals('comment', $commentType->identifier);
    }

    /**
     * Test for the deleteContentType() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentTypeService::deleteContentType()
     * @depends testCopyContentType
     * @depends testLoadContentTypeByIdentifierThrowsNotFoundException
     */
    public function testDeleteContentTypeInTransactionWithCommit()
    {
        $repository = $this->getRepository();

        /* BEGIN: Use Case */
        $contentTypeService = $repository->getContentTypeService();

        // Load content type to copy
        $contentType = $contentTypeService->loadContentTypeByIdentifier('comment');

        // Start a new transaction
        $repository->beginTransaction();

        try {
            // Delete the "comment" content type.
            $contentTypeService->deleteContentType($contentType);

            // Commit all changes
            $repository->commit();
        } catch (Exception $e) {
            // Cleanup hanging transaction on error
            $repository->rollback();
            throw $e;
        }

        try {
            // This call will fail with a "NotFoundException"
            $contentTypeService->loadContentTypeByIdentifier('comment');
        } catch (NotFoundException $e) {
            // Expected execution path
        }
        /* END: Use Case */

        $this->assertTrue(isset($e), 'Can still load content type after rollback.');
    }

    /**
     * Test for the assignContentTypeGroup() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentTypeService::assignContentTypeGroup()
     * @depends testAssignContentTypeGroup
     */
    public function testAssignContentTypeGroupInTransactionWithRollback()
    {
        $repository = $this->getRepository();

        /* BEGIN: Use Case */
        $contentTypeService = $repository->getContentTypeService();

        $mediaGroup = $contentTypeService->loadContentTypeGroupByIdentifier('Media');
        $folderType = $contentTypeService->loadContentTypeByIdentifier('folder');

        // Start a new transaction
        $repository->beginTransaction();

        try {
            // Assign group to content type
            $contentTypeService->assignContentTypeGroup($folderType, $mediaGroup);
        } catch (Exception $e) {
            // Cleanup hanging transaction on error
            $repository->rollback();
            throw $e;
        }

        // Rollback all changes
        $repository->rollback();

        // Load all content types assigned to media group
        $contentTypes = $contentTypeService->loadContentTypes($mediaGroup);

        $contentTypeIds = [];
        foreach ($contentTypes as $contentType) {
            $contentTypeIds[] = $contentType->id;
        }
        /* END: Use Case */

        $this->assertFalse(
            in_array($folderType->id, $contentTypeIds),
            'Folder content type is still in media group after rollback.'
        );
    }

    /**
     * Test for the assignContentTypeGroup() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentTypeService::assignContentTypeGroup()
     * @depends testAssignContentTypeGroup
     */
    public function testAssignContentTypeGroupInTransactionWithCommit()
    {
        $repository = $this->getRepository();

        /* BEGIN: Use Case */
        $contentTypeService = $repository->getContentTypeService();

        $mediaGroup = $contentTypeService->loadContentTypeGroupByIdentifier('Media');
        $folderType = $contentTypeService->loadContentTypeByIdentifier('folder');

        // Start a new transaction
        $repository->beginTransaction();

        try {
            // Assign group to content type
            $contentTypeService->assignContentTypeGroup($folderType, $mediaGroup);

            // Commit all changes
            $repository->commit();
        } catch (Exception $e) {
            // Cleanup hanging transaction on error
            $repository->rollback();
            throw $e;
        }

        // Load all content types assigned to media group
        $contentTypes = $contentTypeService->loadContentTypes($mediaGroup);

        $contentTypeIds = [];
        foreach ($contentTypes as $contentType) {
            $contentTypeIds[] = $contentType->id;
        }
        /* END: Use Case */

        $this->assertTrue(
            in_array($folderType->id, $contentTypeIds),
            'Folder content type not in media group after commit.'
        );
    }

    /**
     * Test for the isContentTypeUsed() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentTypeService::isContentTypeUsed()
     */
    public function testIsContentTypeUsed()
    {
        $repository = $this->getRepository();

        /* BEGIN: Use Case */
        $contentTypeService = $repository->getContentTypeService();

        $folderType = $contentTypeService->loadContentTypeByIdentifier('folder');
        $eventType = $contentTypeService->loadContentTypeByIdentifier('event');

        $isFolderUsed = $contentTypeService->isContentTypeUsed($folderType);
        $isEventUsed = $contentTypeService->isContentTypeUsed($eventType);
        /* END: Use Case */

        $this->assertTrue($isFolderUsed);
        $this->assertFalse($isEventUsed);
    }

    /**
     * @covers \Ibexa\Contracts\Core\Repository\ContentTypeService::removeContentTypeTranslation
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\BadStateException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function testRemoveContentTypeTranslation()
    {
        $repository = $this->getRepository();
        $contentTypeService = $repository->getContentTypeService();

        $contentTypeDraft = $this->createContentTypeDraft();
        $contentTypeService->publishContentTypeDraft($contentTypeDraft);

        $contentType = $contentTypeService->loadContentType($contentTypeDraft->id);

        $this->assertEquals(
            [
                'eng-US' => 'Blog post',
                'ger-DE' => 'Blog-Eintrag',
            ],
            $contentType->getNames()
        );

        $contentTypeService->removeContentTypeTranslation(
            $contentTypeService->createContentTypeDraft($contentType),
            'ger-DE'
        );

        $loadedContentTypeDraft = $contentTypeService->loadContentTypeDraft($contentType->id);

        $this->assertArrayNotHasKey('ger-DE', $loadedContentTypeDraft->getNames());
        $this->assertArrayNotHasKey('ger-DE', $loadedContentTypeDraft->getDescriptions());

        foreach ($loadedContentTypeDraft->fieldDefinitions as $fieldDefinition) {
            $this->assertArrayNotHasKey('ger-DE', $fieldDefinition->getNames());
            $this->assertArrayNotHasKey('ger-DE', $fieldDefinition->getDescriptions());
        }
    }

    /**
     * @covers \Ibexa\Contracts\Core\Repository\ContentTypeService::removeContentTypeTranslation
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\BadStateException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function testRemoveContentTypeTranslationWithMultilingualData()
    {
        $repository = $this->getRepository();
        $contentTypeService = $repository->getContentTypeService();

        $selectionFieldCreate = $contentTypeService->newFieldDefinitionCreateStruct('selection', 'ezselection');

        $selectionFieldCreate->names = [
            'eng-US' => 'Selection',
            'ger-DE' => 'GER Selection',
        ];

        $selectionFieldCreate->fieldGroup = 'blog-content';
        $selectionFieldCreate->position = 3;
        $selectionFieldCreate->isTranslatable = true;
        $selectionFieldCreate->isRequired = true;
        $selectionFieldCreate->isInfoCollector = false;
        $selectionFieldCreate->validatorConfiguration = [];
        $selectionFieldCreate->fieldSettings = [
            'multilingualOptions' => [
                'eng-US' => [
                    0 => 'A first',
                    1 => 'Bielefeld',
                    2 => 'Sindelfingen',
                    3 => 'Turtles',
                    4 => 'Zombies',
                ],
                'ger-DE' => [
                    0 => 'Berlin',
                    1 => 'Cologne',
                    2 => 'Bonn',
                    3 => 'Frankfurt',
                    4 => 'Hamburg',
                ],
            ],
        ];
        $selectionFieldCreate->isSearchable = false;

        $contentTypeDraft = $this->createContentTypeDraft([$selectionFieldCreate]);

        $contentTypeService->publishContentTypeDraft($contentTypeDraft);

        $contentType = $contentTypeService->loadContentType($contentTypeDraft->id);

        $contentTypeService->removeContentTypeTranslation(
            $contentTypeService->createContentTypeDraft($contentType),
            'ger-DE'
        );

        $loadedContentTypeDraft = $contentTypeService->loadContentTypeDraft($contentType->id);

        $fieldDefinition = $loadedContentTypeDraft->getFieldDefinition('selection');
        $this->assertArrayNotHasKey('ger-DE', $fieldDefinition->fieldSettings['multilingualOptions']);
    }

    /**
     * @covers \Ibexa\Contracts\Core\Repository\ContentTypeService::updateContentTypeDraft
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\ForbiddenException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function testUpdateContentTypeDraftWithNewTranslationWithMultilingualData()
    {
        $repository = $this->getRepository();
        $contentTypeService = $repository->getContentTypeService();

        $selectionFieldCreate = $contentTypeService->newFieldDefinitionCreateStruct('selection', 'ezselection');

        $selectionFieldCreate->names = [
            'eng-US' => 'Selection',
            'ger-DE' => 'GER Selection',
        ];

        $selectionFieldCreate->fieldGroup = 'blog-content';
        $selectionFieldCreate->position = 3;
        $selectionFieldCreate->isTranslatable = true;
        $selectionFieldCreate->isRequired = true;
        $selectionFieldCreate->isInfoCollector = false;
        $selectionFieldCreate->validatorConfiguration = [];
        $selectionFieldCreate->fieldSettings = [
            'multilingualOptions' => [
                'eng-US' => [
                    0 => 'A first',
                    1 => 'Bielefeld',
                    2 => 'Sindelfingen',
                    3 => 'Turtles',
                    4 => 'Zombies',
                ],
                'ger-DE' => [
                    0 => 'Berlin',
                    1 => 'Cologne',
                    2 => 'Bonn',
                    3 => 'Frankfurt',
                    4 => 'Hamburg',
                ],
            ],
        ];
        $selectionFieldCreate->isSearchable = false;

        $contentTypeDraft = $this->createContentTypeDraft([$selectionFieldCreate]);
        $contentTypeService->publishContentTypeDraft($contentTypeDraft);

        $contentType = $contentTypeService->loadContentType($contentTypeDraft->id);
        // sanity check
        self::assertEquals(
            ['eng-US', 'ger-DE'],
            array_keys($contentType->getNames())
        );

        $contentTypeDraft = $contentTypeService->createContentTypeDraft($contentType);
        $updateStruct = $contentTypeService->newContentTypeUpdateStruct();
        $updateStruct->names = [
            'eng-GB' => 'BrE blog post',
        ];

        $selectionFieldUpdate = $contentTypeService->newFieldDefinitionUpdateStruct();

        $selectionFieldUpdate->names = [
            'eng-GB' => 'GB Selection',
        ];

        $selectionFieldUpdate->fieldGroup = 'blog-content';
        $selectionFieldUpdate->position = 3;
        $selectionFieldUpdate->isTranslatable = true;
        $selectionFieldUpdate->isRequired = true;
        $selectionFieldUpdate->isInfoCollector = false;
        $selectionFieldUpdate->validatorConfiguration = [];
        $selectionFieldUpdate->fieldSettings = [
            'multilingualOptions' => [
                'eng-US' => [
                    0 => 'A first',
                    1 => 'Bielefeld',
                    2 => 'Sindelfingen',
                    3 => 'Turtles',
                    4 => 'Zombies',
                ],
                'ger-DE' => [
                    0 => 'Berlin',
                    1 => 'Cologne',
                    2 => 'Bonn',
                    3 => 'Frankfurt',
                    4 => 'Hamburg',
                ],
                'eng-GB' => [
                    0 => 'London',
                    1 => 'Liverpool',
                ],
            ],
        ];
        $selectionFieldUpdate->isSearchable = false;

        $contentTypeService->updateFieldDefinition(
            $contentTypeDraft,
            $contentType->getFieldDefinition('selection'),
            $selectionFieldUpdate
        );
        $contentTypeService->updateContentTypeDraft($contentTypeDraft, $updateStruct);
        $contentTypeService->publishContentTypeDraft($contentTypeDraft);

        $loadedFieldDefinition = $contentTypeService->loadContentType($contentType->id)->getFieldDefinition('selection');
        self::assertEquals(
            [
                'eng-US' => [
                    0 => 'A first',
                    1 => 'Bielefeld',
                    2 => 'Sindelfingen',
                    3 => 'Turtles',
                    4 => 'Zombies',
                ],
                'ger-DE' => [
                    0 => 'Berlin',
                    1 => 'Cologne',
                    2 => 'Bonn',
                    3 => 'Frankfurt',
                    4 => 'Hamburg',
                ],
                'eng-GB' => [
                    0 => 'London',
                    1 => 'Liverpool',
                ],
            ],
            $loadedFieldDefinition->fieldSettings['multilingualOptions']
        );
    }

    /**
     * Test for the deleteUserDrafts() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentTypeService::deleteUserDrafts()
     */
    public function testDeleteUserDrafts()
    {
        $this->expectException(NotFoundException::class);

        $repository = $this->getRepository();
        $userService = $repository->getUserService();
        $permissionResolver = $repository->getPermissionResolver();
        $contentTypeService = $repository->getContentTypeService();

        $draft = $this->createContentTypeDraft();
        $user = $permissionResolver->getCurrentUserReference();

        $contentTypeService->deleteUserDrafts($user->getUserId());
        $contentTypeDraft = $contentTypeService->loadContentTypeDraft($draft->id);
    }
}

class_alias(ContentTypeServiceTest::class, 'eZ\Publish\API\Repository\Tests\ContentTypeServiceTest');
