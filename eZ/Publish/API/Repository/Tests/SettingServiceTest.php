<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace eZ\Publish\API\Repository\Tests;

use eZ\Publish\API\Repository\Exceptions\InvalidArgumentException;
use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\SettingService;
use eZ\Publish\API\Repository\Values\Setting\Setting;
use eZ\Publish\API\Repository\Values\Setting\SettingCreateStruct;
use eZ\Publish\API\Repository\Values\Setting\SettingUpdateStruct;

/**
 * Test case for operations in the SettingService using in memory storage.
 *
 * @see \eZ\Publish\API\Repository\SettingService
 * @group integration
 * @group setting
 */
class SettingServiceTest extends BaseTest
{
    /** @var \eZ\Publish\API\Repository\PermissionResolver */
    protected $permissionResolver;

    /** @var \eZ\Publish\API\Repository\SettingService */
    protected $settingService;

    protected function getSettingService(): SettingService
    {
        $container = $this->getSetupFactory()->getServiceContainer();
        /** @var \eZ\Publish\API\Repository\SettingService $settingService */
        $settingService = $container->get('ezpublish.api.service.setting');

        return $settingService;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $repository = $this->getRepository(false);
        $this->permissionResolver = $repository->getPermissionResolver();
    }

    /**
     * Test for the newSettingCreateStruct() method.
     *
     * @see \eZ\Publish\API\Repository\SettingService::newSettingCreateStruct()
     */
    public function testNewSettingCreateStruct()
    {
        $settingService = $this->getSettingService();
        $settingCreate = $settingService->newSettingCreateStruct();

        $this->assertInstanceOf(SettingCreateStruct::class, $settingCreate);
    }

    /**
     * @see \eZ\Publish\API\Repository\SettingService::createSetting()
     */
    public function testCreateSetting()
    {
        $settingService = $this->getSettingService();

        $settingCreate = $settingService->newSettingCreateStruct();
        $settingCreate->setGroup('test_group');
        $settingCreate->setIdentifier('test_identifier');
        $settingCreate->setValue('test_value');

        $setting = $settingService->createSetting($settingCreate);

        $this->assertInstanceOf(Setting::class, $setting);
    }

    /**
     * @see \eZ\Publish\API\Repository\SettingService::createSetting()
     */
    public function testCreateSettingThrowsInvalidArgumentException()
    {
        $this->expectException(InvalidArgumentException::class);

        $settingService = $this->getSettingService();

        $settingCreateFirst = $settingService->newSettingCreateStruct();
        $settingCreateFirst->setGroup('test_group');
        $settingCreateFirst->setIdentifier('test_identifier');
        $settingCreateFirst->setValue('test_value');

        $settingService->createSetting($settingCreateFirst);

        $settingCreateSecond = $settingService->newSettingCreateStruct();
        $settingCreateSecond->setGroup('test_group');
        $settingCreateSecond->setIdentifier('test_identifier');
        $settingCreateSecond->setValue('another_value');

        $settingService->createSetting($settingCreateSecond);
    }

    /**
     * @see \eZ\Publish\API\Repository\SettingService::loadSetting()
     */
    public function testLoadSetting()
    {
        $settingService = $this->getSettingService();

        $settingCreate = $settingService->newSettingCreateStruct();
        $settingCreate->setGroup('another_group');
        $settingCreate->setIdentifier('another_identifier');
        $settingCreate->setValue('test_value');

        $settingService->createSetting($settingCreate);

        $setting = $settingService->loadSetting('another_group', 'another_identifier');

        $this->assertInstanceOf(Setting::class, $setting);
        $this->assertEquals('test_value', $setting->value);
    }

    /**
     * @see \eZ\Publish\API\Repository\SettingService::loadSetting()
     */
    public function testLoadSettingThrowsNotFoundException()
    {
        $this->expectException(NotFoundException::class);

        $settingService = $this->getSettingService();
        $settingService->loadSetting('unknown_group', 'unknown_identifier');
    }

    /**
     * @see \eZ\Publish\API\Repository\SettingService::newSettingUpdateStruct()
     */
    public function testNewSettingUpdateStruct()
    {
        $settingService = $this->getSettingService();
        $settingUpdate = $settingService->newSettingUpdateStruct();

        $this->assertInstanceOf(SettingUpdateStruct::class, $settingUpdate);
    }

    /**
     * @see \eZ\Publish\API\Repository\SettingService::updateSetting()
     */
    public function testUpdateSetting()
    {
        $settingService = $this->getSettingService();

        $settingCreate = $settingService->newSettingCreateStruct();
        $settingCreate->setGroup('update_group');
        $settingCreate->setIdentifier('update_identifier');
        $settingCreate->setValue('some_value');

        $setting = $settingService->createSetting($settingCreate);

        $settingUpdate = $settingService->newSettingUpdateStruct();
        $settingUpdate->setValue('updated_value');

        $updatedSetting = $settingService->updateSetting($setting, $settingUpdate);

        $this->assertInstanceOf(Setting::class, $updatedSetting);

        $updatedSetting = $settingService->loadSetting('update_group', 'update_identifier');

        $this->assertEquals('updated_value', $updatedSetting->value);
    }

    /**
     * @see \eZ\Publish\API\Repository\SettingService::deleteSetting()
     */
    public function testDeleteSetting()
    {
        $this->expectException(NotFoundException::class);

        $settingService = $this->getSettingService();

        $settingCreate = $settingService->newSettingCreateStruct();
        $settingCreate->setGroup('delete_group');
        $settingCreate->setIdentifier('delete_identifier');
        $settingCreate->setValue('some_value');

        $setting = $settingService->createSetting($settingCreate);

        $this->assertInstanceOf(Setting::class, $setting);

        $settingService->deleteSetting($setting);

        $settingService->loadSetting('delete_group', 'delete_identifier');
    }

    /**
     * @see \eZ\Publish\API\Repository\SettingService::deleteSetting()
     */
    public function testDeleteSettingThrowsNotFoundException()
    {
        $this->expectException(NotFoundException::class);

        $settingService = $this->getSettingService();

        $settingCreate = $settingService->newSettingCreateStruct();
        $settingCreate->setGroup('delete_twice_group');
        $settingCreate->setIdentifier('delete_twice_identifier');
        $settingCreate->setValue('some_value');

        $setting = $settingService->createSetting($settingCreate);

        // Delete the newly created setting
        $settingService->deleteSetting($setting);

        // This call should fail with a NotFoundException
        $settingService->deleteSetting($setting);
    }
}
