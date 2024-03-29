<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace eZ\Publish\Core\Persistence\Legacy\Tests\Content\Type;

use eZ\Publish\Core\Persistence\Legacy\Content\StorageFieldDefinition;
use eZ\Publish\Core\Persistence\Legacy\Content\Type\Gateway;
use eZ\Publish\Core\Persistence\Legacy\Content\Type\Handler;
use eZ\Publish\Core\Persistence\Legacy\Content\Type\Mapper;
use eZ\Publish\Core\Persistence\Legacy\Content\Type\Update\Handler as UpdateHandler;
use eZ\Publish\Core\Persistence\Legacy\Exception;
use eZ\Publish\SPI\Persistence\Content\Type;
use eZ\Publish\SPI\Persistence\Content\Type\CreateStruct;
use eZ\Publish\SPI\Persistence\Content\Type\FieldDefinition;
use eZ\Publish\SPI\Persistence\Content\Type\Group;
use eZ\Publish\SPI\Persistence\Content\Type\Group\CreateStruct as GroupCreateStruct;
use eZ\Publish\SPI\Persistence\Content\Type\Group\UpdateStruct as GroupUpdateStruct;
use eZ\Publish\SPI\Persistence\Content\Type\UpdateStruct;
use PHPUnit\Framework\TestCase;

/**
 * Test case for Content Type Handler.
 */
class ContentTypeHandlerTest extends TestCase
{
    /**
     * Gateway mock.
     *
     * @var \eZ\Publish\Core\Persistence\Legacy\Content\Type\Gateway
     */
    protected $gatewayMock;

    /**
     * Mapper mock.
     *
     * @var \eZ\Publish\Core\Persistence\Legacy\Content\Type\Mapper
     */
    protected $mapperMock;

    /**
     * Update\Handler mock.
     *
     * @var \eZ\Publish\Core\Persistence\Legacy\Content\Type\Update\Handler
     */
    protected $updateHandlerMock;

    /**
     * @covers \eZ\Publish\Core\Persistence\Legacy\Content\Type\Handler::createGroup
     */
    public function testCreateGroup()
    {
        $createStruct = new GroupCreateStruct();

        $mapperMock = $this->getMapperMock();
        $mapperMock->expects($this->once())
            ->method('createGroupFromCreateStruct')
            ->with(
                $this->isInstanceOf(
                    GroupCreateStruct::class
                )
            )
            ->will(
                $this->returnValue(new Group())
            );

        $gatewayMock = $this->getGatewayMock();
        $gatewayMock->expects($this->once())
            ->method('insertGroup')
            ->with(
                $this->isInstanceOf(
                    Group::class
                )
            )
            ->will($this->returnValue(23));

        $handler = $this->getHandler();
        $group = $handler->createGroup(
            new GroupCreateStruct()
        );

        $this->assertInstanceOf(
            Group::class,
            $group
        );
        $this->assertEquals(
            23,
            $group->id
        );
    }

    /**
     * @covers \eZ\Publish\Core\Persistence\Legacy\Content\Type\Handler::updateGroup
     */
    public function testUpdateGroup()
    {
        $updateStruct = new GroupUpdateStruct();
        $updateStruct->id = 23;

        $mapperMock = $this->getMapperMock();

        $gatewayMock = $this->getGatewayMock();
        $gatewayMock->expects($this->once())
            ->method('updateGroup')
            ->with(
                $this->isInstanceOf(
                    GroupUpdateStruct::class
                )
            );

        $handlerMock = $this->getMockBuilder(Handler::class)
            ->setMethods(['loadGroup'])
            ->setConstructorArgs([$gatewayMock, $mapperMock, $this->getUpdateHandlerMock()])
            ->getMock();

        $handlerMock->expects($this->once())
            ->method('loadGroup')
            ->with(
                $this->equalTo(23)
            )->will(
                $this->returnValue(new Group())
            );

        $res = $handlerMock->updateGroup(
            $updateStruct
        );

        $this->assertInstanceOf(
            Group::class,
            $res
        );
    }

    /**
     * @covers \eZ\Publish\Core\Persistence\Legacy\Content\Type\Handler::deleteGroup
     */
    public function testDeleteGroupSuccess()
    {
        $gatewayMock = $this->getGatewayMock();
        $gatewayMock->expects($this->once())
            ->method('countTypesInGroup')
            ->with($this->equalTo(23))
            ->will($this->returnValue(0));
        $gatewayMock->expects($this->once())
            ->method('deleteGroup')
            ->with($this->equalTo(23));

        $handler = $this->getHandler();
        $handler->deleteGroup(23);
    }

    /**
     * @covers \eZ\Publish\Core\Persistence\Legacy\Content\Type\Handler::deleteGroup
     * @covers \eZ\Publish\Core\Persistence\Legacy\Exception\GroupNotEmpty
     */
    public function testDeleteGroupFailure()
    {
        $this->expectException(\eZ\Publish\Core\Persistence\Legacy\Exception\GroupNotEmpty::class);
        $this->expectExceptionMessage('Group with ID "23" is not empty.');

        $gatewayMock = $this->getGatewayMock();
        $gatewayMock->expects($this->once())
            ->method('countTypesInGroup')
            ->with($this->equalTo(23))
            ->will($this->returnValue(42));
        $gatewayMock->expects($this->never())
            ->method('deleteGroup');

        $handler = $this->getHandler();
        $handler->deleteGroup(23);
    }

    /**
     * @covers \eZ\Publish\Core\Persistence\Legacy\Content\Type\Handler::loadGroup
     */
    public function testLoadGroup()
    {
        $gatewayMock = $this->getGatewayMock();
        $gatewayMock->expects($this->once())
            ->method('loadGroupData')
            ->with($this->equalTo([23]))
            ->will($this->returnValue([]));

        $mapperMock = $this->getMapperMock();
        $mapperMock->expects($this->once())
            ->method('extractGroupsFromRows')
            ->with($this->equalTo([]))
            ->will($this->returnValue([new Group()]));

        $handler = $this->getHandler();
        $res = $handler->loadGroup(23);

        $this->assertEquals(
            new Group(),
            $res
        );
    }

    /**
     * @covers \eZ\Publish\Core\Persistence\Legacy\Content\Type\Handler::loadGroupByIdentifier
     */
    public function testLoadGroupByIdentifier()
    {
        $gatewayMock = $this->getGatewayMock();
        $gatewayMock->expects($this->once())
            ->method('loadGroupDataByIdentifier')
            ->with($this->equalTo('content'))
            ->will($this->returnValue([]));

        $mapperMock = $this->getMapperMock();
        $mapperMock->expects($this->once())
            ->method('extractGroupsFromRows')
            ->with($this->equalTo([]))
            ->will($this->returnValue([new Group()]));

        $handler = $this->getHandler();
        $res = $handler->loadGroupByIdentifier('content');

        $this->assertEquals(
            new Group(),
            $res
        );
    }

    /**
     * @covers \eZ\Publish\Core\Persistence\Legacy\Content\Type\Handler::loadAllGroups
     */
    public function testLoadAllGroups()
    {
        $gatewayMock = $this->getGatewayMock();
        $gatewayMock->expects($this->once())
            ->method('loadAllGroupsData')
            ->will($this->returnValue([]));

        $mapperMock = $this->getMapperMock();
        $mapperMock->expects($this->once())
            ->method('extractGroupsFromRows')
            ->with($this->equalTo([]))
            ->will($this->returnValue([new Group()]));

        $handler = $this->getHandler();
        $res = $handler->loadAllGroups();

        $this->assertEquals(
            [new Group()],
            $res
        );
    }

    /**
     * @covers \eZ\Publish\Core\Persistence\Legacy\Content\Type\Handler::loadContentTypes
     */
    public function testLoadContentTypes()
    {
        $gatewayMock = $this->getGatewayMock();
        $gatewayMock->expects($this->once())
            ->method('loadTypesDataForGroup')
            ->with($this->equalTo(23), $this->equalTo(0))
            ->will($this->returnValue([]));

        $mapperMock = $this->getMapperMock();
        $mapperMock->expects($this->once())
            ->method('extractTypesFromRows')
            ->with($this->equalTo([]))
            ->will($this->returnValue([new Type()]));

        $handler = $this->getHandler();
        $res = $handler->loadContentTypes(23, 0);

        $this->assertEquals(
            [new Type()],
            $res
        );
    }

    /**
     * @covers \eZ\Publish\Core\Persistence\Legacy\Content\Type\Handler::loadContentTypeList
     */
    public function testLoadContentTypeList(): void
    {
        $gatewayMock = $this->getGatewayMock();
        $gatewayMock->expects($this->once())
            ->method('loadTypesListData')
            ->with($this->equalTo([23, 24]))
            ->willReturn([]);

        $mapperMock = $this->getMapperMock();
        $mapperMock->expects($this->once())
            ->method('extractTypesFromRows')
            ->with($this->equalTo([]))
            ->willReturn([23 => new Type()]);

        $handler = $this->getHandler();
        $types = $handler->loadContentTypeList([23, 24]);

        $this->assertEquals(
            [23 => new Type()],
            $types,
            'Types not loaded correctly'
        );
    }

    /**
     * @covers \eZ\Publish\Core\Persistence\Legacy\Content\Type\Handler::load
     * @covers \eZ\Publish\Core\Persistence\Legacy\Content\Type\Handler::loadFromRows
     */
    public function testLoad()
    {
        $gatewayMock = $this->getGatewayMock();
        $gatewayMock->expects($this->once())
            ->method('loadTypeData')
            ->with(
                $this->equalTo(23),
                $this->equalTo(1)
            )
            ->will($this->returnValue([]));

        $mapperMock = $this->getMapperMock();
        $mapperMock->expects($this->once())
            ->method('extractTypesFromRows')
            ->with($this->equalTo([]))
            ->will(
                $this->returnValue(
                    [new Type()]
                )
            );

        $handler = $this->getHandler();
        $type = $handler->load(23, 1);

        $this->assertEquals(
            new Type(),
            $type,
            'Type not loaded correctly'
        );
    }

    /**
     * @covers \eZ\Publish\Core\Persistence\Legacy\Content\Type\Handler::load
     * @covers \eZ\Publish\Core\Persistence\Legacy\Content\Type\Handler::loadFromRows
     */
    public function testLoadNotFound()
    {
        $this->expectException(\eZ\Publish\Core\Persistence\Legacy\Exception\TypeNotFound::class);

        $gatewayMock = $this->getGatewayMock();
        $gatewayMock->expects($this->once())
            ->method('loadTypeData')
            ->with(
                $this->equalTo(23),
                $this->equalTo(1)
            )
            ->will($this->returnValue([]));

        $mapperMock = $this->getMapperMock();
        $mapperMock->expects($this->once())
            ->method('extractTypesFromRows')
            ->with($this->equalTo([]))
            ->will(
                $this->returnValue(
                    []
                )
            );

        $handler = $this->getHandler();
        $type = $handler->load(23, 1);
    }

    /**
     * @covers \eZ\Publish\Core\Persistence\Legacy\Content\Type\Handler::load
     * @covers \eZ\Publish\Core\Persistence\Legacy\Content\Type\Handler::loadFromRows
     */
    public function testLoadDefaultVersion()
    {
        $gatewayMock = $this->getGatewayMock();
        $gatewayMock->expects($this->once())
            ->method('loadTypeData')
            ->with(
                $this->equalTo(23),
                $this->equalTo(0)
            )
            ->will($this->returnValue([]));

        $mapperMock = $this->getMapperMock();
        $mapperMock->expects($this->once())
            ->method('extractTypesFromRows')
            ->will(
                $this->returnValue(
                    [new Type()]
                )
            );

        $handler = $this->getHandler();
        $type = $handler->load(23);

        $this->assertEquals(
            new Type(),
            $type,
            'Type not loaded correctly'
        );
    }

    /**
     * @covers \eZ\Publish\Core\Persistence\Legacy\Content\Type\Handler::loadByIdentifier
     * @covers \eZ\Publish\Core\Persistence\Legacy\Content\Type\Handler::loadFromRows
     */
    public function testLoadByIdentifier()
    {
        $gatewayMock = $this->getGatewayMock();
        $gatewayMock->expects($this->once())
            ->method('loadTypeDataByIdentifier')
            ->with(
                $this->equalTo('blogentry'),
                $this->equalTo(0)
            )
            ->will($this->returnValue([]));

        $mapperMock = $this->getMapperMock();
        $mapperMock->expects($this->once())
            ->method('extractTypesFromRows')
            ->will(
                $this->returnValue(
                    [new Type()]
                )
            );

        $handler = $this->getHandler();
        $type = $handler->loadByIdentifier('blogentry');

        $this->assertEquals(
            new Type(),
            $type,
            'Type not loaded correctly'
        );
    }

    /**
     * @covers \eZ\Publish\Core\Persistence\Legacy\Content\Type\Handler::loadByRemoteId
     * @covers \eZ\Publish\Core\Persistence\Legacy\Content\Type\Handler::loadFromRows
     */
    public function testLoadByRemoteId()
    {
        $gatewayMock = $this->getGatewayMock();
        $gatewayMock->expects($this->once())
            ->method('loadTypeDataByRemoteId')
            ->with(
                $this->equalTo('someLongHash'),
                $this->equalTo(0)
            )
            ->will($this->returnValue([]));

        $mapperMock = $this->getMapperMock();
        $mapperMock->expects($this->once())
            ->method('extractTypesFromRows')
            ->will(
                $this->returnValue(
                    [new Type()]
                )
            );

        $handler = $this->getHandler();
        $type = $handler->loadByRemoteId('someLongHash');

        $this->assertEquals(
            new Type(),
            $type,
            'Type not loaded correctly'
        );
    }

    /**
     * @covers \eZ\Publish\Core\Persistence\Legacy\Content\Type\Handler::create
     */
    public function testCreate()
    {
        $createStructFix = $this->getContentTypeCreateStructFixture();
        $createStructClone = clone $createStructFix;

        $mapperMock = $this->getMapperMock(
            [
                'toStorageFieldDefinition',
            ]
        );

        $gatewayMock = $this->getGatewayMock();

        $gatewayMock->expects($this->once())
            ->method('insertType')
            ->with(
                $this->isInstanceOf(
                    Type::class
                )
            )
            ->will($this->returnValue(23));
        $gatewayMock->expects($this->once())
            ->method('insertGroupAssignment')
            ->with(
                $this->equalTo(42),
                $this->equalTo(23),
                $this->equalTo(1)
            );
        $gatewayMock->expects($this->exactly(2))
            ->method('insertFieldDefinition')
            ->with(
                $this->equalTo(23),
                $this->equalTo(1),
                $this->isInstanceOf(FieldDefinition::class),
                $this->isInstanceOf(StorageFieldDefinition::class)
            )
            ->will($this->returnValue(42));

        $mapperMock->expects($this->exactly(2))
            ->method('toStorageFieldDefinition')
            ->with(
                $this->isInstanceOf(FieldDefinition::class),
                $this->isInstanceOf(StorageFieldDefinition::class)
            );

        $handler = $this->getHandler();
        $type = $handler->create($createStructFix);

        $this->assertInstanceOf(
            Type::class,
            $type,
            'Incorrect type returned from create()'
        );
        $this->assertEquals(
            23,
            $type->id,
            'Incorrect ID for Type.'
        );

        $this->assertEquals(
            42,
            $type->fieldDefinitions[0]->id,
            'Field definition ID not set correctly'
        );
        $this->assertEquals(
            42,
            $type->fieldDefinitions[1]->id,
            'Field definition ID not set correctly'
        );

        $this->assertEquals(
            $createStructClone,
            $createStructFix,
            'Create struct manipulated'
        );
    }

    /**
     * @covers \eZ\Publish\Core\Persistence\Legacy\Content\Type\Handler::update
     */
    public function testUpdate()
    {
        $gatewayMock = $this->getGatewayMock();
        $gatewayMock->expects($this->once())
            ->method('updateType')
            ->with(
                $this->equalTo(23),
                $this->equalTo(1),
                $this->isInstanceOf(
                    Type::class
                )
            );

        $handlerMock = $this->getMockBuilder(Handler::class)
            ->setMethods(['load'])
            ->setConstructorArgs([$gatewayMock, $this->getMapperMock(), $this->getUpdateHandlerMock()])
            ->getMock();

        $handlerMock->expects($this->once())
            ->method('load')
            ->with(
                $this->equalTo(23),
                $this->equalTo(1)
            )
            ->will($this->returnValue(new Type()));

        $res = $handlerMock->update(23, 1, new UpdateStruct());

        $this->assertInstanceOf(
            Type::class,
            $res
        );
    }

    /**
     * @covers \eZ\Publish\Core\Persistence\Legacy\Content\Type\Handler::delete
     */
    public function testDeleteSuccess()
    {
        $gatewayMock = $this->getGatewayMock();

        $gatewayMock->expects(
            $this->once()
        )->method(
            'countInstancesOfType'
        )->with(
            $this->equalTo(23)
        )->will(
            $this->returnValue(0)
        );

        $gatewayMock->expects(
            $this->once()
        )->method(
            'delete'
        )->with(
            $this->equalTo(23),
            $this->equalTo(0)
        );

        $handler = $this->getHandler();
        $res = $handler->delete(23, 0);

        $this->assertTrue($res);
    }

    /**
     * @covers \eZ\Publish\Core\Persistence\Legacy\Content\Type\Handler::delete
     */
    public function testDeleteThrowsBadStateException()
    {
        $this->expectException(\eZ\Publish\Core\Base\Exceptions\BadStateException::class);

        $gatewayMock = $this->getGatewayMock();

        $gatewayMock->expects(
            $this->once()
        )->method(
            'countInstancesOfType'
        )->with(
            $this->equalTo(23)
        )->will(
            $this->returnValue(1)
        );

        $gatewayMock->expects($this->never())->method('delete');

        $handler = $this->getHandler();
        $res = $handler->delete(23, 0);
    }

    /**
     * @covers \eZ\Publish\Core\Persistence\Legacy\Content\Type\Handler::createDraft
     */
    public function testCreateVersion()
    {
        $userId = 42;
        $contentTypeId = 23;

        $gatewayMock = $this->getGatewayMock();
        $mapperMock = $this->getMapperMock();
        $mapperMock->expects($this->once())
            ->method('createCreateStructFromType')
            ->with(
                $this->isInstanceOf(
                    Type::class
                )
            )->will(
                $this->returnValue(new CreateStruct())
            );

        $handlerMock = $this->getMockBuilder(Handler::class)
            ->setMethods(['load', 'internalCreate'])
            ->setConstructorArgs([$gatewayMock, $mapperMock, $this->getUpdateHandlerMock()])
            ->getMock();

        $handlerMock->expects($this->once())
            ->method('load')
            ->with(
                $this->equalTo($contentTypeId, Type::STATUS_DEFINED)
            )->will(
                $this->returnValue(
                    new Type()
                )
            );

        $typeDraft = new Type();
        $handlerMock->expects($this->once())
            ->method('internalCreate')
            ->with(
                $this->isInstanceOf(CreateStruct::class),
                $this->equalTo($contentTypeId)
            )->will(
                $this->returnValue($typeDraft)
            );

        $res = $handlerMock->createDraft($userId, $contentTypeId);

        $this->assertSame(
            $typeDraft,
            $res
        );
    }

    /**
     * @covers \eZ\Publish\Core\Persistence\Legacy\Content\Type\Handler::copy
     */
    public function testCopy()
    {
        $gatewayMock = $this->getGatewayMock();
        $mapperMock = $this->getMapperMock(['createCreateStructFromType']);
        $mapperMock->expects($this->once())
            ->method('createCreateStructFromType')
            ->with(
                $this->isInstanceOf(
                    Type::class
                )
            )->willReturn(
                new CreateStruct(['identifier' => 'testCopy'])
            );

        $handlerMock = $this->getMockBuilder(Handler::class)
            ->setMethods(['load', 'internalCreate', 'update'])
            ->setConstructorArgs([$gatewayMock, $mapperMock, $this->getUpdateHandlerMock()])
            ->getMock();

        $userId = 42;
        $type = new Type([
            'id' => 23,
            'identifier' => md5(uniqid(get_class($handlerMock), true)),
            'status' => Type::STATUS_DEFINED,
        ]);

        $handlerMock->expects($this->once())
            ->method('load')
            ->with(
                $this->equalTo($type->id, Type::STATUS_DEFINED)
            )->willReturn(
                $type
            );

        $typeCopy = clone $type;
        $typeCopy->id = 24;
        $typeCopy->identifier = 'copy_of' . $type->identifier . '_' . $type->id;

        $handlerMock->expects($this->once())
            ->method('internalCreate')
            ->with(
                $this->isInstanceOf(CreateStruct::class),
            )->willReturn(
                $typeCopy
            );

        $handlerMock->expects($this->once())
            ->method('update')
            ->with(
                $this->equalTo($typeCopy->id),
                $this->equalTo(Type::STATUS_DEFINED),
                $this->isInstanceOf(UpdateStruct::class)
            )
            ->will(
                $this->returnValue($typeCopy)
            );

        $res = $handlerMock->copy($userId, $type->id, Type::STATUS_DEFINED);

        $this->assertEquals(
            $typeCopy,
            $res
        );
    }

    /**
     * @covers \eZ\Publish\Core\Persistence\Legacy\Content\Type\Handler::link
     */
    public function testLink()
    {
        $gatewayMock = $this->getGatewayMock();
        $gatewayMock->expects($this->once())
            ->method('insertGroupAssignment')
            ->with(
                $this->equalTo(3),
                $this->equalTo(23),
                $this->equalTo(1)
            );

        $mapperMock = $this->getMapperMock();

        $handler = $this->getHandler();
        $res = $handler->link(3, 23, 1);

        $this->assertTrue($res);
    }

    /**
     * @covers \eZ\Publish\Core\Persistence\Legacy\Content\Type\Handler::unlink
     */
    public function testUnlinkSuccess()
    {
        $gatewayMock = $this->getGatewayMock();
        $gatewayMock->expects($this->once())
            ->method('countGroupsForType')
            ->with(
                $this->equalTo(23),
                $this->equalTo(1)
            )->will($this->returnValue(2));

        $gatewayMock->expects($this->once())
            ->method('deleteGroupAssignment')
            ->with(
                $this->equalTo(3),
                $this->equalTo(23),
                $this->equalTo(1)
            );

        $mapperMock = $this->getMapperMock();

        $handler = $this->getHandler();
        $res = $handler->unlink(3, 23, 1);

        $this->assertTrue($res);
    }

    /**
     * @covers \eZ\Publish\Core\Persistence\Legacy\Content\Type\Handler::unlink
     * @covers \eZ\Publish\Core\Persistence\Legacy\Exception\RemoveLastGroupFromType
     */
    public function testUnlinkFailure()
    {
        $this->expectException(\eZ\Publish\Core\Persistence\Legacy\Exception\RemoveLastGroupFromType::class);
        $this->expectExceptionMessage('Type with ID "23" in status "1" cannot be unlinked from its last group.');

        $gatewayMock = $this->getGatewayMock();
        $gatewayMock->expects($this->once())
            ->method('countGroupsForType')
            ->with(
                $this->equalTo(23),
                $this->equalTo(1)
            )
            // Only 1 group assigned
            ->will($this->returnValue(1));

        $mapperMock = $this->getMapperMock();

        $handler = $this->getHandler();
        $res = $handler->unlink(3, 23, 1);
    }

    /**
     * @covers \eZ\Publish\Core\Persistence\Legacy\Content\Type\Handler::getFieldDefinition
     */
    public function testGetFieldDefinition()
    {
        $mapperMock = $this->getMapperMock(
            [
                'extractFieldFromRow',
                'extractMultilingualData',
            ]
        );
        $mapperMock->expects($this->once())
            ->method('extractFieldFromRow')
            ->with(
                $this->equalTo([])
            )->will(
                $this->returnValue(new FieldDefinition())
            );

        $mapperMock->expects($this->once())
            ->method('extractMultilingualData')
            ->with(
                $this->equalTo([
                    [],
                ])
            )->will(
                $this->returnValue([])
            );

        $gatewayMock = $this->getGatewayMock();
        $gatewayMock->expects($this->once())
            ->method('loadFieldDefinition')
            ->with(
                $this->equalTo(42),
                $this->equalTo(Type::STATUS_DEFINED)
            )->will(
                $this->returnValue([
                    [],
                ])
            );

        $handler = $this->getHandler();
        $fieldDefinition = $handler->getFieldDefinition(42, Type::STATUS_DEFINED);

        $this->assertInstanceOf(
            FieldDefinition::class,
            $fieldDefinition
        );
    }

    /**
     * @covers \eZ\Publish\Core\Persistence\Legacy\Content\Type\Handler::addFieldDefinition
     */
    public function testAddFieldDefinition()
    {
        $mapperMock = $this->getMapperMock(
            ['toStorageFieldDefinition']
        );
        $mapperMock->expects($this->once())
            ->method('toStorageFieldDefinition')
            ->with(
                $this->isInstanceOf(
                    FieldDefinition::class
                ),
                $this->isInstanceOf(
                    StorageFieldDefinition::class
                )
            );

        $gatewayMock = $this->getGatewayMock();
        $gatewayMock->expects($this->once())
            ->method('insertFieldDefinition')
            ->with(
                $this->equalTo(23),
                $this->equalTo(1),
                $this->isInstanceOf(
                    FieldDefinition::class
                ),
                $this->isInstanceOf(
                    StorageFieldDefinition::class
                )
            )->will(
                $this->returnValue(42)
            );

        $fieldDef = new FieldDefinition();

        $handler = $this->getHandler();
        $handler->addFieldDefinition(23, 1, $fieldDef);

        $this->assertEquals(
            42,
            $fieldDef->id
        );
    }

    /**
     * @covers \eZ\Publish\Core\Persistence\Legacy\Content\Type\Handler::getContentCount
     */
    public function testGetContentCount()
    {
        $gatewayMock = $this->getGatewayMock();
        $gatewayMock->expects($this->once())
            ->method('countInstancesOfType')
            ->with(
                $this->equalTo(23)
            )->will(
                $this->returnValue(42)
            );

        $handler = $this->getHandler();

        $this->assertEquals(
            42,
            $handler->getContentCount(23)
        );
    }

    /**
     * @covers \eZ\Publish\Core\Persistence\Legacy\Content\Type\Handler::removeFieldDefinition
     */
    public function testRemoveFieldDefinition()
    {
        $gatewayMock = $this->getGatewayMock();
        $gatewayMock->expects($this->once())
            ->method('deleteFieldDefinition')
            ->with(
                $this->equalTo(23),
                $this->equalTo(1),
                $this->equalTo(42)
            );

        $handler = $this->getHandler();
        $res = $handler->removeFieldDefinition(23, 1, 42);

        $this->assertTrue($res);
    }

    /**
     * @covers \eZ\Publish\Core\Persistence\Legacy\Content\Type\Handler::updateFieldDefinition
     */
    public function testUpdateFieldDefinition()
    {
        $mapperMock = $this->getMapperMock(
            ['toStorageFieldDefinition']
        );
        $mapperMock->expects($this->once())
            ->method('toStorageFieldDefinition')
            ->with(
                $this->isInstanceOf(
                    FieldDefinition::class
                ),
                $this->isInstanceOf(
                    StorageFieldDefinition::class
                )
            );

        $gatewayMock = $this->getGatewayMock();
        $gatewayMock->expects($this->once())
            ->method('updateFieldDefinition')
            ->with(
                $this->equalTo(23),
                $this->equalTo(1),
                $this->isInstanceOf(
                    FieldDefinition::class
                )
            );

        $fieldDef = new FieldDefinition();

        $handler = $this->getHandler();
        $res = $handler->updateFieldDefinition(23, 1, $fieldDef);

        $this->assertNull($res);
    }

    /**
     * @covers \eZ\Publish\Core\Persistence\Legacy\Content\Type\Handler::publish
     */
    public function testPublish()
    {
        $handler = $this->getPartlyMockedHandler(['load']);
        $updateHandlerMock = $this->getUpdateHandlerMock();

        $handler->expects($this->exactly(2))
            ->method('load')
            ->with(
                $this->equalTo(23),
                $this->logicalOr(
                    $this->equalTo(0),
                    $this->equalTo(1)
                )
            )->will(
                $this->returnValue(new Type())
            );

        $updateHandlerMock->expects($this->once())
            ->method('updateContentObjects')
            ->with(
                $this->isInstanceOf(Type::class),
                $this->isInstanceOf(Type::class)
            );
        $updateHandlerMock->expects($this->once())
            ->method('deleteOldType')
            ->with(
                $this->isInstanceOf(Type::class)
            );
        $updateHandlerMock->expects($this->once())
            ->method('publishNewType')
            ->with(
                $this->isInstanceOf(Type::class),
                $this->equalTo(0)
            );

        $handler->publish(23);
    }

    /**
     * @covers \eZ\Publish\Core\Persistence\Legacy\Content\Type\Handler::publish
     */
    public function testPublishNoOldType()
    {
        $handler = $this->getPartlyMockedHandler(['load']);
        $updateHandlerMock = $this->getUpdateHandlerMock();

        $handler->expects($this->at(0))
            ->method('load')
            ->with(
                $this->equalTo(23),
                $this->equalTo(1)
            )->will(
                $this->returnValue(new Type())
            );

        $handler->expects($this->at(1))
            ->method('load')
            ->with(
                $this->equalTo(23),
                $this->equalTo(0)
            )->will(
                $this->throwException(new Exception\TypeNotFound(23, 0))
            );

        $updateHandlerMock->expects($this->never())
            ->method('updateContentObjects');
        $updateHandlerMock->expects($this->never())
            ->method('deleteOldType');
        $updateHandlerMock->expects($this->once())
            ->method('publishNewType')
            ->with(
                $this->isInstanceOf(Type::class),
                $this->equalTo(0)
            );

        $handler->publish(23);
    }

    /**
     * Returns a handler to test, based on mock objects.
     *
     * @return \eZ\Publish\Core\Persistence\Legacy\Content\Type\Handler
     */
    protected function getHandler()
    {
        return new Handler(
            $this->getGatewayMock(),
            $this->getMapperMock(),
            $this->getUpdateHandlerMock()
        );
    }

    /**
     * Returns a handler to test with $methods mocked.
     *
     * @param array $methods
     *
     * @return \eZ\Publish\Core\Persistence\Legacy\Content\Type\Handler
     */
    protected function getPartlyMockedHandler(array $methods)
    {
        return $this->getMockBuilder(Handler::class)
            ->setMethods($methods)
            ->setConstructorArgs(
                [
                    $this->getGatewayMock(),
                    $this->getMapperMock(),
                    $this->getUpdateHandlerMock(),
                ]
            )
            ->getMock();
    }

    /**
     * Returns a gateway mock.
     *
     * @return \eZ\Publish\Core\Persistence\Legacy\Content\Type\Gateway
     */
    protected function getGatewayMock()
    {
        if (!isset($this->gatewayMock)) {
            $this->gatewayMock = $this->getMockForAbstractClass(
                Gateway::class
            );
        }

        return $this->gatewayMock;
    }

    /**
     * Returns a mapper mock.
     *
     * @param array $methods
     *
     * @return \eZ\Publish\Core\Persistence\Legacy\Content\Type\Mapper
     */
    protected function getMapperMock($methods = [])
    {
        if (!isset($this->mapperMock)) {
            $this->mapperMock = $this->getMockBuilder(Mapper::class)
                ->disableOriginalConstructor()
                ->setMethods($methods)
                ->getMock();
        }

        return $this->mapperMock;
    }

    /**
     * Returns a Update\Handler mock.
     *
     * @return \eZ\Publish\Core\Persistence\Legacy\Content\Type\Update\Handler
     */
    public function getUpdateHandlerMock()
    {
        if (!isset($this->updateHandlerMock)) {
            $this->updateHandlerMock = $this->getMockBuilder(UpdateHandler::class)
                ->disableOriginalConstructor()
                ->setMethods([])
                ->getMock();
        }

        return $this->updateHandlerMock;
    }

    /**
     * Returns a CreateStruct fixture.
     *
     * @return \eZ\Publish\SPI\Persistence\Content\Type\CreateStruct
     */
    protected function getContentTypeCreateStructFixture()
    {
        $struct = new CreateStruct();
        $struct->status = 1;
        $struct->groupIds = [
            42,
        ];
        $struct->name = [
            'eng-GB' => 'test name',
        ];

        $fieldDefName = new FieldDefinition(['position' => 1]);
        $fieldDefShortDescription = new FieldDefinition(['position' => 2]);

        $struct->fieldDefinitions = [
            $fieldDefName,
            $fieldDefShortDescription,
        ];

        return $struct;
    }

    /**
     * @covers \eZ\Publish\Core\Persistence\Legacy\Content\Type\Handler::removeContentTypeTranslation
     */
    public function testRemoveContentTypeTranslation()
    {
        $mapperMock = $this->getMapperMock();
        $mapperMock->expects($this->once())
            ->method('createUpdateStructFromType')
            ->with(
                $this->isInstanceOf(
                    Type::class
                )
            )
            ->will(
                $this->returnValue(new UpdateStruct())
            );

        $handlerMock = $this->getMockBuilder(Handler::class)
            ->setMethods(['load', 'update'])
            ->setConstructorArgs([$this->getGatewayMock(), $mapperMock, $this->getUpdateHandlerMock()])
            ->getMock();

        $handlerMock->expects($this->once())
            ->method('load')
            ->with(
                $this->equalTo(23),
                $this->equalTo(1)
            )
            ->will($this->returnValue(new Type(['id' => 23])));

        $handlerMock->expects($this->once())
            ->method('update')
            ->with(
                $this->equalTo(23),
                $this->equalTo(1),
                $this->isInstanceOf(
                    UpdateStruct::class
                )
            )
            ->will($this->returnValue(new Type()));

        $res = $handlerMock->removeContentTypeTranslation(23, 'eng-GB');

        $this->assertInstanceOf(
            Type::class,
            $res
        );
    }
}
