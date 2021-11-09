<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Core\Repository\Values\Content;

use Ibexa\Core\Repository\Values\Content\VersionInfo;
use PHPUnit\Framework\TestCase;

class VersionInfoTest extends TestCase
{
    public function testIsDraft()
    {
        $versionInfo = $this->createVersionInfoWithStatus(VersionInfo::STATUS_DRAFT);
        self::assertTrue($versionInfo->isDraft());

        $versionInfo = $this->createVersionInfoWithStatus(VersionInfo::STATUS_ARCHIVED);
        self::assertFalse($versionInfo->isDraft());
        $versionInfo = $this->createVersionInfoWithStatus(VersionInfo::STATUS_PUBLISHED);
        self::assertFalse($versionInfo->isDraft());
    }

    public function testIsPublished()
    {
        $versionInfo = $this->createVersionInfoWithStatus(VersionInfo::STATUS_PUBLISHED);
        self::assertTrue($versionInfo->isPublished());

        $versionInfo = $this->createVersionInfoWithStatus(VersionInfo::STATUS_DRAFT);
        self::assertFalse($versionInfo->isPublished());
        $versionInfo = $this->createVersionInfoWithStatus(VersionInfo::STATUS_ARCHIVED);
        self::assertFalse($versionInfo->isPublished());
    }

    public function testIsArchived()
    {
        $versionInfo = $this->createVersionInfoWithStatus(VersionInfo::STATUS_ARCHIVED);
        self::assertTrue($versionInfo->isArchived());

        $versionInfo = $this->createVersionInfoWithStatus(VersionInfo::STATUS_DRAFT);
        self::assertFalse($versionInfo->isArchived());
        $versionInfo = $this->createVersionInfoWithStatus(VersionInfo::STATUS_PUBLISHED);
        self::assertFalse($versionInfo->isArchived());
    }

    private function createVersionInfoWithStatus($status)
    {
        return new VersionInfo([
            'status' => $status,
        ]);
    }
}

class_alias(VersionInfoTest::class, 'eZ\Publish\Core\Repository\Tests\Values\Content\VersionInfoTest');
