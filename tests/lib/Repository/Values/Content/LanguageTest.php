<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Core\Repository\Values\Content;

use Ibexa\Contracts\Core\Repository\Exceptions\PropertyNotFoundException;
use Ibexa\Contracts\Core\Repository\Exceptions\PropertyReadOnlyException;
use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Tests\Core\Repository\Values\ValueObjectTestTrait;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ibexa\Contracts\Core\Repository\Values\Content\Language
 */
class LanguageTest extends TestCase
{
    use ValueObjectTestTrait;

    /**
     * Test default properties of just created class.
     */
    public function testNewClass()
    {
        $language = new Language();

        $this->assertPropertiesCorrect(
            [
                'id' => null,
                'languageCode' => null,
                'name' => null,
                'enabled' => null,
            ],
            $language
        );
    }

    /**
     * Test retrieving missing property.
     */
    public function testMissingProperty()
    {
        $this->expectException(PropertyNotFoundException::class);
        $this->expectExceptionMessage('Property \'notDefined\' not found on class');

        $language = new Language();
        $value = $language->notDefined;
        self::fail('Succeeded getting non existing property');
    }

    /**
     * Test setting read only property.
     */
    public function testReadOnlyProperty()
    {
        $this->expectException(PropertyReadOnlyException::class);
        $this->expectExceptionMessage('Property \'id\' is readonly on class');

        $language = new Language();
        $language->id = 42;
        self::fail('Succeeded setting read only property');
    }

    /**
     * Test if property exists.
     */
    public function testIsPropertySet()
    {
        $language = new Language();
        $value = isset($language->notDefined);
        self::assertFalse($value);

        $value = isset($language->id);
        self::assertTrue($value);
    }

    /**
     * Test unsetting a property.
     */
    public function testUnsetProperty()
    {
        $this->expectException(PropertyReadOnlyException::class);
        $this->expectExceptionMessage('Property \'id\' is readonly on class');

        $language = new Language(['id' => 2]);
        unset($language->id);
        self::fail('Unsetting read-only property succeeded');
    }
}

class_alias(LanguageTest::class, 'eZ\Publish\API\Repository\Tests\Values\Content\LanguageTest');
