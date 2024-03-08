<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Core\Limitation;

use eZ\Publish\API\Repository\Tests\Limitation\PermissionResolver\BaseLimitationIntegrationTest;
use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use eZ\Publish\API\Repository\Values\Content\Search\SearchHit;
use eZ\Publish\API\Repository\Values\User\Limitation\ContentTypeLimitation;
use eZ\Publish\API\Repository\Values\User\Limitation\LocationLimitation;
use eZ\Publish\API\Repository\Values\User\Limitation\UserGroupLimitation;

final class UserGroupLimitationTest extends BaseLimitationIntegrationTest
{
    private const FOLDER_CONTENT_TYPE_ID = 1;

    public function testHasUserWithUserGroupLimitationAccessToCreatedLocations(): void
    {
        $repository = $this->getRepository();

        $user = $this->createUserWithPolicies('test_user', $this->getPermissions());
        $userGroups = $repository->getUserService()->loadUserGroupsOfUser($user);
        $userGroupIds = array_column($userGroups, 'id');

        $repository->getPermissionResolver()->setCurrentUserReference($user);

        $parentFolder = $this->createFolder(
            ['eng-US' => 'Parent folder'],
            2
        );
        $childFolder = $this->createFolder(
            ['eng-US' => 'Child folder'],
            $parentFolder->contentInfo->getMainLocationId()
        );

        $this->refreshSearch($repository);

        $query = new LocationQuery();
        $query->filter = new Criterion\LogicalAnd([
            new Criterion\ContentTypeId(self::FOLDER_CONTENT_TYPE_ID),
            new Criterion\UserMetadata('group', 'in', $userGroupIds),
        ]);

        $results = $repository->getSearchService()->findLocations($query)->searchHits;
        $resultLocationIds = array_map(static function (SearchHit $hit): int {
            /** @var \eZ\Publish\API\Repository\Values\Content\Location $location */
            $location = $hit->valueObject;

            return $location->id;
        }, $results);

        self::assertContains($parentFolder->contentInfo->getMainLocationId(), $resultLocationIds);
        self::assertContains($childFolder->contentInfo->getMainLocationId(), $resultLocationIds);
    }

    /**
     * @return array<array<string, mixed>>
     */
    private function getPermissions(): array
    {
        return [
            [
                'module' => 'content',
                'function' => 'create',
            ],
            [
                'module' => 'content',
                'function' => 'publish',
            ],
            [
                'module' => 'content',
                'function' => 'read',
                'limitations' => [
                    new LocationLimitation(['limitationValues' => [2]]),
                ],
            ],
            [
                'module' => 'content',
                'function' => 'read',
                'limitations' => [
                    new ContentTypeLimitation(['limitationValues' => [self::FOLDER_CONTENT_TYPE_ID]]),
                    new UserGroupLimitation(['limitationValues' => [1]]),
                ],
            ],
        ];
    }
}
