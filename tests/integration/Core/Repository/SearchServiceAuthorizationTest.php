<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Integration\Core\Repository;

use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Core\Repository\Values\Content\Content;

/**
 * Test case for operations in the SearchService.
 *
 * @covers \Ibexa\Contracts\Core\Repository\SearchService
 * @depends Ibexa\Tests\Integration\Core\Repository\UserServiceTest::testLoadUser
 * @group integration
 * @group authorization
 */
class SearchServiceAuthorizationTest extends BaseTest
{
    /**
     * Test for the findContent() method but with anonymous user.
     *
     * @covers \Ibexa\Contracts\Core\Repository\SearchService::findContent()
     * @depends Ibexa\Tests\Integration\Core\Repository\SearchServiceTest::testFindContentFiltered
     */
    public function testFindContent()
    {
        $repository = $this->getRepository();
        $permissionResolver = $repository->getPermissionResolver();

        $anonymousUserId = $this->generateId('user', 10);
        /* BEGIN: Use Case */
        // $anonymousUserId is the ID of the "Anonymous" user in a eZ
        // Publish demo installation.
        $searchService = $repository->getSearchService();
        $userService = $repository->getUserService();

        // Set anonymous user
        $permissionResolver->setCurrentUserReference($userService->loadUser($anonymousUserId));

        // Should return Content with location id: 2 as the anonymous user should have access to standard section
        $searchResult = $searchService->findContent(new Query(['filter' => new Criterion\LocationId(2)]));
        /* END: Use Case */

        self::assertEquals(1, $searchResult->totalCount, 'Search query should return totalCount of 1');
        self::assertNotEmpty($searchResult->searchHits, '$searchResult->searchHits should not be empty');
        self::assertEquals('Home', $searchResult->searchHits[0]->valueObject->contentInfo->name);
    }

    /**
     * Test for the findContent() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\SearchService::findContent()
     * @depends Ibexa\Tests\Integration\Core\Repository\SearchServiceTest::testFindContentFiltered
     */
    public function testFindContentEmptyResult()
    {
        $repository = $this->getRepository();
        $permissionResolver = $repository->getPermissionResolver();

        $anonymousUserId = $this->generateId('user', 10);
        /* BEGIN: Use Case */
        // $anonymousUserId is the ID of the "Anonymous" user in a eZ
        // Publish demo installation.
        $searchService = $repository->getSearchService();
        $userService = $repository->getUserService();

        // Set anonymous user
        $permissionResolver->setCurrentUserReference($userService->loadUser($anonymousUserId));

        // This call will return an empty search result
        $searchResult = $searchService->findContent(new Query(['filter' => new Criterion\LocationId(5)]));
        /* END: Use Case */

        self::assertEmpty(
            $searchResult->searchHits,
            'Expected Not Found exception, got content with name: ' .
            (!empty($searchResult->searchHits) ? $searchResult->searchHits[0]->valueObject->contentInfo->name : '')
        );
        self::assertEquals(0, $searchResult->totalCount, 'Search query should return totalCount of 0');
    }

    /**
     * Test for the findSingle() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\SearchService::findSingle()
     * @depends Ibexa\Tests\Integration\Core\Repository\SearchServiceTest::testFindSingle
     */
    public function testFindSingleThrowsNotFoundException()
    {
        $this->expectException(NotFoundException::class);

        $repository = $this->getRepository();
        $permissionResolver = $repository->getPermissionResolver();

        $anonymousUserId = $this->generateId('user', 10);
        /* BEGIN: Use Case */
        // $anonymousUserId is the ID of the "Anonymous" user in a eZ
        // Publish demo installation.
        $searchService = $repository->getSearchService();
        $userService = $repository->getUserService();

        // Set anonymous user
        $permissionResolver->setCurrentUserReference($userService->loadUser($anonymousUserId));

        // This call will fail with a "NotFoundException" as user does not have access
        $searchService->findSingle(
            new Criterion\ContentId(
                [4]
            )
        );
        /* END: Use Case */
    }

    /**
     * Test for the findContent() method, verifying disabling permissions.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentService::findContent($query, $languageFilter, $filterOnUserPermissions)
     * @depends Ibexa\Tests\Integration\Core\Repository\SearchServiceAuthorizationTest::testFindContent
     */
    public function testFindContentWithUserPermissionFilter()
    {
        $repository = $this->getRepository();
        $permissionResolver = $repository->getPermissionResolver();

        /* BEGIN: Use Case */
        $user = $this->createMediaUserVersion1();

        // Set new media editor as current user
        $permissionResolver->setCurrentUserReference($user);

        $searchService = $repository->getSearchService();

        // Search for "Admin Users" user group which user normally does not have access to
        $query = new Query();
        $query->filter = new Criterion\LogicalAnd(
            [
                new Criterion\ContentId(12),
            ]
        );

        // Search for matching content
        $searchResultWithoutPermissions = $searchService->findContent($query, [], false);

        // Search for matching content
        $searchResultWithPermissions = $searchService->findContent($query, []);
        /* END: Use Case */

        $this->assertEquals(1, $searchResultWithoutPermissions->totalCount);
        $this->assertEquals(0, $searchResultWithPermissions->totalCount);
    }

    /**
     * Test for the findSingle() method disabling permission filtering.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentService::findSingle($query, $languageFilter, $filterOnUserPermissions)
     * @depends Ibexa\Tests\Integration\Core\Repository\SearchServiceAuthorizationTest::testFindContent
     */
    public function testFindSingleWithUserPermissionFilter()
    {
        $repository = $this->getRepository();
        $permissionResolver = $repository->getPermissionResolver();

        /* BEGIN: Use Case */
        $user = $this->createMediaUserVersion1();

        // Set new media editor as current user
        $permissionResolver->setCurrentUserReference($user);

        // Search for "Admin Users" user group which user normally does not have access to
        $content = $repository->getSearchService()->findSingle(
            new Criterion\ContentId(12),
            [],
            false
        );
        /* END: Use Case */

        self::assertInstanceOf(
            Content::class,
            $content
        );
    }

    /**
     * Test for the findSingle() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\ContentService::findSingle($query, $languageFilter, $filterOnUserPermissions)
     * @depends Ibexa\Tests\Integration\Core\Repository\SearchServiceAuthorizationTest::testFindContent
     */
    public function testFindSingleThrowsNotFoundExceptionWithUserPermissionFilter()
    {
        $this->expectException(NotFoundException::class);

        $repository = $this->getRepository();
        $permissionResolver = $repository->getPermissionResolver();

        /* BEGIN: Use Case */
        $user = $this->createMediaUserVersion1();

        // Set new media editor as current user
        $permissionResolver->setCurrentUserReference($user);

        $searchService = $repository->getSearchService();

        // This call will fail with a "NotFoundException", because the current
        // user has no access to the "Admin Users" user group
        $searchService->findSingle(
            new Criterion\ContentId(12)
        );
        /* END: Use Case */
    }
}

class_alias(SearchServiceAuthorizationTest::class, 'eZ\Publish\API\Repository\Tests\SearchServiceAuthorizationTest');
