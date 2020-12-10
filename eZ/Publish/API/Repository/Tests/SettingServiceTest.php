<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace eZ\Publish\API\Repository\Tests;

use eZ\Publish\API\Repository\Exceptions\InvalidArgumentException;
use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\SettingService;
use eZ\Publish\API\Repository\Values\Setting\Setting;

/**
 * Test case for operations in the SettingService using in memory storage.
 *
 * @covers \eZ\Publish\API\Repository\SettingService
 * @group integration
 * @group setting
 */
final class SettingServiceTest extends BaseTest
{
    /** @var \eZ\Publish\API\Repository\PermissionResolver */
    protected $permissionResolver;

    /** @var \eZ\Publish\API\Repository\SettingService */
    protected $settingService;

    protected function getSettingService(): SettingService
    {
        $container = $this->getSetupFactory()->getServiceContainer();
        /** @var \eZ\Publish\API\Repository\SettingService $settingService */
        $settingService = $container->get(SettingService::class);

        return $settingService;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $repository = $this->getRepository(false);
        $this->permissionResolver = $repository->getPermissionResolver();
    }

    /**
     * @covers \eZ\Publish\API\Repository\SettingService::createSetting()
     */
    public function testCreateSetting(): void
    {
        $settingService = $this->getSettingService();

        $settingCreate = $settingService->newSettingCreateStruct();
        $settingCreate->setGroup('test_group');
        $settingCreate->setIdentifier('test_identifier');
        $settingCreate->setValue('test_value');

        $setting = $settingService->createSetting($settingCreate);
        self::assertEquals(new Setting([
            'group' => 'test_group',
            'identifier' => 'test_identifier',
            'value' => 'test_value',
        ]), $setting);
    }

    /**
     * @covers \eZ\Publish\API\Repository\SettingService::createSetting()
     */
    public function testCreateSettingThrowsInvalidArgumentException(): void
    {
        $settingService = $this->getSettingService();

        $settingCreateFirst = $settingService->newSettingCreateStruct();
        $settingCreateFirst->setGroup('test_group2');
        $settingCreateFirst->setIdentifier('test_identifier2');
        $settingCreateFirst->setValue('test_value');

        $settingService->createSetting($settingCreateFirst);

        $settingCreateSecond = $settingService->newSettingCreateStruct();
        $settingCreateSecond->setGroup('test_group2');
        $settingCreateSecond->setIdentifier('test_identifier2');
        $settingCreateSecond->setValue('another_value');

        $this->expectException(InvalidArgumentException::class);

        $settingService->createSetting($settingCreateSecond);
    }

    /**
     * @covers \eZ\Publish\API\Repository\SettingService::loadSetting()
     */
    public function testLoadSetting(): void
    {
        $settingService = $this->getSettingService();

        $settingCreate = $settingService->newSettingCreateStruct();
        $settingCreate->setGroup('another_group');
        $settingCreate->setIdentifier('another_identifier');
        $settingCreate->setValue('test_value');

        $settingService->createSetting($settingCreate);
        $setting = $settingService->loadSetting('another_group', 'another_identifier');

        self::assertEquals('test_value', $setting->value);
    }

    /**
     * @covers \eZ\Publish\API\Repository\SettingService::loadSetting()
     */
    public function testLoadSettingThrowsNotFoundException(): void
    {
        $settingService = $this->getSettingService();

        $this->expectException(NotFoundException::class);

        $settingService->loadSetting('unknown_group', 'unknown_identifier');
    }

    /**
     * @covers \eZ\Publish\API\Repository\SettingService::updateSetting()
     */
    public function testUpdateSetting(): void
    {
        $settingService = $this->getSettingService();

        $settingCreate = $settingService->newSettingCreateStruct();
        $settingCreate->setGroup('update_group');
        $settingCreate->setIdentifier('update_identifier');
        $settingCreate->setValue('some_value');

        $setting = $settingService->createSetting($settingCreate);

        $settingUpdate = $settingService->newSettingUpdateStruct();
        $settingUpdate->setValue('updated_value');

        $settingService->updateSetting($setting, $settingUpdate);
        $updatedSetting = $settingService->loadSetting('update_group', 'update_identifier');

        self::assertEquals('updated_value', $updatedSetting->value);
    }

    /**
     * @covers \eZ\Publish\API\Repository\SettingService::deleteSetting()
     */
    public function testDeleteSetting(): void
    {
        $settingService = $this->getSettingService();

        $settingCreate = $settingService->newSettingCreateStruct();
        $settingCreate->setGroup('delete_group');
        $settingCreate->setIdentifier('delete_identifier');
        $settingCreate->setValue('some_value');

        $setting = $settingService->createSetting($settingCreate);
        $settingService->deleteSetting($setting);

        $this->expectException(NotFoundException::class);

        $settingService->loadSetting('delete_group', 'delete_identifier');
    }

    /**
     * @covers \eZ\Publish\API\Repository\SettingService::deleteSetting()
     */
    public function testDeleteSettingThrowsNotFoundException(): void
    {
        $settingService = $this->getSettingService();

        $settingCreate = $settingService->newSettingCreateStruct();
        $settingCreate->setGroup('delete_twice_group');
        $settingCreate->setIdentifier('delete_twice_identifier');
        $settingCreate->setValue('some_value');

        $setting = $settingService->createSetting($settingCreate);

        // Delete the newly created setting
        $settingService->deleteSetting($setting);

        $this->expectException(NotFoundException::class);

        // This call should fail with a NotFoundException
        $settingService->deleteSetting($setting);
    }
}
