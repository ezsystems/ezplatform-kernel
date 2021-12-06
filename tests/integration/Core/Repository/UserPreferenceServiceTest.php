<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Core\Repository;

use Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException;
use Ibexa\Contracts\Core\Repository\Values\UserPreference\UserPreference;
use Ibexa\Contracts\Core\Repository\Values\UserPreference\UserPreferenceList;
use Ibexa\Contracts\Core\Repository\Values\UserPreference\UserPreferenceSetStruct;

/**
 * Test case for the UserPreferenceService.
 *
 * @covers \Ibexa\Contracts\Core\Repository\UserPreferenceService
 */
class UserPreferenceServiceTest extends BaseTest
{
    /**
     * @covers \Ibexa\Contracts\Core\Repository\UserPreferenceService::loadUserPreferences()
     */
    public function testLoadUserPreferences()
    {
        $repository = $this->getRepository();

        /* BEGIN: Use Case */
        $userPreferenceService = $repository->getUserPreferenceService();
        $userPreferenceList = $userPreferenceService->loadUserPreferences(0, 25);
        /* END: Use Case */

        $this->assertInstanceOf(UserPreferenceList::class, $userPreferenceList);
        $this->assertIsArray($userPreferenceList->items);
        $this->assertIsInt($userPreferenceList->totalCount);
        $this->assertEquals(5, $userPreferenceList->totalCount);
    }

    /**
     * @covers \Ibexa\Contracts\Core\Repository\UserPreferenceService::getUserPreference()
     */
    public function testGetUserPreference()
    {
        $repository = $this->getRepository();

        $userPreferenceName = 'setting_1';

        /* BEGIN: Use Case */
        $userPreferenceService = $repository->getUserPreferenceService();
        // $userPreferenceName is the name of an existing preference
        $userPreference = $userPreferenceService->getUserPreference($userPreferenceName);
        /* END: Use Case */

        $this->assertInstanceOf(UserPreference::class, $userPreference);
        $this->assertEquals($userPreferenceName, $userPreference->name);
    }

    /**
     * @covers \Ibexa\Contracts\Core\Repository\UserPreferenceService::setUserPreference()
     * @depends testGetUserPreference
     */
    public function testSetUserPreference()
    {
        $repository = $this->getRepository();

        $userPreferenceName = 'timezone';

        /* BEGIN: Use Case */
        $userPreferenceService = $repository->getUserPreferenceService();

        $setStruct = new UserPreferenceSetStruct([
            'name' => $userPreferenceName,
            'value' => 'America/New_York',
        ]);

        $userPreferenceService->setUserPreference([$setStruct]);
        $userPreference = $userPreferenceService->getUserPreference($userPreferenceName);
        /* END: Use Case */

        $this->assertInstanceOf(UserPreference::class, $userPreference);
        $this->assertEquals($userPreferenceName, $userPreference->name);
    }

    /**
     * @covers \Ibexa\Contracts\Core\Repository\UserPreferenceService::setUserPreference()
     * @depends testSetUserPreference
     */
    public function testSetUserPreferenceThrowsInvalidArgumentExceptionOnInvalidValue()
    {
        $this->expectException(InvalidArgumentException::class);

        $repository = $this->getRepository();

        /* BEGIN: Use Case */
        $userPreferenceService = $repository->getUserPreferenceService();

        $setStruct = new UserPreferenceSetStruct([
            'name' => 'setting',
            'value' => new \stdClass(),
        ]);

        // This call will fail because value is not specified
        $userPreferenceService->setUserPreference([$setStruct]);
        /* END: Use Case */
    }

    /**
     * @covers \Ibexa\Contracts\Core\Repository\UserPreferenceService::setUserPreference()
     * @depends testSetUserPreference
     */
    public function testSetUserPreferenceThrowsInvalidArgumentExceptionOnEmptyName()
    {
        $this->expectException(InvalidArgumentException::class);

        $repository = $this->getRepository();

        /* BEGIN: Use Case */
        $userPreferenceService = $repository->getUserPreferenceService();

        $setStruct = new UserPreferenceSetStruct([
            'value' => 'value',
        ]);

        // This call will fail because value is not specified
        $userPreferenceService->setUserPreference([$setStruct]);
        /* END: Use Case */
    }

    /**
     * @covers \Ibexa\Contracts\Core\Repository\UserPreferenceService::getUserPreferenceCount()
     */
    public function testGetUserPreferenceCount()
    {
        $repository = $this->getRepository();

        /* BEGIN: Use Case */
        $userPreferenceService = $repository->getUserPreferenceService();
        $userPreferenceCount = $userPreferenceService->getUserPreferenceCount();
        /* END: Use Case */

        $this->assertEquals(5, $userPreferenceCount);
    }
}

class_alias(UserPreferenceServiceTest::class, 'eZ\Publish\API\Repository\Tests\UserPreferenceServiceTest');
