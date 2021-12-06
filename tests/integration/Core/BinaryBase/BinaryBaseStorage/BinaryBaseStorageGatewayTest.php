<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Core\BinaryBase\BinaryBaseStorage;

use Ibexa\Contracts\Core\Persistence\Content\Field;
use Ibexa\Contracts\Core\Persistence\Content\FieldValue;
use Ibexa\Contracts\Core\Persistence\Content\VersionInfo;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Contracts\Core\Test\Persistence\Fixture\FixtureImporter;
use Ibexa\Contracts\Core\Test\Persistence\Fixture\YamlFixture;
use Ibexa\Core\FieldType\BinaryBase\BinaryBaseStorage\Gateway as BinaryBaseStorageGateway;
use Ibexa\Core\FieldType\BinaryFile\BinaryFileStorage\Gateway\DoctrineStorage;
use Ibexa\Tests\Integration\Core\BaseCoreFieldTypeIntegrationTest;

/**
 * BinaryBase Field Type external storage gateway tests.
 */
class BinaryBaseStorageGatewayTest extends BaseCoreFieldTypeIntegrationTest
{
    protected function setUp(): void
    {
        parent::setUp();

        $importer = new FixtureImporter($this->getDatabaseConnection());
        $importer->import(new YamlFixture(__DIR__ . '/_fixtures/ezbinaryfile.yaml'));
    }

    protected function getGateway(): BinaryBaseStorageGateway
    {
        return new DoctrineStorage($this->getDatabaseConnection());
    }

    public function testGetFileReferenceWithFixture(): void
    {
        $data = $this->getGateway()->getFileReferenceData(10, 1);

        $expected = [
            'id' => 'image/a6bbf351175ad9c2f27e5b17c2c5d105.png',
            'mimeType' => 'image/png',
            'fileName' => 'test.png',
            'downloadCount' => 0,
        ];

        self::assertEquals($expected, $data);
    }

    public function testStoreFileReference(): void
    {
        $field = new Field();
        $field->id = 123;
        $field->fieldDefinitionId = 231;
        $field->type = 'ezbinaryfile';
        $field->versionNo = 1;
        $field->value = new FieldValue([
            'externalData' => [
                 'id' => 'image/809c753a26e11f363cd8c14d824d162a.jpg',
                 'path' => '/tmp/phpR4tNSI',
                 'inputUri' => '/tmp/phpR4tNSI',
                 'fileName' => '1.jpg',
                 'fileSize' => '372949',
                 'mimeType' => 'image/jpg',
                 'uri' => '/admin/content/download/75/320?version=1',
                 'downloadCount' => 0,
            ],
        ]);

        $versionInfo = new VersionInfo([
            'contentInfo' => new ContentInfo([
                'id' => 1,
            ]),
            'versionNo' => 1,
        ]);

        $this->getGateway()->storeFileReference($versionInfo, $field);

        $data = $this->getGateway()->getFileReferenceData(123, 1);

        $expected = [
            'id' => 'image/809c753a26e11f363cd8c14d824d162a.jpg',
            'mimeType' => 'image/jpg',
            'fileName' => '1.jpg',
            'downloadCount' => 0,
        ];

        self::assertEquals($expected, $data);
    }
}

class_alias(BinaryBaseStorageGatewayTest::class, 'eZ\Publish\Core\FieldType\Tests\Integration\BinaryBase\BinaryBaseStorage\BinaryBaseStorageGatewayTest');
