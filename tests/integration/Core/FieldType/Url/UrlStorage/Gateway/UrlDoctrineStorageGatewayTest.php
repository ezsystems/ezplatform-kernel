<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Core\FieldType\Url\UrlStorage\Gateway;

use eZ\Publish\Core\FieldType\Tests\Integration\BaseCoreFieldTypeIntegrationTest;
use eZ\Publish\Core\FieldType\Url\UrlStorage\Gateway as UrlStorageGateway;
use eZ\Publish\Core\FieldType\Url\UrlStorage\Gateway\DoctrineStorage;

/**
 * @covers \eZ\Publish\Core\FieldType\Url\UrlStorage\Gateway\DoctrineStorage
 */
final class UrlDoctrineStorageGatewayTest extends BaseCoreFieldTypeIntegrationTest
{
    public function testGetUrlsFromUrlLink(): void
    {
        $gateway = $this->getGateway();

        $urlIds = [];
        $urlIds[] = $gateway->insertUrl('https://ibexa.co/example1');
        $urlIds[] = $gateway->insertUrl('https://ibexa.co/example2');
        $urlIds[] = $gateway->insertUrl('https://ibexa.co/example3');

        $gateway->linkUrl($urlIds[0], 10, 1);
        $gateway->linkUrl($urlIds[1], 10, 1);
        $gateway->linkUrl($urlIds[1], 12, 2);
        $gateway->linkUrl($urlIds[2], 14, 1);

        $urls = $gateway->getUrlsFromUrlLink(10, 1);
        sort($urls);
        self::assertEquals(['https://ibexa.co/example1', 'https://ibexa.co/example2'], $urls, 'Did not get expected urls for field 10');
        self::assertEquals(['https://ibexa.co/example2'], $gateway->getUrlsFromUrlLink(12, 2), 'Did not get expected url for field 12');
        self::assertEquals(['https://ibexa.co/example3'], $gateway->getUrlsFromUrlLink(14, 1), 'Did not get expected url for field 14');
        self::assertEquals([], $gateway->getUrlsFromUrlLink(15, 1), 'Expected no urls for field 15');
    }

    protected function getGateway(): UrlStorageGateway
    {
        return new DoctrineStorage($this->getDatabaseConnection());
    }
}
