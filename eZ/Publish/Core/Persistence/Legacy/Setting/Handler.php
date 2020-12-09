<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace eZ\Publish\Core\Persistence\Legacy\Setting;

use eZ\Publish\Core\Base\Exceptions\NotFoundException as NotFound;
use eZ\Publish\SPI\Persistence\Setting\Handler as BaseSettingHandler;
use eZ\Publish\SPI\Persistence\Setting\Setting;

class Handler implements BaseSettingHandler
{
    /** @var \eZ\Publish\Core\Persistence\Legacy\Setting\Gateway */
    protected $settingGateway;

    public function __construct(Gateway $settingGateway)
    {
        $this->settingGateway = $settingGateway;
    }

    public function create(string $group, string $identifier, string $serializedValue): Setting
    {
        $lastId = $this->settingGateway->insertSetting(
            $group,
            $identifier,
            $serializedValue
        );

        $setting = $this->settingGateway->loadSettingById($lastId);

        return new Setting([
            'group' => $setting['group'],
            'identifier' => $setting['identifier'],
            'serializedValue' => $setting['value'],
        ]);
    }

    /**
     * @throws \eZ\Publish\Core\Base\Exceptions\NotFoundException
     */
    public function update(string $group, string $identifier, string $serializedValue): Setting
    {
        $this->settingGateway->updateSetting(
            $group,
            $identifier,
            $serializedValue
        );

        $setting = $this->settingGateway->loadSetting($group, $identifier);

        return new Setting([
            'group' => $setting['group'],
            'identifier' => $setting['identifier'],
            'serializedValue' => $setting['value'],
        ]);
    }

    /**
     * @throws \eZ\Publish\Core\Base\Exceptions\NotFoundException
     */
    public function load(string $group, string $identifier): Setting
    {
        $setting = $this->settingGateway->loadSetting($group, $identifier);

        if (empty($setting)) {
            throw new NotFound('Setting', [
                'group' => $group,
                'identifier' => $identifier,
            ]);
        }

        return new Setting([
            'group' => $setting['group'],
            'identifier' => $setting['identifier'],
            'serializedValue' => $setting['value'],
        ]);
    }

    public function delete(string $group, string $identifier): void
    {
        $this->settingGateway->deleteSetting($group, $identifier);
    }
}
