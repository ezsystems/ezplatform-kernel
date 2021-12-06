<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Integration\Core\Repository;

use Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException;

/**
 * Test case for operations in the LanguageService using in memory storage.
 *
 * @covers \Ibexa\Contracts\Core\Repository\LanguageService
 * @depends Ibexa\Tests\Integration\Core\Repository\UserServiceTest::testLoadUser
 * @group integration
 * @group authorization
 */
class LanguageServiceAuthorizationTest extends BaseTest
{
    /**
     * Test for the createLanguage() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\LanguageService::createLanguage()
     * @depends testCreateLanguage
     */
    public function testCreateLanguageThrowsUnauthorizedException()
    {
        $this->expectException(UnauthorizedException::class);

        $repository = $this->getRepository();
        $permissionResolver = $repository->getPermissionResolver();

        $anonymousUserId = $this->generateId('user', 10);
        /* BEGIN: Use Case */
        // $anonymousUserId is the ID of the "Anonymous" user in a eZ
        // Publish demo installation.
        $userService = $repository->getUserService();
        $languageService = $repository->getContentLanguageService();

        $languageCreate = $languageService->newLanguageCreateStruct();
        $languageCreate->enabled = true;
        $languageCreate->name = 'English (New Zealand)';
        $languageCreate->languageCode = 'eng-NZ';

        // Set anonymous user
        $permissionResolver->setCurrentUserReference($userService->loadUser($anonymousUserId));

        // This call will fail with a "UnauthorizedException"
        $languageService->createLanguage($languageCreate);
        /* END: Use Case */
    }

    /**
     * Test for the updateLanguageName() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\LanguageService::updateLanguageName()
     * @depends testUpdateLanguageName
     */
    public function testUpdateLanguageNameThrowsUnauthorizedException()
    {
        $this->expectException(UnauthorizedException::class);

        $repository = $this->getRepository();
        $permissionResolver = $repository->getPermissionResolver();

        $anonymousUserId = $this->generateId('user', 10);
        /* BEGIN: Use Case */
        // $anonymousUserId is the ID of the "Anonymous" user in a eZ
        // Publish demo installation.
        $userService = $repository->getUserService();
        $languageService = $repository->getContentLanguageService();

        $languageCreate = $languageService->newLanguageCreateStruct();
        $languageCreate->enabled = false;
        $languageCreate->name = 'English';
        $languageCreate->languageCode = 'eng-NZ';

        $languageId = $languageService->createLanguage($languageCreate)->id;

        $language = $languageService->loadLanguageById($languageId);

        // Set anonymous user
        $permissionResolver->setCurrentUserReference($userService->loadUser($anonymousUserId));

        // This call will fail with a "UnauthorizedException"
        $languageService->updateLanguageName($language, 'New language name.');
        /* END: Use Case */
    }

    /**
     * Test for the enableLanguage() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\LanguageService::enableLanguage()
     * @depends testEnableLanguage
     */
    public function testEnableLanguageThrowsUnauthorizedException()
    {
        $this->expectException(UnauthorizedException::class);

        $repository = $this->getRepository();
        $permissionResolver = $repository->getPermissionResolver();

        $anonymousUserId = $this->generateId('user', 10);
        /* BEGIN: Use Case */
        // $anonymousUserId is the ID of the "Anonymous" user in a eZ
        // Publish demo installation.
        $userService = $repository->getUserService();
        $languageService = $repository->getContentLanguageService();

        $languageCreate = $languageService->newLanguageCreateStruct();
        $languageCreate->enabled = false;
        $languageCreate->name = 'English';
        $languageCreate->languageCode = 'eng-NZ';

        $language = $languageService->createLanguage($languageCreate);

        // Set anonymous user
        $permissionResolver->setCurrentUserReference($userService->loadUser($anonymousUserId));

        // This call will fail with a "UnauthorizedException"
        $languageService->enableLanguage($language);
        /* END: Use Case */
    }

    /**
     * Test for the disableLanguage() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\LanguageService::disableLanguage()
     * @depends testDisableLanguage
     */
    public function testDisableLanguageThrowsUnauthorizedException()
    {
        $this->expectException(UnauthorizedException::class);

        $repository = $this->getRepository();
        $permissionResolver = $repository->getPermissionResolver();

        $anonymousUserId = $this->generateId('user', 10);
        /* BEGIN: Use Case */
        // $anonymousUserId is the ID of the "Anonymous" user in a eZ
        // Publish demo installation.
        $userService = $repository->getUserService();
        $languageService = $repository->getContentLanguageService();

        $languageCreate = $languageService->newLanguageCreateStruct();
        $languageCreate->enabled = true;
        $languageCreate->name = 'English';
        $languageCreate->languageCode = 'eng-NZ';

        $language = $languageService->createLanguage($languageCreate);

        // Set anonymous user
        $permissionResolver->setCurrentUserReference($userService->loadUser($anonymousUserId));

        // This call will fail with a "UnauthorizedException"
        $languageService->disableLanguage($language);
        /* END: Use Case */
    }

    /**
     * Test for the deleteLanguage() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\LanguageService::deleteLanguage()
     * @depends testDeleteLanguage
     */
    public function testDeleteLanguageThrowsUnauthorizedException()
    {
        $this->expectException(UnauthorizedException::class);

        $repository = $this->getRepository();
        $permissionResolver = $repository->getPermissionResolver();

        $anonymousUserId = $this->generateId('user', 10);
        /* BEGIN: Use Case */
        // $anonymousUserId is the ID of the "Anonymous" user in a eZ
        // Publish demo installation.
        $userService = $repository->getUserService();
        $languageService = $repository->getContentLanguageService();

        $languageCreateEnglish = $languageService->newLanguageCreateStruct();
        $languageCreateEnglish->enabled = false;
        $languageCreateEnglish->name = 'English';
        $languageCreateEnglish->languageCode = 'eng-NZ';

        $language = $languageService->createLanguage($languageCreateEnglish);

        // Set anonymous user
        $permissionResolver->setCurrentUserReference($userService->loadUser($anonymousUserId));

        // This call will fail with a "UnauthorizedException"
        $languageService->deleteLanguage($language);
        /* END: Use Case */
    }
}

class_alias(LanguageServiceAuthorizationTest::class, 'eZ\Publish\API\Repository\Tests\LanguageServiceAuthorizationTest');
