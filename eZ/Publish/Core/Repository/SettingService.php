<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace eZ\Publish\Core\Repository;

use eZ\Publish\API\Repository\Exceptions\NotFoundException as APINotFoundException;
use eZ\Publish\API\Repository\PermissionResolver;
use eZ\Publish\API\Repository\SettingService as SettingServiceInterface;
use eZ\Publish\API\Repository\Values\Setting\Setting;
use eZ\Publish\API\Repository\Values\Setting\SettingCreateStruct;
use eZ\Publish\API\Repository\Values\Setting\SettingUpdateStruct;
use eZ\Publish\Core\Base\Exceptions\InvalidArgumentException;
use eZ\Publish\Core\Base\Exceptions\UnauthorizedException;
use eZ\Publish\SPI\Persistence\Setting\Handler as SettingHandler;
use eZ\Publish\SPI\Persistence\Setting\Setting as SPISetting;

final class SettingService implements SettingServiceInterface
{
    /** @var \eZ\Publish\SPI\Persistence\Setting\Handler */
    private $settingHandler;

    /** @var \eZ\Publish\API\Repository\PermissionResolver */
    private $permissionResolver;

    public function __construct(
        SettingHandler $settingHandler,
        PermissionResolver $permissionResolver
    ) {
        $this->settingHandler = $settingHandler;
        $this->permissionResolver = $permissionResolver;
    }

    public function loadSetting(string $group, string $identifier): Setting
    {
        return $this->buildDomainSettingObject(
            $this->settingHandler->load($group, $identifier)
        );
    }

    /**
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     * @throws \eZ\Publish\Core\Base\Exceptions\UnauthorizedException
     * @throws \eZ\Publish\API\Repository\Exceptions\BadStateException
     */
    public function updateSetting(Setting $setting, SettingUpdateStruct $settingUpdateStruct): Setting
    {
        if (!$this->permissionResolver->canUser('setting', 'update', $setting)) {
            throw new UnauthorizedException('setting', 'update');
        }

        return $this->buildDomainSettingObject(
            $this->settingHandler->update(
                $setting->group,
                $setting->identifier,
                json_encode($settingUpdateStruct->value)
            )
        );
    }

    /**
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     * @throws \eZ\Publish\Core\Base\Exceptions\UnauthorizedException
     * @throws \eZ\Publish\API\Repository\Exceptions\BadStateException
     */
    public function createSetting(SettingCreateStruct $settingCreateStruct): Setting
    {
        if (!$this->permissionResolver->canUser('setting', 'create', $settingCreateStruct)) {
            throw new UnauthorizedException('setting', 'create');
        }

        try {
            $existingSetting = $this->settingHandler->load($settingCreateStruct->group, $settingCreateStruct->identifier);
            if ($existingSetting !== null) {
                throw new InvalidArgumentException('settingCreateStruct', 'A Setting with the specified group and identifier already exists');
            }
        } catch (APINotFoundException $e) {
            // Do nothing
        }

        return $this->buildDomainSettingObject(
            $this->settingHandler->create(
                $settingCreateStruct->group,
                $settingCreateStruct->identifier,
                json_encode($settingCreateStruct->value)
            )
        );
    }

    /**
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     * @throws \eZ\Publish\Core\Base\Exceptions\UnauthorizedException
     * @throws \eZ\Publish\API\Repository\Exceptions\BadStateException
     */
    public function deleteSetting(Setting $setting): void
    {
        if (!$this->permissionResolver->canUser('setting', 'remove', $setting)) {
            throw new UnauthorizedException('setting', 'remove');
        }

        $existingSetting = $this->settingHandler->load($setting->group, $setting->identifier);

        $this->settingHandler->delete(
            $existingSetting->group,
            $existingSetting->identifier
        );
    }

    public function newSettingCreateStruct(array $properties = []): SettingCreateStruct
    {
        return new SettingCreateStruct($properties);
    }

    public function newSettingUpdateStruct(array $properties = []): SettingUpdateStruct
    {
        return new SettingUpdateStruct($properties);
    }

    private function buildDomainSettingObject(SPISetting $setting): Setting
    {
        return new Setting([
            'group' => $setting->group,
            'identifier' => $setting->identifier,
            'value' => json_decode($setting->serializedValue),
        ]);
    }
}
