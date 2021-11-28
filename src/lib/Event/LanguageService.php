<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\Event;

use Ibexa\Contracts\Core\Repository\Decorator\LanguageServiceDecorator;
use Ibexa\Contracts\Core\Repository\Events\Language\BeforeCreateLanguageEvent;
use Ibexa\Contracts\Core\Repository\Events\Language\BeforeDeleteLanguageEvent;
use Ibexa\Contracts\Core\Repository\Events\Language\BeforeDisableLanguageEvent;
use Ibexa\Contracts\Core\Repository\Events\Language\BeforeEnableLanguageEvent;
use Ibexa\Contracts\Core\Repository\Events\Language\BeforeUpdateLanguageNameEvent;
use Ibexa\Contracts\Core\Repository\Events\Language\CreateLanguageEvent;
use Ibexa\Contracts\Core\Repository\Events\Language\DeleteLanguageEvent;
use Ibexa\Contracts\Core\Repository\Events\Language\DisableLanguageEvent;
use Ibexa\Contracts\Core\Repository\Events\Language\EnableLanguageEvent;
use Ibexa\Contracts\Core\Repository\Events\Language\UpdateLanguageNameEvent;
use Ibexa\Contracts\Core\Repository\LanguageService as LanguageServiceInterface;
use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Contracts\Core\Repository\Values\Content\LanguageCreateStruct;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class LanguageService extends LanguageServiceDecorator
{
    /** @var \Symfony\Contracts\EventDispatcher\EventDispatcherInterface */
    protected $eventDispatcher;

    public function __construct(
        LanguageServiceInterface $innerService,
        EventDispatcherInterface $eventDispatcher
    ) {
        parent::__construct($innerService);

        $this->eventDispatcher = $eventDispatcher;
    }

    public function createLanguage(LanguageCreateStruct $languageCreateStruct): Language
    {
        $eventData = [$languageCreateStruct];

        $beforeEvent = new BeforeCreateLanguageEvent(...$eventData);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return $beforeEvent->getLanguage();
        }

        $language = $beforeEvent->hasLanguage()
            ? $beforeEvent->getLanguage()
            : $this->innerService->createLanguage($languageCreateStruct);

        $this->eventDispatcher->dispatch(
            new CreateLanguageEvent($language, ...$eventData)
        );

        return $language;
    }

    public function updateLanguageName(
        Language $language,
        string $newName
    ): Language {
        $eventData = [
            $language,
            $newName,
        ];

        $beforeEvent = new BeforeUpdateLanguageNameEvent(...$eventData);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return $beforeEvent->getUpdatedLanguage();
        }

        $updatedLanguage = $beforeEvent->hasUpdatedLanguage()
            ? $beforeEvent->getUpdatedLanguage()
            : $this->innerService->updateLanguageName($language, $newName);

        $this->eventDispatcher->dispatch(
            new UpdateLanguageNameEvent($updatedLanguage, ...$eventData)
        );

        return $updatedLanguage;
    }

    public function enableLanguage(Language $language): Language
    {
        $eventData = [$language];

        $beforeEvent = new BeforeEnableLanguageEvent(...$eventData);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return $beforeEvent->getEnabledLanguage();
        }

        $enabledLanguage = $beforeEvent->hasEnabledLanguage()
            ? $beforeEvent->getEnabledLanguage()
            : $this->innerService->enableLanguage($language);

        $this->eventDispatcher->dispatch(
            new EnableLanguageEvent($enabledLanguage, ...$eventData)
        );

        return $enabledLanguage;
    }

    public function disableLanguage(Language $language): Language
    {
        $eventData = [$language];

        $beforeEvent = new BeforeDisableLanguageEvent(...$eventData);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return $beforeEvent->getDisabledLanguage();
        }

        $disabledLanguage = $beforeEvent->hasDisabledLanguage()
            ? $beforeEvent->getDisabledLanguage()
            : $this->innerService->disableLanguage($language);

        $this->eventDispatcher->dispatch(
            new DisableLanguageEvent($disabledLanguage, ...$eventData)
        );

        return $disabledLanguage;
    }

    public function deleteLanguage(Language $language): void
    {
        $eventData = [$language];

        $beforeEvent = new BeforeDeleteLanguageEvent(...$eventData);

        $this->eventDispatcher->dispatch($beforeEvent);
        if ($beforeEvent->isPropagationStopped()) {
            return;
        }

        $this->innerService->deleteLanguage($language);

        $this->eventDispatcher->dispatch(
            new DeleteLanguageEvent(...$eventData)
        );
    }
}

class_alias(LanguageService::class, 'eZ\Publish\Core\Event\LanguageService');
