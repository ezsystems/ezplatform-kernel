<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Core\Search\Common\EventSubscriber;

use eZ\Publish\API\Repository\Events\Trash\DeleteTrashItemEvent;
use eZ\Publish\API\Repository\Values\Content\Trash\TrashItemDeleteResult;
use eZ\Publish\Core\Repository\Values\Content\TrashItem;
use eZ\Publish\Core\Search\Common\EventSubscriber\TrashEventSubscriber;
use eZ\Publish\SPI\Persistence\Content as PersistenceContent;
use eZ\Publish\SPI\Persistence\Content\Handler;
use eZ\Publish\SPI\Persistence\Handler as PersistenceHandler;
use eZ\Publish\SPI\Search\Handler as SearchHandler;
use PHPUnit\Framework\TestCase;

final class TrashEventSubscriberTest extends TestCase
{
    /** @var \eZ\Publish\SPI\Search\Handler&\PHPUnit\Framework\MockObject\MockObject */
    private $searchHandler;

    /** @var \eZ\Publish\SPI\Persistence\Handler&\PHPUnit\Framework\MockObject\MockObject */
    private $persistenceHandler;

    /** @var \eZ\Publish\Core\Search\Common\EventSubscriber\TrashEventSubscriber */
    private $subscriber;

    protected function setUp(): void
    {
        $this->searchHandler = $this->createMock(SearchHandler::class);
        $this->persistenceHandler = $this->createMock(PersistenceHandler::class);

        $this->subscriber = new TrashEventSubscriber(
            $this->searchHandler,
            $this->persistenceHandler
        );
    }

    public function testOnDeleteTrashItem(): void
    {
        $trashItem = new TrashItem(['id' => 12345]);
        $reverseRelationContentId = 12;
        $trashItemDeleteResult = new TrashItemDeleteResult(
            [
                'trashItemId' => $trashItem->id,
                'reverseRelationContentIds' => [$reverseRelationContentId],
            ]
        );

        $this->persistenceHandler
            ->expects(self::once())
            ->method('contentHandler')
            ->willReturn($contentHandler = $this->createMock(Handler::class));

        $contentHandler
            ->expects(self::once())
            ->method('load')
            ->with($reverseRelationContentId)
            ->willReturn($content = new PersistenceContent());

        $this->searchHandler
            ->expects(self::once())
            ->method('indexContent')
            ->with($content);

        $this->subscriber->onDeleteTrashItem(
            new DeleteTrashItemEvent(
                $trashItemDeleteResult,
                $trashItem,
            )
        );
    }
}
