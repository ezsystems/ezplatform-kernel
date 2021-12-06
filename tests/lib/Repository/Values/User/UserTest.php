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
use Ibexa\Core\Repository\Values\User\User;
use Ibexa\Tests\Core\Repository\Values\ValueObjectTestTrait;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ibexa\Core\Repository\Values\User\User
 */
class UserTest extends TestCase
{
    use ValueObjectTestTrait;

    /**
     * Test a new class and default values on properties.
     */
    public function testNewClass()
    {
        $user = new User();

        $this->assertPropertiesCorrect(
            [
                'login' => null,
                'email' => null,
                'passwordHash' => null,
                'hashAlgorithm' => null,
                'maxLogin' => null,
                'enabled' => false,
            ],
            $user
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

        $object = new User(['content' => $contentMock]);

        $this->assertEquals($name, $object->getName());
    }

    /**
     * Test retrieving missing property.
     */
    public function testMissingProperty()
    {
        $this->expectException(PropertyNotFoundException::class);

        $user = new User();
        $value = $user->notDefined;
        self::fail('Succeeded getting non existing property');
    }

    public function testObjectProperties()
    {
        $object = new User();
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

        $user = new User();
        $user->login = 'user';
        self::fail('Succeeded setting read only property');
    }

    /**
     * Test if property exists.
     */
    public function testIsPropertySet()
    {
        $user = new User();
        $value = isset($user->notDefined);
        self::assertFalse($value);

        $value = isset($user->login);
        self::assertTrue($value);
    }

    /**
     * Test unsetting a property.
     */
    public function testUnsetProperty()
    {
        $this->expectException(PropertyReadOnlyException::class);

        $user = new User(['login' => 'admin']);
        unset($user->login);
        self::fail('Unsetting read-only property succeeded');
    }
}

class_alias(UserTest::class, 'eZ\Publish\Core\Repository\Tests\Values\User\UserTest');
