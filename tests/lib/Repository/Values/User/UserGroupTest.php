<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Core\Repository\Values\User;

use Ibexa\Contracts\Core\Repository\Exceptions\PropertyNotFoundException;
use Ibexa\Contracts\Core\Repository\Exceptions\PropertyReadOnlyException;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo;
use Ibexa\Core\Repository\Values\User\UserGroup;
use Ibexa\Tests\Core\Repository\Values\ValueObjectTestTrait;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ibexa\Core\Repository\Values\User\UserGroup
 */
class UserGroupTest extends TestCase
{
    use ValueObjectTestTrait;

    public function testNewClass()
    {
        $group = new UserGroup();
        self::assertNull($group->parentId);

        $this->assertPropertiesCorrect(
            [
                'parentId' => null,
            ],
            $group
        );
    }

    /**
     * Test getName method.
     */
    public function testGetName()
    {
        $name = 'Translated name';
        $contentMock = $this->createMock(Content::class);
        $versionInfoMock = $this->createMock(VersionInfo::class);

        $contentMock->expects($this->once())
            ->method('getVersionInfo')
            ->willReturn($versionInfoMock);

        $versionInfoMock->expects($this->once())
            ->method('getName')
            ->willReturn($name);

        $object = new UserGroup(['content' => $contentMock]);

        $this->assertEquals($name, $object->getName());
    }

    /**
     * Test retrieving missing property.
     */
    public function testMissingProperty()
    {
        $this->expectException(PropertyNotFoundException::class);

        $userGroup = new UserGroup();
        $value = $userGroup->notDefined;
        self::fail('Succeeded getting non existing property');
    }

    public function testObjectProperties()
    {
        $object = new UserGroup();
        $properties = $object->attributes();
        self::assertNotContains('internalFields', $properties, 'Internal property found ');
        self::assertContains('id', $properties, 'Property not found ');
        self::assertContains('fields', $properties, 'Property not found ');
        self::assertContains('versionInfo', $properties, 'Property not found ');
        self::assertContains('contentInfo', $properties, 'Property not found ');

        // check for duplicates and double check existence of property
        $propertiesHash = [];
        foreach ($properties as $property) {
            if (isset($propertiesHash[$property])) {
                self::fail("Property '{$property}' exists several times in properties list");
            } elseif (!isset($object->$property)) {
                self::fail("Property '{$property}' does not exist on object, even though it was hinted to be there");
            }
            $propertiesHash[$property] = 1;
        }
    }

    /**
     * Test setting read only property.
     */
    public function testReadOnlyProperty()
    {
        $this->expectException(PropertyReadOnlyException::class);

        $userGroup = new UserGroup();
        $userGroup->parentId = 42;
        self::fail('Succeeded setting read only property');
    }

    /**
     * Test if property exists.
     */
    public function testIsPropertySet()
    {
        $userGroup = new UserGroup();
        $value = isset($userGroup->notDefined);
        self::assertFalse($value);

        $value = isset($userGroup->parentId);
        self::assertTrue($value);
    }

    /**
     * Test unsetting a property.
     */
    public function testUnsetProperty()
    {
        $this->expectException(PropertyReadOnlyException::class);

        $userGroup = new UserGroup(['parentId' => 1]);
        unset($userGroup->parentId);
        self::fail('Unsetting read-only property succeeded');
    }
}

class_alias(UserGroupTest::class, 'eZ\Publish\Core\Repository\Tests\Values\User\UserGroupTest');
