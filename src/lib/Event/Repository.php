<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\Event;

use Ibexa\Contracts\Core\Repository\BookmarkService as BookmarkServiceInterface;
use Ibexa\Contracts\Core\Repository\ContentService as ContentServiceInterface;
use Ibexa\Contracts\Core\Repository\ContentTypeService as ContentTypeServiceInterface;
use Ibexa\Contracts\Core\Repository\FieldTypeService as FieldTypeServiceInterface;
use Ibexa\Contracts\Core\Repository\LanguageService as LanguageServiceInterface;
use Ibexa\Contracts\Core\Repository\LocationService as LocationServiceInterface;
use Ibexa\Contracts\Core\Repository\NotificationService as NotificationServiceInterface;
use Ibexa\Contracts\Core\Repository\ObjectStateService as ObjectStateServiceInterface;
use Ibexa\Contracts\Core\Repository\PermissionResolver as PermissionResolverInterface;
use Ibexa\Contracts\Core\Repository\Repository as RepositoryInterface;
use Ibexa\Contracts\Core\Repository\RoleService as RoleServiceInterface;
use Ibexa\Contracts\Core\Repository\SearchService as SearchServiceInterface;
use Ibexa\Contracts\Core\Repository\SectionService as SectionServiceInterface;
use Ibexa\Contracts\Core\Repository\TrashService as TrashServiceInterface;
use Ibexa\Contracts\Core\Repository\URLAliasService as URLAliasServiceInterface;
use Ibexa\Contracts\Core\Repository\URLService as URLServiceInterface;
use Ibexa\Contracts\Core\Repository\URLWildcardService as URLWildcardServiceInterface;
use Ibexa\Contracts\Core\Repository\UserPreferenceService as UserPreferenceServiceInterface;
use Ibexa\Contracts\Core\Repository\UserService as UserServiceInterface;

final class Repository implements RepositoryInterface
{
    /** @var \Ibexa\Contracts\Core\Repository\Repository */
    private $repository;

    /** @var \Ibexa\Contracts\Core\Repository\BookmarkService */
    private $bookmarkService;

    /** @var \Ibexa\Contracts\Core\Repository\ContentService */
    private $contentService;

    /** @var \Ibexa\Contracts\Core\Repository\ContentTypeService */
    private $contentTypeService;

    /** @var \Ibexa\Contracts\Core\Repository\FieldTypeService */
    private $fieldTypeService;

    /** @var \Ibexa\Contracts\Core\Repository\LanguageService */
    private $languageService;

    /** @var \Ibexa\Contracts\Core\Repository\LocationService */
    private $locationService;

    /** @var \Ibexa\Contracts\Core\Repository\NotificationService */
    private $notificationService;

    /** @var \Ibexa\Contracts\Core\Repository\ObjectStateService */
    private $objectStateService;

    /** @var \Ibexa\Contracts\Core\Repository\RoleService */
    private $roleService;

    /** @var \Ibexa\Contracts\Core\Repository\SearchService */
    private $searchService;

    /** @var \Ibexa\Contracts\Core\Repository\SectionService */
    private $sectionService;

    /** @var \Ibexa\Contracts\Core\Repository\TrashService */
    private $trashService;

    /** @var \Ibexa\Contracts\Core\Repository\URLAliasService */
    private $urlAliasService;

    /** @var \Ibexa\Contracts\Core\Repository\URLService */
    private $urlService;

    /** @var \Ibexa\Contracts\Core\Repository\URLWildcardService */
    private $urlWildcardService;

    /** @var \Ibexa\Contracts\Core\Repository\UserPreferenceService */
    private $userPreferenceService;

    /** @var \Ibexa\Contracts\Core\Repository\UserService */
    private $userService;

    public function __construct(
        RepositoryInterface $repository,
        BookmarkServiceInterface $bookmarkService,
        ContentServiceInterface $contentService,
        ContentTypeServiceInterface $contentTypeService,
        FieldTypeServiceInterface $fieldTypeService,
        LanguageServiceInterface $languageService,
        LocationServiceInterface $locationService,
        NotificationServiceInterface $notificationService,
        ObjectStateServiceInterface $objectStateService,
        RoleServiceInterface $roleService,
        SearchServiceInterface $searchService,
        SectionServiceInterface $sectionService,
        TrashServiceInterface $trashService,
        URLAliasServiceInterface $urlAliasService,
        URLServiceInterface $urlService,
        URLWildcardServiceInterface $urlWildcardService,
        UserPreferenceServiceInterface $userPreferenceService,
        UserServiceInterface $userService
    ) {
        $this->repository = $repository;
        $this->bookmarkService = $bookmarkService;
        $this->contentService = $contentService;
        $this->contentTypeService = $contentTypeService;
        $this->fieldTypeService = $fieldTypeService;
        $this->languageService = $languageService;
        $this->locationService = $locationService;
        $this->notificationService = $notificationService;
        $this->objectStateService = $objectStateService;
        $this->roleService = $roleService;
        $this->searchService = $searchService;
        $this->sectionService = $sectionService;
        $this->trashService = $trashService;
        $this->urlAliasService = $urlAliasService;
        $this->urlService = $urlService;
        $this->urlWildcardService = $urlWildcardService;
        $this->userPreferenceService = $userPreferenceService;
        $this->userService = $userService;
    }

    public function sudo(callable $callback, ?RepositoryInterface $outerRepository = null)
    {
        return $this->repository->sudo($callback, $outerRepository);
    }

    public function beginTransaction(): void
    {
        $this->repository->beginTransaction();
    }

    public function commit(): void
    {
        $this->repository->commit();
    }

    public function rollback(): void
    {
        $this->repository->rollback();
    }

    public function getPermissionResolver(): PermissionResolverInterface
    {
        return $this->repository->getPermissionResolver();
    }

    public function getBookmarkService(): BookmarkServiceInterface
    {
        return $this->bookmarkService;
    }

    public function getContentService(): ContentServiceInterface
    {
        return $this->contentService;
    }

    public function getContentTypeService(): ContentTypeServiceInterface
    {
        return $this->contentTypeService;
    }

    public function getFieldTypeService(): FieldTypeServiceInterface
    {
        return $this->fieldTypeService;
    }

    public function getContentLanguageService(): LanguageServiceInterface
    {
        return $this->languageService;
    }

    public function getLocationService(): LocationServiceInterface
    {
        return $this->locationService;
    }

    public function getNotificationService(): NotificationServiceInterface
    {
        return $this->notificationService;
    }

    public function getObjectStateService(): ObjectStateServiceInterface
    {
        return $this->objectStateService;
    }

    public function getRoleService(): RoleServiceInterface
    {
        return $this->roleService;
    }

    public function getSearchService(): SearchServiceInterface
    {
        return $this->searchService;
    }

    public function getSectionService(): SectionServiceInterface
    {
        return $this->sectionService;
    }

    public function getTrashService(): TrashServiceInterface
    {
        return $this->trashService;
    }

    public function getURLAliasService(): URLAliasServiceInterface
    {
        return $this->urlAliasService;
    }

    public function getURLService(): URLServiceInterface
    {
        return $this->urlService;
    }

    public function getURLWildcardService(): URLWildcardServiceInterface
    {
        return $this->urlWildcardService;
    }

    public function getUserPreferenceService(): UserPreferenceServiceInterface
    {
        return $this->userPreferenceService;
    }

    public function getUserService(): UserServiceInterface
    {
        return $this->userService;
    }
}

class_alias(Repository::class, 'eZ\Publish\Core\Event\Repository');
