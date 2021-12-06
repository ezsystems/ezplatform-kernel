<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Core\Repository\Values\Content;

use Ibexa\Contracts\Core\Repository\Exceptions\PropertyNotFoundException;
use Ibexa\Contracts\Core\Repository\Exceptions\PropertyReadOnlyException;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Core\Repository\Values\Content\TrashItem;
use Ibexa\Tests\Core\Repository\Values\ValueObjectTestTrait;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ibexa\Core\Repository\Values\Content\TrashItem
 */
class TrashItemTest extends TestCase
{
    use ValueObjectTestTrait;

    public function testNewClass()
    {
        // create ContentInfo to be able to retrieve the contentId property via magic method
        $contentInfo = new ContentInfo();
        $trashItem = new TrashItem(['contentInfo' => $contentInfo]);

        $this->assertPropertiesCorrect(
            [
                'contentInfo' => $contentInfo,
                'contentId' => null,
                'id' => null,
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
            $trashItem
        );
    }

    /**
     * Test retrieving missing property.
     */
    public function testMissingProperty()
    {
        $this->expectException(PropertyNotFoundException::class);

        $trashItem = new TrashItem();
        $value = $trashItem->notDefined;
        self::fail('Succeeded getting non existing property');
    }

    /**
     * Test setting read only property.
     */
    public function testReadOnlyProperty()
    {
        $this->expectException(PropertyReadOnlyException::class);

        $trashItem = new TrashItem();
        $trashItem->id = 42;
        self::fail('Succeeded setting read only property');
    }

    /**
     * Test if property exists.
     */
    public function testIsPropertySet()
    {
        $trashItem = new TrashItem();
        $value = isset($trashItem->notDefined);
        self::assertFalse($value);

        $value = isset($trashItem->id);
        self::assertTrue($value);
    }

    /**
     * Test unsetting a property.
     *
     * @covers \Ibexa\Core\Repository\Values\Content\TrashItem::__unset
     */
    public function testUnsetProperty()
    {
        $this->expectException(PropertyReadOnlyException::class);

        $trashItem = new TrashItem(['id' => 2]);
        unset($trashItem->id);
        self::fail('Unsetting read-only property succeeded');
    }
}

class_alias(TrashItemTest::class, 'eZ\Publish\Core\Repository\Tests\Values\Content\TrashItemTest');
