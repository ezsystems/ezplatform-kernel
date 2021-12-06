<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\Persistence\Cache;

use Ibexa\Contracts\Core\Persistence\Handler as PersistenceHandlerInterface;
use Ibexa\Contracts\Core\Persistence\Setting\Handler as SPISettingHandler;
use Ibexa\Core\Persistence\Cache\BookmarkHandler as CacheBookmarkHandler;
use Ibexa\Core\Persistence\Cache\ContentHandler as CacheContentHandler;
use Ibexa\Core\Persistence\Cache\ContentLanguageHandler as CacheContentLanguageHandler;
use Ibexa\Core\Persistence\Cache\ContentTypeHandler as CacheContentTypeHandler;
use Ibexa\Core\Persistence\Cache\LocationHandler as CacheLocationHandler;
use Ibexa\Core\Persistence\Cache\NotificationHandler as CacheNotificationHandler;
use Ibexa\Core\Persistence\Cache\ObjectStateHandler as CacheObjectStateHandler;
use Ibexa\Core\Persistence\Cache\SectionHandler as CacheSectionHandler;
use Ibexa\Core\Persistence\Cache\SettingHandler as SettingHandler;
use Ibexa\Core\Persistence\Cache\TransactionHandler as CacheTransactionHandler;
use Ibexa\Core\Persistence\Cache\TrashHandler as CacheTrashHandler;
use Ibexa\Core\Persistence\Cache\UrlAliasHandler as CacheUrlAliasHandler;
use Ibexa\Core\Persistence\Cache\URLHandler as CacheUrlHandler;
use Ibexa\Core\Persistence\Cache\UrlWildcardHandler as CacheUrlWildcardHandler;
use Ibexa\Core\Persistence\Cache\UserHandler as CacheUserHandler;
use Ibexa\Core\Persistence\Cache\UserPreferenceHandler as CacheUserPreferenceHandler;

/**
 * Persistence Cache Handler class.
 */
class Handler implements PersistenceHandlerInterface
{
    /** @var \Ibexa\Contracts\Core\Persistence\Handler */
    protected $persistenceHandler;

    /** @var \Ibexa\Core\Persistence\Cache\SectionHandler */
    protected $sectionHandler;

    /** @var \Ibexa\Core\Persistence\Cache\ContentHandler */
    protected $contentHandler;

    /** @var \Ibexa\Core\Persistence\Cache\ContentLanguageHandler */
    protected $contentLanguageHandler;

    /** @var \Ibexa\Core\Persistence\Cache\ContentTypeHandler */
    protected $contentTypeHandler;

    /** @var \Ibexa\Core\Persistence\Cache\LocationHandler */
    protected $locationHandler;

    /** @var \Ibexa\Core\Persistence\Cache\UserHandler */
    protected $userHandler;

    /** @var \Ibexa\Core\Persistence\Cache\TrashHandler */
    protected $trashHandler;

    /** @var \Ibexa\Core\Persistence\Cache\UrlAliasHandler */
    protected $urlAliasHandler;

    /** @var \Ibexa\Core\Persistence\Cache\ObjectStateHandler */
    protected $objectStateHandler;

    /** @var \Ibexa\Core\Persistence\Cache\TransactionHandler */
    protected $transactionHandler;

    /** @var \Ibexa\Core\Persistence\Cache\URLHandler */
    protected $urlHandler;

    /** @var \Ibexa\Core\Persistence\Cache\BookmarkHandler */
    protected $bookmarkHandler;

    /** @var \Ibexa\Core\Persistence\Cache\NotificationHandler */
    protected $notificationHandler;

    /** @var \Ibexa\Core\Persistence\Cache\UserPreferenceHandler */
    protected $userPreferenceHandler;

    /** @var \Ibexa\Core\Persistence\Cache\UrlWildcardHandler */
    private $urlWildcardHandler;

    /** @var \Ibexa\Core\Persistence\Cache\PersistenceLogger */
    protected $logger;

    /** @var \Ibexa\Core\Persistence\Cache\SettingHandler */
    private $settingHandler;

    public function __construct(
        PersistenceHandlerInterface $persistenceHandler,
        CacheSectionHandler $sectionHandler,
        CacheLocationHandler $locationHandler,
        CacheContentHandler $contentHandler,
        CacheContentLanguageHandler $contentLanguageHandler,
        CacheContentTypeHandler $contentTypeHandler,
        CacheUserHandler $userHandler,
        CacheTransactionHandler $transactionHandler,
        CacheTrashHandler $trashHandler,
        CacheUrlAliasHandler $urlAliasHandler,
        CacheObjectStateHandler $objectStateHandler,
        CacheUrlHandler $urlHandler,
        CacheBookmarkHandler $bookmarkHandler,
        CacheNotificationHandler $notificationHandler,
        CacheUserPreferenceHandler $userPreferenceHandler,
        CacheUrlWildcardHandler $urlWildcardHandler,
        SettingHandler $settingHandler,
        PersistenceLogger $logger
    ) {
        $this->persistenceHandler = $persistenceHandler;
        $this->sectionHandler = $sectionHandler;
        $this->locationHandler = $locationHandler;
        $this->contentHandler = $contentHandler;
        $this->contentLanguageHandler = $contentLanguageHandler;
        $this->contentTypeHandler = $contentTypeHandler;
        $this->userHandler = $userHandler;
        $this->transactionHandler = $transactionHandler;
        $this->trashHandler = $trashHandler;
        $this->urlAliasHandler = $urlAliasHandler;
        $this->objectStateHandler = $objectStateHandler;
        $this->urlHandler = $urlHandler;
        $this->bookmarkHandler = $bookmarkHandler;
        $this->notificationHandler = $notificationHandler;
        $this->userPreferenceHandler = $userPreferenceHandler;
        $this->urlWildcardHandler = $urlWildcardHandler;
        $this->settingHandler = $settingHandler;
        $this->logger = $logger;
    }

    /**
     * @return \Ibexa\Contracts\Core\Persistence\Content\Handler
     */
    public function contentHandler()
    {
        return $this->contentHandler;
    }

    /**
     * @return \Ibexa\Contracts\Core\Persistence\Content\Type\Handler
     */
    public function contentTypeHandler()
    {
        return $this->contentTypeHandler;
    }

    /**
     * @return \Ibexa\Contracts\Core\Persistence\Content\Language\Handler
     */
    public function contentLanguageHandler()
    {
        return $this->contentLanguageHandler;
    }

    /**
     * @return \Ibexa\Contracts\Core\Persistence\Content\Location\Handler
     */
    public function locationHandler()
    {
        return $this->locationHandler;
    }

    /**
     * @return \Ibexa\Contracts\Core\Persistence\Content\ObjectState\Handler
     */
    public function objectStateHandler()
    {
        return $this->objectStateHandler;
    }

    /**
     * @return \Ibexa\Contracts\Core\Persistence\User\Handler
     */
    public function userHandler()
    {
        return $this->userHandler;
    }

    /**
     * @return \Ibexa\Contracts\Core\Persistence\Content\Section\Handler
     */
    public function sectionHandler()
    {
        return $this->sectionHandler;
    }

    /**
     * @return \Ibexa\Contracts\Core\Persistence\Content\Location\Trash\Handler
     */
    public function trashHandler()
    {
        return $this->trashHandler;
    }

    /**
     * @return \Ibexa\Contracts\Core\Persistence\Content\UrlAlias\Handler
     */
    public function urlAliasHandler()
    {
        return $this->urlAliasHandler;
    }

    /**
     * @return \Ibexa\Contracts\Core\Persistence\Content\UrlWildcard\Handler
     */
    public function urlWildcardHandler()
    {
        return $this->urlWildcardHandler;
    }

    /**
     * @return \Ibexa\Contracts\Core\Persistence\TransactionHandler
     */
    public function transactionHandler()
    {
        return $this->transactionHandler;
    }

    public function settingHandler(): SPISettingHandler
    {
        return $this->settingHandler;
    }

    /**
     * @return \Ibexa\Core\Persistence\Cache\URLHandler
     */
    public function urlHandler()
    {
        return $this->urlHandler;
    }

    /**
     * @return \Ibexa\Core\Persistence\Cache\BookmarkHandler
     */
    public function bookmarkHandler()
    {
        return $this->bookmarkHandler;
    }

    /**
     * @return \Ibexa\Core\Persistence\Cache\NotificationHandler
     */
    public function notificationHandler()
    {
        return $this->notificationHandler;
    }

    /**
     * @return \Ibexa\Core\Persistence\Cache\UserPreferenceHandler
     */
    public function userPreferenceHandler()
    {
        return $this->userPreferenceHandler;
    }

    /**
     * {@inheritdoc}
     */
    public function beginTransaction()
    {
        $this->transactionHandler->beginTransaction();
    }

    /**
     * {@inheritdoc}
     */
    public function commit()
    {
        $this->transactionHandler->commit();
    }

    /**
     * {@inheritdoc}
     */
    public function rollback()
    {
        $this->transactionHandler->rollback();
    }
}

class_alias(Handler::class, 'eZ\Publish\Core\Persistence\Cache\Handler');
