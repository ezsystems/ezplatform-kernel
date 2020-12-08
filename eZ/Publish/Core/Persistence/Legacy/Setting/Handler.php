<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace eZ\Publish\Core\Persistence\Legacy\Setting;

use eZ\Publish\SPI\Persistence\Setting\Handler as BaseSettingHandler;
use eZ\Publish\Core\Base\Exceptions\NotFoundException as NotFound;
use eZ\Publish\SPI\Persistence\Setting\Setting;

class Handler implements BaseSettingHandler
{
    /** @var \eZ\Publish\Core\Persistence\Legacy\Setting\Gateway */
    protected $settingGateway;

    public function __construct(Gateway $settingGateway)
    {
        $this->settingGateway = $settingGateway;
    }

    /**
     * {@inheritDoc}
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function create(string $group, string $identifier, $value): Setting
    {
        $setting = $this->settingGateway->loadSettingById(
            $this->settingGateway->insertSetting(
                $group,
                $identifier,
                json_encode($value)
            )
        );

        return new Setting([
            'group' => $setting['group'],
            'identifier' => $setting['identifier'],
            'value' => json_decode($setting['value']),
        ]);
    }

    /**
     * {@inheritDoc}
     *
     * @throws \Doctrine\DBAL\Exception
     * @throws \eZ\Publish\Core\Base\Exceptions\NotFoundException
     */
    public function update(string $group, string $identifier, $value): Setting
    {
        $this->settingGateway->updateSetting(
            $group,
            $identifier,
            json_encode($value)
        );

        return $this->load($group, $identifier);
    }

    /**
     * @throws \Doctrine\DBAL\Exception
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
            'value' => json_decode($setting['value']),
        ]);
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function delete(string $group, string $identifier): void
    {
        $this->settingGateway->deleteSetting($group, $identifier);
    }
}
