<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace eZ\Publish\Core\FieldType\Tests\Url\Gateway;

use eZ\Publish\Core\FieldType\Url\UrlStorage\Gateway;
use eZ\Publish\Core\FieldType\Url\UrlStorage\Gateway\DoctrineStorage;
use eZ\Publish\Core\Persistence\Legacy\Tests\TestCase;

/**
 * @covers \eZ\Publish\Core\FieldType\Url\UrlStorage\Gateway\DoctrineStorage
 */
class DoctrineStorageTest extends TestCase
{
    /**
     * @covers \eZ\Publish\Core\FieldType\Url\UrlStorage\Gateway\DoctrineStorage::getIdUrlMap
     */
    public function testGetIdUrlMap()
    {
        $this->insertDatabaseFixture(__DIR__ . '/_fixtures/urls.php');

        $gateway = $this->getStorageGateway();

        $this->assertEquals(
            [
                23 => '/content/view/sitemap/2',
                24 => '/content/view/tagcloud/2',
            ],
            $gateway->getIdUrlMap(
                [23, 24, 'fake']
            )
        );
    }

    /**
     * @covers \eZ\Publish\Core\FieldType\Url\UrlStorage\Gateway\DoctrineStorage::getUrlIdMap
     */
    public function testGetUrlIdMap()
    {
        $this->insertDatabaseFixture(__DIR__ . '/_fixtures/urls.php');

        $gateway = $this->getStorageGateway();

        $this->assertEquals(
            [
                '/content/view/sitemap/2' => 23,
                '/content/view/tagcloud/2' => 24,
            ],
            $gateway->getUrlIdMap(
                [
                    '/content/view/sitemap/2',
                    '/content/view/tagcloud/2',
                    'fake',
                ]
            )
        );
    }

    /**
     * @covers \eZ\Publish\Core\FieldType\Url\UrlStorage\Gateway\DoctrineStorage::insertUrl
     */
    public function testInsertUrl()
    {
        $gateway = $this->getStorageGateway();

        $url = 'one/two/three';
        $time = time();
        $id = $gateway->insertUrl($url);

        $query = $this->connection->createQueryBuilder();
        $query
            ->select('*')
            ->from('ezurl')
            ->where(
                $query->expr()->eq(
                    $this->connection->quoteIdentifier('id'),
                    ':id'
                )
            )
            ->setParameter('id', $id, \PDO::PARAM_INT)
        ;

        $statement = $query->execute();
        $result = $statement->fetchAllAssociative();

        $expected = [
            [
                'id' => $id,
                'is_valid' => '1',
                'last_checked' => '0',
                'original_url_md5' => md5($url),
                'url' => $url,
            ],
        ];

        $this->assertGreaterThanOrEqual($time, $result[0]['created']);
        $this->assertGreaterThanOrEqual($time, $result[0]['modified']);

        unset($result[0]['created']);
        unset($result[0]['modified']);

        $this->assertEquals($expected, $result);
    }

    /**
     * @covers \eZ\Publish\Core\FieldType\Url\UrlStorage\Gateway\DoctrineStorage::linkUrl
     */
    public function testLinkUrl()
    {
        $gateway = $this->getStorageGateway();

        $urlId = 12;
        $fieldId = 10;
        $versionNo = 1;
        $gateway->linkUrl($urlId, $fieldId, $versionNo);

        $query = $this->connection->createQueryBuilder();
        $query
            ->select('*')
            ->from('ezurl_object_link')
            ->where(
                $query->expr()->eq($this->connection->quoteIdentifier('url_id'), ':urlId')
            )
            ->setParameter(':urlId', $urlId, \PDO::PARAM_INT)
        ;

        $statement = $query->execute();

        $result = $statement->fetchAllAssociative();

        $expected = [
            [
                'contentobject_attribute_id' => $fieldId,
                'contentobject_attribute_version' => $versionNo,
                'url_id' => $urlId,
            ],
        ];

        $this->assertEquals($expected, $result);
    }

    /**
     * @covers \eZ\Publish\Core\FieldType\Url\UrlStorage\Gateway\DoctrineStorage::unlinkUrl
     */
    public function testUnlinkUrl()
    {
        $this->insertDatabaseFixture(__DIR__ . '/_fixtures/urls.php');

        $gateway = $this->getStorageGateway();

        $fieldId = 42;
        $versionNo = 5;
        $gateway->unlinkUrl($fieldId, $versionNo);

        $query = $this->connection->createQueryBuilder();
        $query->select('*')->from('ezurl_object_link');

        $statement = $query->execute();
        $result = $statement->fetchAllAssociative();

        $expected = [
            [
                'contentobject_attribute_id' => 43,
                'contentobject_attribute_version' => 6,
                'url_id' => 24,
            ],
        ];

        $this->assertEquals($expected, $result);

        // Check that orphaned URLs are correctly removed
        $query = $this->connection->createQueryBuilder();
        $query->select('*')->from('ezurl');

        $statement = $query->execute();

        $result = $statement->fetchAllAssociative();

        $expected = [
            [
                'created' => '1343140541',
                'id' => '24',
                'is_valid' => '1',
                'last_checked' => '0',
                'modified' => '1343140541',
                'original_url_md5' => 'c86bcb109d8e70f9db65c803baafd550',
                'url' => '/content/view/tagcloud/2',
            ],
        ];

        $this->assertEquals($expected, $result);
    }

    /**
     * @covers \eZ\Publish\Core\FieldType\Url\UrlStorage\Gateway\DoctrineStorage::getUrlsFromUrlLink
     */
    public function testGetUrlsFromUrlLink()
    {
        $gateway = $this->getStorageGateway();

        $urlIds = [];
        $urlIds[] = $gateway->insertUrl('https://ibexa.co/example1');
        $urlIds[] = $gateway->insertUrl('https://ibexa.co/example2');
        $urlIds[] = $gateway->insertUrl('https://ibexa.co/example3');

        $gateway->linkUrl($urlIds[0], 10, 1);
        $gateway->linkUrl($urlIds[1], 10, 1);
        $gateway->linkUrl($urlIds[1], 12, 2);
        $gateway->linkUrl($urlIds[2], 14, 1);

        self::assertEquals(['https://ibexa.co/example1' => true, 'https://ibexa.co/example2' => true], $gateway->getUrlsFromUrlLink(10, 1), 'Did not get expected urlS for field 10');
        self::assertEquals(['https://ibexa.co/example2' => true], $gateway->getUrlsFromUrlLink(12, 2), 'Did not get expected url for field 12');
        self::assertEquals(['https://ibexa.co/example3' => true], $gateway->getUrlsFromUrlLink(14, 1), 'Did not get expected url for field 14');
        self::assertEquals([], $gateway->getUrlsFromUrlLink(15, 1), 'Expected no urls for field 15');
    }

    protected function getStorageGateway(): Gateway
    {
        if (!isset($this->storageGateway)) {
            $this->storageGateway = new DoctrineStorage($this->getDatabaseConnection());
        }

        return $this->storageGateway;
    }
}
