<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository;

/**
 * Repository interface.
 */
interface Repository
{
    /**
     * Allows API execution to be performed with full access, sand-boxed.
     *
     * The closure sandbox will do a catch all on exceptions and rethrow after
     * re-setting the sudo flag.
     *
     * Example use:
     *     $location = $repository->sudo(function (Repository $repo) use ($locationId) {
     *             return $repo->getLocationService()->loadLocation($locationId)
     *         }
     *     );
     *
     *
     * @param callable $callback
     * @param \Ibexa\Contracts\Core\Repository\Repository|null $outerRepository Optional, mostly for internal use but allows to
     *                                                   specify Repository to pass to closure.
     *
     * @throws \Exception Re-throws exceptions thrown inside $callback
     *
     * @return mixed
     */
    public function sudo(callable $callback, ?Repository $outerRepository = null);

    /**
     * Get Content Service.
     *
     * Get service object to perform operations on Content objects and it's aggregate members.
     *
     * @return \Ibexa\Contracts\Core\Repository\ContentService
     */
    public function getContentService(): ContentService;

    /**
     * Get Content Language Service.
     *
     * Get service object to perform operations on Content language objects
     *
     * @return \Ibexa\Contracts\Core\Repository\LanguageService
     */
    public function getContentLanguageService(): LanguageService;

    /**
     * Get Content Type Service.
     *
     * Get service object to perform operations on Content Type objects and it's aggregate members.
     * ( Group, Field & FieldCategory )
     *
     * @return \Ibexa\Contracts\Core\Repository\ContentTypeService
     */
    public function getContentTypeService(): ContentTypeService;

    /**
     * Get Content Location Service.
     *
     * Get service object to perform operations on Location objects and subtrees
     *
     * @return \Ibexa\Contracts\Core\Repository\LocationService
     */
    public function getLocationService(): LocationService;

    /**
     * Get Content Trash service.
     *
     * Trash service allows to perform operations related to location trash
     * (trash/untrash, load/list from trash...)
     *
     * @return \Ibexa\Contracts\Core\Repository\TrashService
     */
    public function getTrashService(): TrashService;

    /**
     * Get Content Section Service.
     *
     * Get Section service that lets you manipulate section objects
     *
     * @return \Ibexa\Contracts\Core\Repository\SectionService
     */
    public function getSectionService(): SectionService;

    /**
     * Get Search Service.
     *
     * Get search service that lets you find content objects
     *
     * @return \Ibexa\Contracts\Core\Repository\SearchService
     */
    public function getSearchService(): SearchService;

    /**
     * Get User Service.
     *
     * Get service object to perform operations on Users and UserGroup
     *
     * @return \Ibexa\Contracts\Core\Repository\UserService
     */
    public function getUserService(): UserService;

    /**
     * Get URLAliasService.
     *
     * @return \Ibexa\Contracts\Core\Repository\URLAliasService
     */
    public function getURLAliasService(): URLAliasService;

    /**
     * Get URLWildcardService.
     *
     * @return \Ibexa\Contracts\Core\Repository\URLWildcardService
     */
    public function getURLWildcardService(): URLWildcardService;

    /**
     * Get ObjectStateService.
     *
     * @return \Ibexa\Contracts\Core\Repository\ObjectStateService
     */
    public function getObjectStateService(): ObjectStateService;

    /**
     * Get RoleService.
     *
     * @return \Ibexa\Contracts\Core\Repository\RoleService
     */
    public function getRoleService(): RoleService;

    /**
     * Get FieldTypeService.
     *
     * @return \Ibexa\Contracts\Core\Repository\FieldTypeService
     */
    public function getFieldTypeService(): FieldTypeService;

    /**
     * Get PermissionResolver.
     *
     * @return \Ibexa\Contracts\Core\Repository\PermissionResolver
     */
    public function getPermissionResolver(): PermissionResolver;

    /**
     * Get URLService.
     *
     * @return \Ibexa\Contracts\Core\Repository\URLService
     */
    public function getURLService(): URLService;

    /**
     * Get BookmarkService.
     *
     * @return \Ibexa\Contracts\Core\Repository\BookmarkService
     */
    public function getBookmarkService(): BookmarkService;

    /**
     * Get NotificationService.
     *
     * @return \Ibexa\Contracts\Core\Repository\NotificationService
     */
    public function getNotificationService(): NotificationService;

    /**
     * Get UserPreferenceService.
     *
     * @return \Ibexa\Contracts\Core\Repository\UserPreferenceService
     */
    public function getUserPreferenceService(): UserPreferenceService;

    /**
     * Begin transaction.
     *
     * Begins an transaction, make sure you'll call commit or rollback when done,
     * otherwise work will be lost.
     */
    public function beginTransaction(): void;

    /**
     * Commit transaction.
     *
     * Commit transaction, or throw exceptions if no transactions has been started.
     *
     * @throws \RuntimeException If no transaction has been started
     */
    public function commit(): void;

    /**
     * Rollback transaction.
     *
     * Rollback transaction, or throw exceptions if no transactions has been started.
     *
     * @throws \RuntimeException If no transaction has been started
     */
    public function rollback(): void;
}

class_alias(Repository::class, 'eZ\Publish\API\Repository\Repository');
