<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\Repository\SiteAccessAware;

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

/**
 * Repository class.
 */
class Repository implements RepositoryInterface
{
    /** @var \Ibexa\Contracts\Core\Repository\Repository */
    protected $repository;

    /** @var \Ibexa\Contracts\Core\Repository\ContentService */
    protected $contentService;

    /** @var \Ibexa\Contracts\Core\Repository\SectionService */
    protected $sectionService;

    /** @var \Ibexa\Contracts\Core\Repository\SearchService */
    protected $searchService;

    /** @var \Ibexa\Contracts\Core\Repository\UserService */
    protected $userService;

    /** @var \Ibexa\Contracts\Core\Repository\LanguageService */
    protected $languageService;

    /** @var \Ibexa\Contracts\Core\Repository\LocationService */
    protected $locationService;

    /** @var \Ibexa\Contracts\Core\Repository\TrashService */
    protected $trashService;

    /** @var \Ibexa\Contracts\Core\Repository\ContentTypeService */
    protected $contentTypeService;

    /** @var \Ibexa\Contracts\Core\Repository\ObjectStateService */
    protected $objectStateService;

    /** @var \Ibexa\Contracts\Core\Repository\URLAliasService */
    protected $urlAliasService;

    /** @var \Ibexa\Core\Repository\NotificationService */
    protected $notificationService;

    /**
     * Construct repository object from aggregated repository.
     */
    public function __construct(
        RepositoryInterface $repository,
        ContentService $contentService,
        ContentTypeService $contentTypeService,
        ObjectStateService $objectStateService,
        URLAliasService $urlAliasService,
        UserService $userService,
        SearchService $searchService,
        SectionService $sectionService,
        TrashService $trashService,
        LocationService $locationService,
        LanguageService $languageService,
        NotificationService $notificationService
    ) {
        $this->repository = $repository;
        $this->contentService = $contentService;
        $this->contentTypeService = $contentTypeService;
        $this->objectStateService = $objectStateService;
        $this->urlAliasService = $urlAliasService;
        $this->userService = $userService;
        $this->searchService = $searchService;
        $this->sectionService = $sectionService;
        $this->trashService = $trashService;
        $this->locationService = $locationService;
        $this->languageService = $languageService;
        $this->notificationService = $notificationService;
    }

    public function sudo(callable $callback, ?RepositoryInterface $outerRepository = null)
    {
        return $this->repository->sudo($callback, $outerRepository ?? $this);
    }

    public function getContentService(): ContentServiceInterface
    {
        return $this->contentService;
    }

    public function getContentLanguageService(): LanguageServiceInterface
    {
        return $this->languageService;
    }

    public function getContentTypeService(): ContentTypeServiceInterface
    {
        return $this->contentTypeService;
    }

    public function getLocationService(): LocationServiceInterface
    {
        return $this->locationService;
    }

    public function getTrashService(): TrashServiceInterface
    {
        return $this->trashService;
    }

    public function getSectionService(): SectionServiceInterface
    {
        return $this->sectionService;
    }

    public function getUserService(): UserServiceInterface
    {
        return $this->userService;
    }

    public function getURLAliasService(): URLAliasServiceInterface
    {
        return $this->urlAliasService;
    }

    public function getURLWildcardService(): URLWildcardServiceInterface
    {
        return $this->repository->getURLWildcardService();
    }

    public function getObjectStateService(): ObjectStateServiceInterface
    {
        return $this->objectStateService;
    }

    public function getRoleService(): RoleServiceInterface
    {
        return $this->repository->getRoleService();
    }

    public function getSearchService(): SearchServiceInterface
    {
        return $this->searchService;
    }

    public function getFieldTypeService(): FieldTypeServiceInterface
    {
        return $this->repository->getFieldTypeService();
    }

    public function getPermissionResolver(): PermissionResolverInterface
    {
        return $this->repository->getPermissionResolver();
    }

    public function getURLService(): URLServiceInterface
    {
        return $this->repository->getURLService();
    }

    public function getBookmarkService(): BookmarkServiceInterface
    {
        return $this->repository->getBookmarkService();
    }

    public function getNotificationService(): NotificationServiceInterface
    {
        return $this->repository->getNotificationService();
    }

    public function getUserPreferenceService(): UserPreferenceServiceInterface
    {
        return $this->repository->getUserPreferenceService();
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
}

class_alias(Repository::class, 'eZ\Publish\Core\Repository\SiteAccessAware\Repository');
