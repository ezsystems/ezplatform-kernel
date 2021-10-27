<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Core\Repository;

use Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\SettingService;
use Ibexa\Contracts\Core\Repository\Values\Setting\Setting;

/**
 * Test case for operations in the SettingService using in memory storage.
 *
 * @covers \Ibexa\Contracts\Core\Repository\SettingService
 * @group integration
 * @group setting
 */
final class SettingServiceTest extends BaseTest
{
    /** @var \Ibexa\Contracts\Core\Repository\PermissionResolver */
    protected $permissionResolver;

    /** @var \Ibexa\Contracts\Core\Repository\SettingService */
    protected $settingService;

    protected function getSettingService(): SettingService
    {
        $container = $this->getSetupFactory()->getServiceContainer();
        /** @var \Ibexa\Contracts\Core\Repository\SettingService $settingService */
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
     * @covers \Ibexa\Contracts\Core\Repository\SettingService::createSetting()
     *
     * @dataProvider dataProviderForCreateSetting
     */
    public function testCreateSetting(string $identifier, $value): void
    {
        $settingService = $this->getSettingService();

        $settingCreate = $settingService->newSettingCreateStruct();
        $settingCreate->setGroup('test_group');
        $settingCreate->setIdentifier($identifier);
        $settingCreate->setValue($value);

        $setting = $settingService->createSetting($settingCreate);

        self::assertEquals(new Setting([
            'group' => 'test_group',
            'identifier' => $identifier,
            'value' => $value,
        ]), $setting);
    }

    public function dataProviderForCreateSetting(): iterable
    {
        yield 'null' => [
            'example_null',
            null,
        ];

        yield 'boolean' => [
            'example_boolean',
            true,
        ];

        yield 'string' => [
            'example_string',
            'string',
        ];

        yield 'int' => [
            'example_int',
            2,
        ];

        yield 'float' => [
            'example_number',
            3.14,
        ];

        yield 'array' => [
            'example_hash',
            [
                'foo' => 'foo',
                'bar' => 2,
                'baz' => 3.14,
                'foobar' => range(1, 10),
            ],
        ];
    }

    /**
     * @covers \Ibexa\Contracts\Core\Repository\SettingService::createSetting()
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
     * @covers \Ibexa\Contracts\Core\Repository\SettingService::loadSetting()
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
     * @covers \Ibexa\Contracts\Core\Repository\SettingService::loadSetting()
     */
    public function testLoadSettingThrowsNotFoundException(): void
    {
        $settingService = $this->getSettingService();

        $this->expectException(NotFoundException::class);

        $settingService->loadSetting('unknown_group', 'unknown_identifier');
    }

    /**
     * @covers \Ibexa\Contracts\Core\Repository\SettingService::updateSetting()
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
     * @covers \Ibexa\Contracts\Core\Repository\SettingService::deleteSetting()
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
     * @covers \Ibexa\Contracts\Core\Repository\SettingService::deleteSetting()
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

class_alias(SettingServiceTest::class, 'eZ\Publish\API\Repository\Tests\SettingServiceTest');
