<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Core\Repository\Values\ObjectState;

use Ibexa\Contracts\Core\Repository\Exceptions\PropertyNotFoundException;
use Ibexa\Contracts\Core\Repository\Exceptions\PropertyReadOnlyException;
use Ibexa\Core\Repository\Values\ObjectState\ObjectState;
use Ibexa\Tests\Core\Repository\Values\MultiLanguageTestTrait;
use Ibexa\Tests\Core\Repository\Values\ValueObjectTestTrait;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ibexa\Core\Repository\Values\ObjectState\ObjectState
 */
class ObjectStateTest extends TestCase
{
    use ValueObjectTestTrait;
    use MultiLanguageTestTrait;

    /**
     * Test a new class and default values on properties.
     */
    public function testNewClass()
    {
        $objectState = new ObjectState();

        $this->assertPropertiesCorrect(
            [
                'id' => null,
                'identifier' => null,
                'priority' => null,
                'mainLanguageCode' => null,
                'languageCodes' => null,
                'names' => [],
                'descriptions' => [],
            ],
            $objectState
        );
    }

    /**
     * Test a new class with unified multi language logic properties.
     *
     * @return \Ibexa\Core\Repository\Values\ObjectState\ObjectState
     */
    public function testNewClassWithMultiLanguageProperties()
    {
        $properties = [
            'names' => [
                'eng-US' => 'Name',
                'pol-PL' => 'Nazwa',
            ],
            'descriptions' => [
                'eng-US' => 'Description',
                'pol-PL' => 'Opis',
            ],
            'mainLanguageCode' => 'eng-US',
            'prioritizedLanguages' => ['pol-PL', 'eng-US'],
        ];

        $objectState = new ObjectState($properties);
        $this->assertPropertiesCorrect($properties, $objectState);

        // BC test:
        self::assertTrue(isset($objectState->defaultLanguageCode));
        self::assertSame('eng-US', $objectState->defaultLanguageCode);

        return $objectState;
    }

    /**
     * Test retrieving missing property.
     *
     * @covers \Ibexa\Core\Repository\Values\ObjectState\ObjectState::__get
     * @covers \Ibexa\Core\Repository\Values\ObjectState\ObjectStateGroup::__get
     */
    public function testMissingProperty()
    {
        $this->expectException(PropertyNotFoundException::class);

        $objectState = new ObjectState();
        $value = $objectState->notDefined;
        $this->fail('Succeeded getting non existing property');
    }

    /**
     * Test setting read only property.
     *
     * @covers \Ibexa\Core\Repository\Values\ObjectState\ObjectState::__set
     * @covers \Ibexa\Core\Repository\Values\ObjectState\ObjectStateGroup::__set
     */
    public function testReadOnlyProperty()
    {
        $this->expectException(PropertyReadOnlyException::class);

        $objectState = new ObjectState();
        $objectState->id = 42;
        $this->fail('Succeeded setting read only property');
    }

    /**
     * Test if property exists.
     */
    public function testIsPropertySet()
    {
        $objectState = new ObjectState();
        $value = isset($objectState->notDefined);
        $this->assertFalse($value);

        $value = isset($objectState->id);
        $this->assertTrue($value);
    }

    /**
     * Test unsetting a property.
     *
     * @covers \Ibexa\Core\Repository\Values\ObjectState\ObjectState::__unset
     * @covers \Ibexa\Contracts\Core\Repository\Values\ObjectState\ObjectStateGroup::__unset
     */
    public function testUnsetProperty()
    {
        $this->expectException(PropertyReadOnlyException::class);

        $objectState = new ObjectState(['id' => 2]);
        unset($objectState->id);
        $this->fail('Unsetting read-only property succeeded');
    }
}

class_alias(ObjectStateTest::class, 'eZ\Publish\Core\Repository\Tests\Values\ObjectState\ObjectStateTest');
