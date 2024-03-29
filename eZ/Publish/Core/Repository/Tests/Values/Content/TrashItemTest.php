<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace eZ\Publish\Core\Repository\Tests\Values\Content;

use eZ\Publish\API\Repository\Tests\Values\ValueObjectTestTrait;
use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\Core\Repository\Values\Content\TrashItem;
use PHPUnit\Framework\TestCase;

class TrashItemTest extends TestCase
{
    use ValueObjectTestTrait;

    /**
     * @covers \eZ\Publish\Core\Repository\Values\Content\TrashItem::__construct
     */
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
     *
     * @covers \eZ\Publish\API\Repository\Values\Content\TrashItem::__get
     */
    public function testMissingProperty()
    {
        $this->expectException(\eZ\Publish\API\Repository\Exceptions\PropertyNotFoundException::class);

        $trashItem = new TrashItem();
        $value = $trashItem->notDefined;
        self::fail('Succeeded getting non existing property');
    }

    /**
     * Test setting read only property.
     *
     * @covers \eZ\Publish\API\Repository\Values\Content\TrashItem::__set
     */
    public function testReadOnlyProperty()
    {
        $this->expectException(\eZ\Publish\API\Repository\Exceptions\PropertyReadOnlyException::class);

        $trashItem = new TrashItem();
        $trashItem->id = 42;
        self::fail('Succeeded setting read only property');
    }

    /**
     * Test if property exists.
     *
     * @covers \eZ\Publish\API\Repository\Values\Content\TrashItem::__isset
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
     * @covers \eZ\Publish\API\Repository\Values\Content\TrashItem::__unset
     */
    public function testUnsetProperty()
    {
        $this->expectException(\eZ\Publish\API\Repository\Exceptions\PropertyReadOnlyException::class);

        $trashItem = new TrashItem(['id' => 2]);
        unset($trashItem->id);
        self::fail('Unsetting read-only property succeeded');
    }
}
