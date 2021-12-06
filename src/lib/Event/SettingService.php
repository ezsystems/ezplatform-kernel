<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\Event;

use Ibexa\Contracts\Core\Repository\Decorator\SettingServiceDecorator;
use Ibexa\Contracts\Core\Repository\Events\Setting\BeforeCreateSettingEvent;
use Ibexa\Contracts\Core\Repository\Events\Setting\BeforeDeleteSettingEvent;
use Ibexa\Contracts\Core\Repository\Events\Setting\BeforeUpdateSettingEvent;
use Ibexa\Contracts\Core\Repository\Events\Setting\CreateSettingEvent;
use Ibexa\Contracts\Core\Repository\Events\Setting\DeleteSettingEvent;
use Ibexa\Contracts\Core\Repository\Events\Setting\UpdateSettingEvent;
use Ibexa\Contracts\Core\Repository\SettingService as SettingServiceInterface;
use Ibexa\Contracts\Core\Repository\Values\Setting\Setting;
use Ibexa\Contracts\Core\Repository\Values\Setting\SettingCreateStruct;
use Ibexa\Contracts\Core\Repository\Values\Setting\SettingUpdateStruct;
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

class_alias(SettingService::class, 'eZ\Publish\Core\Event\SettingService');
