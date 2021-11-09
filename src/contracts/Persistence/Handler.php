<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Persistence;

use Ibexa\Contracts\Core\Persistence\Setting\Handler as SettingHandler;

/**
 * The main handler for Storage Engine.
 */
interface Handler
{
    /**
     * @return \Ibexa\Contracts\Core\Persistence\Content\Handler
     */
    public function contentHandler();

    /**
     * @return \Ibexa\Contracts\Core\Persistence\Content\Type\Handler
     */
    public function contentTypeHandler();

    /**
     * @return \Ibexa\Contracts\Core\Persistence\Content\Language\Handler
     */
    public function contentLanguageHandler();

    /**
     * @return \Ibexa\Contracts\Core\Persistence\Content\Location\Handler
     */
    public function locationHandler();

    /**
     * @return \Ibexa\Contracts\Core\Persistence\Content\ObjectState\Handler
     */
    public function objectStateHandler();

    /**
     * @return \Ibexa\Contracts\Core\Persistence\Content\Location\Trash\Handler
     */
    public function trashHandler();

    /**
     * @return \Ibexa\Contracts\Core\Persistence\User\Handler
     */
    public function userHandler();

    /**
     * @return \Ibexa\Contracts\Core\Persistence\Content\Section\Handler
     */
    public function sectionHandler();

    /**
     * @return \Ibexa\Contracts\Core\Persistence\Content\UrlAlias\Handler
     */
    public function urlAliasHandler();

    /**
     * @return \Ibexa\Contracts\Core\Persistence\Content\UrlWildcard\Handler
     */
    public function urlWildcardHandler();

    /**
     * @return \Ibexa\Core\Persistence\Legacy\URL\Handler
     */
    public function urlHandler();

    /**
     * @return \Ibexa\Contracts\Core\Persistence\Bookmark\Handler
     */
    public function bookmarkHandler();

    /**
     * @return \Ibexa\Contracts\Core\Persistence\Notification\Handler
     */
    public function notificationHandler();

    /**
     * @return \Ibexa\Contracts\Core\Persistence\UserPreference\Handler
     */
    public function userPreferenceHandler();

    /**
     * @return \Ibexa\Contracts\Core\Persistence\TransactionHandler
     */
    public function transactionHandler();

    public function settingHandler(): SettingHandler;

    /**
     * Begin transaction.
     *
     * Begins an transaction, make sure you'll call commit or rollback when done,
     * otherwise work will be lost.
     *
     * @deprecated Since 5.3 {@use transactionHandler()->beginTransaction()}
     */
    public function beginTransaction();

    /**
     * Commit transaction.
     *
     * Commit transaction, or throw exceptions if no transactions has been started.
     *
     * @throws \RuntimeException If no transaction has been started
     *
     * @deprecated Since 5.3 {@use transactionHandler()->commit()}
     */
    public function commit();

    /**
     * Rollback transaction.
     *
     * Rollback transaction, or throw exceptions if no transactions has been started.
     *
     * @throws \RuntimeException If no transaction has been started
     *
     * @deprecated Since 5.3 {@use transactionHandler()->rollback()}
     */
    public function rollback();
}

class_alias(Handler::class, 'eZ\Publish\SPI\Persistence\Handler');
