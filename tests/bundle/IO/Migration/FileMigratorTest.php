<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\IO\Migration;

use eZ\Bundle\EzPublishIOBundle\ApiLoader\HandlerRegistry;
use eZ\Bundle\EzPublishIOBundle\Migration\FileMigrator\FileMigrator;
use eZ\Publish\Core\IO\IOBinarydataHandler;
use eZ\Publish\Core\IO\IOMetadataHandler;
use eZ\Publish\SPI\IO\BinaryFile;
use PHPUnit\Framework\TestCase;

final class FileMigratorTest extends TestCase
{
    /** @var \eZ\Bundle\EzPublishIOBundle\ApiLoader\HandlerRegistry|\PHPUnit\Framework\MockObject\MockObject */
    private $metadataHandlerRegistry;

    /** @var \eZ\Bundle\EzPublishIOBundle\ApiLoader\HandlerRegistry|\PHPUnit\Framework\MockObject\MockObject */
    private $binaryHandlerRegistry;

    /** @var \eZ\Bundle\EzPublishIOBundle\Migration\FileMigratorInterface */
    private $fileMigrator;

    /** @var \eZ\Publish\Core\IO\IOMetadataHandler\Flysystem */
    private $metadataFlysystem;

    /** @var \eZ\Publish\Core\IO\IOMetadataHandler\LegacyDFSCluster */
    private $metadataLegacyDFSCluster;

    /** @var \eZ\Publish\Core\IO\IOBinarydataHandler\Flysystem */
    private $binaryFlysystemFrom;

    /** @var \eZ\Publish\Core\IO\IOBinarydataHandler\Flysystem */
    private $binaryFlysystemTo;

    protected function setUp(): void
    {
        parent::setUp();

        $this->metadataHandlerRegistry = $this->createMock(HandlerRegistry::class);
        $this->binaryHandlerRegistry = $this->createMock(HandlerRegistry::class);

        $this->metadataFlysystem = $this->createMock(IOMetadataHandler\Flysystem::class);
        $this->metadataLegacyDFSCluster = $this->createMock(IOMetadataHandler\LegacyDFSCluster::class);

        $this->binaryFlysystemFrom = $this->createMock(IOBinarydataHandler\Flysystem::class);
        $this->binaryFlysystemTo = $this->createMock(IOBinarydataHandler\Flysystem::class);

        $this->fileMigrator = new FileMigrator($this->metadataHandlerRegistry, $this->binaryHandlerRegistry);
    }

    public function testMigrateFile(): void
    {
        $this->metadataHandlerRegistry
            ->expects(self::exactly(2))
            ->method('getConfiguredHandler')
            ->withConsecutive(
                ['default'],
                ['dfs']
            )
            ->willReturnOnConsecutiveCalls(
                $this->metadataFlysystem,
                $this->metadataLegacyDFSCluster
            );

        $this->binaryHandlerRegistry
            ->expects(self::exactly(2))
            ->method('getConfiguredHandler')
            ->withConsecutive(
                ['default'],
                ['nfs']
            )
            ->willReturnOnConsecutiveCalls(
                $this->binaryFlysystemFrom,
                $this->binaryFlysystemTo
            );

        $this->fileMigrator->setIODataHandlersByIdentifiers('default', 'default', 'dfs', 'nfs');

        $binaryFile = new BinaryFile();
        $binaryFile->id = '1234.jpg';
        $binaryFile->mtime = new \DateTime();
        $binaryFile->size = 12345;
        $binaryFile->uri = '1/1234.jpg';

        $this->binaryFlysystemTo
            ->expects(self::once())
            ->method('create');

        $this->metadataLegacyDFSCluster
            ->expects(self::once())
            ->method('create');

        $flag = $this->fileMigrator->migrateFile($binaryFile);

        self::assertTrue($flag);
    }

    public function testSkipMigratingIfSameHandlers(): void
    {
        $this->metadataHandlerRegistry
            ->expects(self::exactly(2))
            ->method('getConfiguredHandler')
            ->withConsecutive(
                ['default'],
                ['default']
            )
            ->willReturnOnConsecutiveCalls(
                $this->metadataFlysystem,
                $this->metadataFlysystem
            );

        $this->binaryHandlerRegistry
            ->expects(self::exactly(2))
            ->method('getConfiguredHandler')
            ->withConsecutive(
                ['default'],
                ['default']
            )
            ->willReturnOnConsecutiveCalls(
                $this->binaryFlysystemFrom,
                $this->binaryFlysystemFrom
            );

        $this->fileMigrator->setIODataHandlersByIdentifiers('default', 'default', 'default', 'default');

        $binaryFile = new BinaryFile();

        $this->binaryFlysystemFrom
            ->expects(self::never())
            ->method('create');

        $this->metadataFlysystem
            ->expects(self::never())
            ->method('create');

        $flag = $this->fileMigrator->migrateFile($binaryFile);

        self::assertTrue($flag);
    }
}
