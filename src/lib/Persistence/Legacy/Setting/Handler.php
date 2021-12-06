<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Core\Persistence\Legacy\Setting;

use Ibexa\Contracts\Core\Persistence\Setting\Handler as BaseSettingHandler;
use Ibexa\Contracts\Core\Persistence\Setting\Setting;
use Ibexa\Core\Base\Exceptions\NotFoundException as NotFound;

class Handler implements BaseSettingHandler
{
    /** @var \Ibexa\Core\Persistence\Legacy\Setting\Gateway */
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
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
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
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
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

class_alias(Handler::class, 'eZ\Publish\Core\Persistence\Legacy\Setting\Handler');
