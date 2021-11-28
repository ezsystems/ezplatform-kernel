<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\Repository;

use Ibexa\Contracts\Core\Persistence\Setting\Handler as SettingHandler;
use Ibexa\Contracts\Core\Persistence\Setting\Setting as SPISetting;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException as APINotFoundException;
use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Contracts\Core\Repository\SettingService as SettingServiceInterface;
use Ibexa\Contracts\Core\Repository\Values\Setting\Setting;
use Ibexa\Contracts\Core\Repository\Values\Setting\SettingCreateStruct;
use Ibexa\Contracts\Core\Repository\Values\Setting\SettingUpdateStruct;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;
use Ibexa\Core\Base\Exceptions\UnauthorizedException;

final class SettingService implements SettingServiceInterface
{
    /** @var \Ibexa\Contracts\Core\Persistence\Setting\Handler */
    private $settingHandler;

    /** @var \Ibexa\Contracts\Core\Repository\PermissionResolver */
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
        return $this->buildSettingDomainObject(
            $this->settingHandler->load($group, $identifier)
        );
    }

    public function updateSetting(Setting $setting, SettingUpdateStruct $settingUpdateStruct): Setting
    {
        if (!$this->permissionResolver->canUser('setting', 'update', $setting)) {
            throw new UnauthorizedException('setting', 'update');
        }

        return $this->buildSettingDomainObject(
            $this->settingHandler->update(
                $setting->group,
                $setting->identifier,
                json_encode($settingUpdateStruct->value)
            )
        );
    }

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

        return $this->buildSettingDomainObject(
            $this->settingHandler->create(
                $settingCreateStruct->group,
                $settingCreateStruct->identifier,
                json_encode($settingCreateStruct->value)
            )
        );
    }

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

    private function buildSettingDomainObject(SPISetting $setting): Setting
    {
        return new Setting([
            'group' => $setting->group,
            'identifier' => $setting->identifier,
            'value' => json_decode($setting->serializedValue, true),
        ]);
    }
}

class_alias(SettingService::class, 'eZ\Publish\Core\Repository\SettingService');
