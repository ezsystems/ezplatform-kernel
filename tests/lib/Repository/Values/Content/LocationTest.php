<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Core\Repository\Values\Content;

use Ibexa\Contracts\Core\Repository\Exceptions\PropertyNotFoundException;
use Ibexa\Contracts\Core\Repository\Exceptions\PropertyReadOnlyException;
use Ibexa\Core\Repository\Values\Content\Location;
use Ibexa\Tests\Core\Repository\Values\ValueObjectTestTrait;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ibexa\Core\Repository\Values\Content\Location
 */
class LocationTest extends TestCase
{
    use ValueObjectTestTrait;

    /**
     * Test a new class and default values on properties.
     */
    public function testNewClass()
    {
        $location = new Location();

        $this->assertPropertiesCorrect(
            [
                'id' => null,
                'contentInfo' => null,
                'priority' => null,
                'hidden' => null,
                'invisible' => null,
                'remoteId' => null,
                'parentLocationId' => null,
                'pathString' => null,
                'path' => [],
                'depth' => null,
                'sortField' => null,
                'sortOrder' => null,
            ],
            $location
        );
    }

    /**
     * Test retrieving missing property.
     */
    public function testMissingProperty()
    {
        $this->expectException(PropertyNotFoundException::class);

        $location = new Location();
        $value = $location->notDefined;
        self::fail('Succeeded getting non existing property');
    }

    /**
     * Test setting read only property.
     *
     * @covers \Ibexa\Core\Repository\Values\Content\Location::__set
     */
    public function testReadOnlyProperty()
    {
        $this->expectException(PropertyReadOnlyException::class);

        $location = new Location();
        $location->id = 42;
        self::fail('Succeeded setting read only property');
    }

    /**
     * Test if property exists.
     */
    public function testIsPropertySet()
    {
        $location = new Location();
        $value = isset($location->notDefined);
        self::assertFalse($value);

        $value = isset($location->id);
        self::assertTrue($value);
    }

    /**
     * Test unsetting a property.
     *
     * @covers \Ibexa\Core\Repository\Values\Content\Location::__unset
     */
    public function testUnsetProperty()
    {
        $this->expectException(PropertyReadOnlyException::class);

        $location = new Location(['id' => 2]);
        unset($location->id);
        self::fail('Unsetting read-only property succeeded');
    }
}

class_alias(LocationTest::class, 'eZ\Publish\Core\Repository\Tests\Values\Content\LocationTest');
