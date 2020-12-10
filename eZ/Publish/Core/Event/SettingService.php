<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace eZ\Publish\Core\Event;

use eZ\Publish\API\Repository\Events\Setting\BeforeCreateSettingEvent;
use eZ\Publish\API\Repository\Events\Setting\BeforeDeleteSettingEvent;
use eZ\Publish\API\Repository\Events\Setting\BeforeUpdateSettingEvent;
use eZ\Publish\API\Repository\Events\Setting\CreateSettingEvent;
use eZ\Publish\API\Repository\Events\Setting\DeleteSettingEvent;
use eZ\Publish\API\Repository\Events\Setting\UpdateSettingEvent;
use eZ\Publish\API\Repository\SettingService as SettingServiceInterface;
use eZ\Publish\API\Repository\Values\Setting\Setting;
use eZ\Publish\API\Repository\Values\Setting\SettingCreateStruct;
use eZ\Publish\API\Repository\Values\Setting\SettingUpdateStruct;
use eZ\Publish\SPI\Repository\Decorator\SettingServiceDecorator;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class SettingService extends SettingServiceDecorator
{
    /** @var \Symfony\Contracts\EventDispatcher\EventDispatcherInterface */
    private $eventDispatcher;

    public function __construct(
        SettingServiceInterface $innerService,
        EventDispatcherInterface $eventDispatcher
    ) {
        parent::__construct($innerService);

        $this->eventDispatcher = $eventDispatcher;
    }

    public function createSetting(SettingCreateStruct $settingCreateStruct): Setting
    {
        $eventData = [$settingCreateStruct];

        $beforeEvent = new BeforeCreateSettingEvent(...$eventData);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return $beforeEvent->getSetting();
        }

        $setting = $beforeEvent->hasSetting()
            ? $beforeEvent->getSetting()
            : $this->innerService->createSetting($settingCreateStruct);

        $this->eventDispatcher->dispatch(
            new CreateSettingEvent($setting, ...$eventData)
        );

        return $setting;
    }

    public function updateSetting(Setting $setting, SettingUpdateStruct $settingUpdateStruct): Setting
    {
        $eventData = [
            $setting,
            $settingUpdateStruct,
        ];

        $beforeEvent = new BeforeUpdateSettingEvent(...$eventData);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return $beforeEvent->getUpdatedSetting();
        }

        $updatedSetting = $beforeEvent->hasUpdatedSetting()
            ? $beforeEvent->getUpdatedSetting()
            : $this->innerService->updateSetting($setting, $settingUpdateStruct);

        $this->eventDispatcher->dispatch(
            new UpdateSettingEvent($updatedSetting, ...$eventData)
        );

        return $updatedSetting;
    }

    public function deleteSetting(Setting $setting): void
    {
        $eventData = [$setting];

        $beforeEvent = new BeforeDeleteSettingEvent(...$eventData);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return;
        }

        $this->innerService->deleteSetting($setting);

        $this->eventDispatcher->dispatch(
            new DeleteSettingEvent(...$eventData)
        );
    }
}
