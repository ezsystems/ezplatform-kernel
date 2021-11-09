<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Integration\Core\Repository;

use Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException;

class URLAliasServiceAuthorizationTest extends BaseTest
{
    /**
     * Test for the createUrlAlias() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\URLAliasService::createUrlAlias()
     * @depends Ibexa\Tests\Integration\Core\Repository\URLAliasServiceTest::testCreateUrlAlias
     */
    public function testCreateUrlAliasThrowsUnauthorizedException()
    {
        $this->expectException(UnauthorizedException::class);

        $repository = $this->getRepository();

        $anonymousUserId = $this->generateId('user', 10);
        $parentLocationId = $this->generateId('location', 2);
        /* BEGIN: Use Case */
        // $anonymousUserId is the ID of the "Anonymous" user in a eZ
        // Publish demo installation.
        // $locationId is the ID of an existing location
        $userService = $repository->getUserService();
        $urlAliasService = $repository->getURLAliasService();
        $locationService = $repository->getLocationService();

        $content = $this->createFolder(['eng-GB' => 'Foo'], $parentLocationId);
        $location = $locationService->loadLocation($content->contentInfo->mainLocationId);

        $anonymousUser = $userService->loadUser($anonymousUserId);
        $repository->getPermissionResolver()->setCurrentUserReference($anonymousUser);

        $this->expectException(UnauthorizedException::class);
        $urlAliasService->createUrlAlias($location, '/Home/My-New-Site', 'eng-US');
        /* END: Use Case */
    }

    /**
     * Test for the createGlobalUrlAlias() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\URLAliasService::createGlobalUrlAlias()
     * @depends Ibexa\Tests\Integration\Core\Repository\URLAliasServiceTest::testCreateGlobalUrlAlias
     */
    public function testCreateGlobalUrlAliasThrowsUnauthorizedException()
    {
        $this->expectException(UnauthorizedException::class);

        $repository = $this->getRepository();

        $anonymousUserId = $this->generateId('user', 10);
        /* BEGIN: Use Case */
        // $anonymousUserId is the ID of the "Anonymous" user in a eZ
        // Publish demo installation.
        $userService = $repository->getUserService();
        $urlAliasService = $repository->getURLAliasService();

        $anonymousUser = $userService->loadUser($anonymousUserId);
        $repository->getPermissionResolver()->setCurrentUserReference($anonymousUser);

        // This call will fail with an UnauthorizedException
        $urlAliasService->createGlobalUrlAlias('module:content/search?SearchText=eZ', '/Home/My-New-Site', 'eng-US');
        /* END: Use Case */
    }

    /**
     * Test for the removeAliases() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\URLAliasService::removeAliases()
     * @depends Ibexa\Tests\Integration\Core\Repository\URLAliasServiceTest::testRemoveAliases
     */
    public function testRemoveAliasesThrowsUnauthorizedException()
    {
        $this->expectException(UnauthorizedException::class);

        $repository = $this->getRepository();
        $anonymousUserId = $this->generateId('user', 10);

        $locationService = $repository->getLocationService();
        $someLocation = $locationService->loadLocation(
            $this->generateId('location', 12)
        );

        /* BEGIN: Use Case */
        // $someLocation contains a location with automatically generated
        // aliases assigned
        // $anonymousUserId is the ID of the "Anonymous" user in a eZ
        $urlAliasService = $repository->getURLAliasService();
        $userService = $repository->getUserService();

        $anonymousUser = $userService->loadUser($anonymousUserId);
        $repository->getPermissionResolver()->setCurrentUserReference($anonymousUser);

        $initialAliases = $urlAliasService->listLocationAliases($someLocation);

        // This call will fail with an UnauthorizedException
        $urlAliasService->removeAliases($initialAliases);
        /* END: Use Case */
    }
}

class_alias(URLAliasServiceAuthorizationTest::class, 'eZ\Publish\API\Repository\Tests\URLAliasServiceAuthorizationTest');
