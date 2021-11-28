<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Core\Repository\Limitation\PermissionResolver;

use Ibexa\Contracts\Core\Limitation\Target\Version;
use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\Core\Repository\Values\User\Limitation\LocationLimitation;

class LocationLimitationIntegrationTest extends BaseLimitationIntegrationTest
{
    private const LOCATION_ID = 2;

    public function providerForCanUserEditOrPublishContent(): array
    {
        $limitationRoot = new LocationLimitation();
        $limitationRoot->limitationValues = [self::LOCATION_ID];

        return [
            [[$limitationRoot], true],
        ];
    }

    /**
     * @dataProvider providerForCanUserEditOrPublishContent
     *
     * @param array $limitations
     * @param bool $expectedResult
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\ForbiddenException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function testCanUserEditContent(array $limitations, bool $expectedResult): void
    {
        $repository = $this->getRepository();
        $locationService = $repository->getLocationService();

        $location = $locationService->loadLocation(2);

        $this->loginAsEditorUserWithLimitations('content', 'edit', $limitations);

        $this->assertCanUser(
            $expectedResult,
            'content',
            'edit',
            $limitations,
            $location->contentInfo,
            [$location]
        );

        $this->assertCanUser(
            $expectedResult,
            'content',
            'edit',
            $limitations,
            $location->contentInfo,
            [$location, new Version(['allLanguageCodesList' => 'eng-GB'])]
        );
    }

    /**
     * @dataProvider providerForCanUserEditOrPublishContent
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\ForbiddenException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function testCanUserReadTrashedContent(array $limitations, bool $expectedResult): void
    {
        $repository = $this->getRepository();
        $locationService = $repository->getLocationService();

        $location = $locationService->loadLocation(2);

        $this->loginAsEditorUserWithLimitations('content', 'read', $limitations);

        $trashItem = $repository->sudo(
            static function (Repository $repository) use ($location) {
                return $repository->getTrashService()->trash($location);
            }
        );

        $this->assertCanUser(
            $expectedResult,
            'content',
            'read',
            $limitations,
            $trashItem->contentInfo
        );
    }
}

class_alias(LocationLimitationIntegrationTest::class, 'eZ\Publish\API\Repository\Tests\Limitation\PermissionResolver\LocationLimitationIntegrationTest');
