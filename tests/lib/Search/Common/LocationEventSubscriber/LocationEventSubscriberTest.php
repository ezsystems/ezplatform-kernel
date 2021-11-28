<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Core\Search\Common\LocationEventSubscriber;

use Ibexa\Contracts\Core\Persistence\Content as SPIContent;
use Ibexa\Contracts\Core\Persistence\Content\ContentInfo as SPIContentInfo;
use Ibexa\Contracts\Core\Persistence\Content\Handler as ContentHandler;
use Ibexa\Contracts\Core\Persistence\Content\Location as SPILocation;
use Ibexa\Contracts\Core\Persistence\Content\Location\Handler as LocationHandler;
use Ibexa\Contracts\Core\Persistence\Content\VersionInfo as SPIVersionInfo;
use Ibexa\Contracts\Core\Persistence\Handler as PersistenceHandler;
use Ibexa\Contracts\Core\Repository\Events\Location\CreateLocationEvent;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Contracts\Core\Repository\Values\Content\LocationCreateStruct;
use Ibexa\Core\Repository\Values\Content\Location;
use Ibexa\Core\Search\Common\EventSubscriber\LocationEventSubscriber;
use Ibexa\Core\Search\Legacy\Content\Handler as SearchHandler;
use PHPUnit\Framework\TestCase;

final class LocationEventSubscriberTest extends TestCase
{
    private const EXAMPLE_LOCATION_ID = 54;
    private const EXAMPLE_CONTENT_ID = 56;
    private const EXAMPLE_VERSION_NO = 3;

    /** @var \Ibexa\Core\Search\Legacy\Content\Handler|\PHPUnit\Framework\MockObject\MockObject */
    private $searchHandler;

    /** @var \Ibexa\Contracts\Core\Persistence\Handler|\PHPUnit\Framework\MockObject\MockObject */
    private $persistenceHandler;

    /** @var \Ibexa\Core\Search\Common\EventSubscriber\LocationEventSubscriber */
    private $subscriber;

    protected function setUp(): void
    {
        $this->searchHandler = $this->createMock(SearchHandler::class);
        $this->persistenceHandler = $this->createMock(PersistenceHandler::class);

        $this->subscriber = new LocationEventSubscriber(
            $this->searchHandler,
            $this->persistenceHandler
        );
    }

    public function testOnCreateLocation(): void
    {
        $spiLocation = $this->getExampleSPILocation();
        $spiContent = $this->getExampleSPIContent();

        $this->configurePersistenceHandler($spiContent, $spiLocation);

        $this->searchHandler->expects($this->atLeastOnce())->method('indexContent')->with($spiContent);
        $this->searchHandler->expects($this->atLeastOnce())->method('indexLocation')->with($spiLocation);

        $this->subscriber->onCreateLocation(
            new CreateLocationEvent(
                $this->getExampleAPILocation(),
                $this->getExampleAPIContentInfo(),
                new LocationCreateStruct()
            )
        );
    }

    private function configurePersistenceHandler(SPIContent $spiContent, SPILocation $spiLocation): void
    {
        $contentHandler = $this->createMock(ContentHandler::class);
        $contentHandler
            ->method('loadContentInfo')
            ->with(self::EXAMPLE_CONTENT_ID)
            ->willReturn($this->getExampleSPIContentInfo());

        $contentHandler
            ->method('load')
            ->with(self::EXAMPLE_CONTENT_ID, self::EXAMPLE_VERSION_NO)
            ->willReturn($spiContent);

        $locationHandler = $this->createMock(LocationHandler::class);
        $locationHandler->method('load')->with(self::EXAMPLE_LOCATION_ID)->willReturn($spiLocation);

        $this->persistenceHandler->method('locationHandler')->willReturn($locationHandler);
        $this->persistenceHandler->method('contentHandler')->willReturn($contentHandler);
    }

    private function getExampleAPIContentInfo(): ContentInfo
    {
        return new ContentInfo([
            'id' => self::EXAMPLE_CONTENT_ID,
            'currentVersionNo' => self::EXAMPLE_VERSION_NO,
        ]);
    }

    private function getExampleAPILocation(): Location
    {
        return new Location(['id' => self::EXAMPLE_LOCATION_ID]);
    }

    private function getExampleSPILocation(): SPILocation
    {
        return new SPILocation([
            'id' => self::EXAMPLE_LOCATION_ID,
        ]);
    }

    private function getExampleSPIContent(): SPIContent
    {
        return new SPIContent([
            'versionInfo' => new SPIVersionInfo([
                'id' => self::EXAMPLE_CONTENT_ID,
                'versionNo' => self::EXAMPLE_VERSION_NO,
            ]),
        ]);
    }

    private function getExampleSPIContentInfo(): SPIContentInfo
    {
        return new SPIContentInfo([
            'id' => self::EXAMPLE_CONTENT_ID,
            'currentVersionNo' => self::EXAMPLE_VERSION_NO,
        ]);
    }
}

class_alias(LocationEventSubscriberTest::class, 'eZ\Publish\Core\Search\Tests\Common\LocationEventSubscriber\LocationEventSubscriberTest');
