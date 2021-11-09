<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Core\MVC\Symfony\Event;

use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Core\MVC\Symfony\Event\ContentCacheClearEvent;
use Ibexa\Core\Repository\Values\Content\Location;
use PHPUnit\Framework\TestCase;

class ContentCacheClearEventTest extends TestCase
{
    public function testConstruct()
    {
        $contentInfo = new ContentInfo();
        $event = new ContentCacheClearEvent($contentInfo);
        $this->assertSame($contentInfo, $event->getContentInfo());
    }

    public function testAddLocationsToClear()
    {
        $contentInfo = new ContentInfo();
        $event = new ContentCacheClearEvent($contentInfo);
        $locations = [new Location(), new Location()];
        $event->addLocationToClear($locations[0]);
        $event->addLocationToClear($locations[1]);

        $this->assertSame($locations, $event->getLocationsToClear());
    }

    public function setLocationsToClear()
    {
        $contentInfo = new ContentInfo();
        $event = new ContentCacheClearEvent($contentInfo);
        $initialLocations = [new Location(), new Location()];
        $event->addLocationToClear($initialLocations[0]);
        $event->addLocationToClear($initialLocations[1]);
        $this->assertSame($initialLocations, $event->getLocationsToClear());

        $otherLocations = [new Location(), new Location()];
        $event->setLocationsToClear($otherLocations);
        $this->assertSame($otherLocations, $event->getLocationsToClear());
    }
}

class_alias(ContentCacheClearEventTest::class, 'eZ\Publish\Core\MVC\Symfony\Event\Tests\ContentCacheClearEventTest');
