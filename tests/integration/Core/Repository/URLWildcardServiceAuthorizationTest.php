<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Integration\Core\Repository;

use Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException;

/**
 * Test case for operations in the URLWildcardService.
 *
 * @covers \Ibexa\Contracts\Core\Repository\URLWildcardService
 * @group integration
 * @group authorization
 */
class URLWildcardServiceAuthorizationTest extends BaseTest
{
    /**
     * @covers \Ibexa\Contracts\Core\Repository\URLWildcardService::create
     * @depends Ibexa\Tests\Integration\Core\Repository\URLWildcardServiceTest::testCreate
     */
    public function testCreateThrowsUnauthorizedException(): void
    {
        $this->expectException(UnauthorizedException::class);

        $repository = $this->getRepository();

        $anonymousUserId = $this->generateId('user', 10);
        /* BEGIN: Use Case */
        // $anonymousUserId is the ID of the "Anonymous" user in a eZ
        // Publish demo installation.

        $userService = $repository->getUserService();
        $urlWildcardService = $repository->getURLWildcardService();

        $repository->getPermissionResolver()->setCurrentUserReference($userService->loadUser($anonymousUserId));

        $this->expectException(UnauthorizedException::class);
        $urlWildcardService->create('/articles/*', '/content/{1}');
        /* END: Use Case */
    }

    /**
     * Test for the remove() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\URLWildcardService::remove()
     * @depends Ibexa\Tests\Integration\Core\Repository\URLWildcardServiceTest::testRemove
     */
    public function testRemoveThrowsUnauthorizedException()
    {
        $this->expectException(UnauthorizedException::class);

        $repository = $this->getRepository();

        $anonymousUserId = $this->generateId('user', 10);
        /* BEGIN: Use Case */
        // $anonymousUserId is the ID of the "Anonymous" user in a eZ
        // Publish demo installation.
        $userService = $repository->getUserService();
        $urlWildcardService = $repository->getURLWildcardService();

        // Create a new url wildcard
        $urlWildcardId = $urlWildcardService->create('/articles/*', '/content/{1}')->id;

        $repository->getPermissionResolver()->setCurrentUserReference($userService->loadUser($anonymousUserId));

        // Load newly created url wildcard
        $urlWildcard = $urlWildcardService->load($urlWildcardId);

        $this->expectException(UnauthorizedException::class);
        $urlWildcardService->remove($urlWildcard);
        /* END: Use Case */
    }
}

class_alias(URLWildcardServiceAuthorizationTest::class, 'eZ\Publish\API\Repository\Tests\URLWildcardServiceAuthorizationTest');
