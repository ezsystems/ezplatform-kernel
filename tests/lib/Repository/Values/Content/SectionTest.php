<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Core\Repository\Values\Content;

use Ibexa\Contracts\Core\Repository\Exceptions\PropertyNotFoundException;
use Ibexa\Contracts\Core\Repository\Exceptions\PropertyReadOnlyException;
use Ibexa\Contracts\Core\Repository\Values\Content\Section;
use Ibexa\Tests\Core\Repository\Values\ValueObjectTestTrait;
use PHPUnit\Framework\TestCase;

class SectionTest extends TestCase
{
    use ValueObjectTestTrait;

    /**
     * Test a new class and default values on properties.
     *
     * @covers \Ibexa\Contracts\Core\Repository\Values\Content\Section::__construct
     */
    public function testNewClass()
    {
        $section = new Section();

        $this->assertPropertiesCorrect(
            [
                'id' => null,
                'identifier' => null,
                'name' => null,
            ],
            $section
        );
    }

    /**
     * Test retrieving missing property.
     *
     * @covers \Ibexa\Contracts\Core\Repository\Values\Content\Section::__get
     */
    public function testMissingProperty()
    {
        $this->expectException(PropertyNotFoundException::class);

        $section = new Section();
        $value = $section->notDefined;
        self::fail('Succeeded getting non existing property');
    }

    /**
     * Test setting read only property.
     *
     * @covers \Ibexa\Contracts\Core\Repository\Values\Content\Section::__set
     */
    public function testReadOnlyProperty()
    {
        $this->expectException(PropertyReadOnlyException::class);

        $section = new Section();
        $section->id = 22;
        self::fail('Succeeded setting read only property');
    }

    /**
     * Test if property exists.
     *
     * @covers \Ibexa\Contracts\Core\Repository\Values\Content\Section::__isset
     */
    public function testIsPropertySet()
    {
        $section = new Section();
        $value = isset($section->notDefined);
        self::assertFalse($value);

        $value = isset($section->id);
        self::assertTrue($value);
    }

    /**
     * Test unsetting a property.
     *
     * @covers \Ibexa\Contracts\Core\Repository\Values\Content\Section::__unset
     */
    public function testUnsetProperty()
    {
        $this->expectException(PropertyReadOnlyException::class);

        $section = new Section(['id' => 1]);
        unset($section->id);
        self::fail('Unsetting read-only property succeeded');
    }
}

class_alias(SectionTest::class, 'eZ\Publish\API\Repository\Tests\Values\Content\SectionTest');
