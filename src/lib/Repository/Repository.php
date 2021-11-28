<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\Repository;

use Exception;
use Ibexa\Contracts\Core\Persistence\Filter\Content\Handler as ContentFilteringHandler;
use Ibexa\Contracts\Core\Persistence\Filter\Location\Handler as LocationFilteringHandler;
use Ibexa\Contracts\Core\Persistence\Handler as PersistenceHandler;
use Ibexa\Contracts\Core\Repository\BookmarkService as BookmarkServiceInterface;
use Ibexa\Contracts\Core\Repository\ContentService as ContentServiceInterface;
use Ibexa\Contracts\Core\Repository\ContentTypeService as ContentTypeServiceInterface;
use Ibexa\Contracts\Core\Repository\FieldTypeService as FieldTypeServiceInterface;
use Ibexa\Contracts\Core\Repository\LanguageResolver;
use Ibexa\Contracts\Core\Repository\LanguageService as LanguageServiceInterface;
use Ibexa\Contracts\Core\Repository\LocationService as LocationServiceInterface;
use Ibexa\Contracts\Core\Repository\NotificationService as NotificationServiceInterface;
use Ibexa\Contracts\Core\Repository\ObjectStateService as ObjectStateServiceInterface;
use Ibexa\Contracts\Core\Repository\PasswordHashService;
use Ibexa\Contracts\Core\Repository\PermissionCriterionResolver as PermissionCriterionResolverInterface;
use Ibexa\Contracts\Core\Repository\PermissionResolver as PermissionResolverInterface;
use Ibexa\Contracts\Core\Repository\PermissionService;
use Ibexa\Contracts\Core\Repository\Repository as RepositoryInterface;
use Ibexa\Contracts\Core\Repository\RoleService as RoleServiceInterface;
use Ibexa\Contracts\Core\Repository\SearchService as SearchServiceInterface;
use Ibexa\Contracts\Core\Repository\SectionService as SectionServiceInterface;
use Ibexa\Contracts\Core\Repository\Strategy\ContentThumbnail\ThumbnailStrategy;
use Ibexa\Contracts\Core\Repository\TrashService as TrashServiceInterface;
use Ibexa\Contracts\Core\Repository\URLAliasService as URLAliasServiceInterface;
use Ibexa\Contracts\Core\Repository\URLService as URLServiceInterface;
use Ibexa\Contracts\Core\Repository\URLWildcardService as URLWildcardServiceInterface;
use Ibexa\Contracts\Core\Repository\UserPreferenceService as UserPreferenceServiceInterface;
use Ibexa\Contracts\Core\Repository\UserService as UserServiceInterface;
use Ibexa\Contracts\Core\Repository\Validator\ContentValidator;
use Ibexa\Contracts\Core\Search\Handler as SearchHandler;
use Ibexa\Core\FieldType\FieldTypeRegistry;
use Ibexa\Core\Repository\Helper\NameSchemaService;
use Ibexa\Core\Repository\Helper\RelationProcessor;
use Ibexa\Core\Repository\Permission\LimitationService;
use Ibexa\Core\Repository\ProxyFactory\ProxyDomainMapperFactoryInterface;
use Ibexa\Core\Repository\ProxyFactory\ProxyDomainMapperInterface;
use Ibexa\Core\Repository\User\PasswordValidatorInterface;
use Ibexa\Core\Search\Common\BackgroundIndexer;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use RuntimeException;

/**
 * Repository class.
 */
class Repository implements RepositoryInterface
{
    /**
     * Repository Handler object.
     *
     * @var \Ibexa\Contracts\Core\Persistence\Handler
     */
    protected $persistenceHandler;

    /**
     * Instance of main Search Handler.
     *
     * @var \Ibexa\Contracts\Core\Search\Handler
     */
    protected $searchHandler;

    /**
     * Instance of content service.
     *
     * @var \Ibexa\Contracts\Core\Repository\ContentService
     */
    protected $contentService;

    /**
     * Instance of section service.
     *
     * @var \Ibexa\Contracts\Core\Repository\SectionService
     */
    protected $sectionService;

    /**
     * Instance of role service.
     *
     * @var \Ibexa\Contracts\Core\Repository\RoleService
     */
    protected $roleService;

    /**
     * Instance of search service.
     *
     * @var \Ibexa\Contracts\Core\Repository\SearchService
     */
    protected $searchService;

    /**
     * Instance of user service.
     *
     * @var \Ibexa\Contracts\Core\Repository\UserService
     */
    protected $userService;

    /**
     * Instance of language service.
     *
     * @var \Ibexa\Contracts\Core\Repository\LanguageService
     */
    protected $languageService;

    /**
     * Instance of location service.
     *
     * @var \Ibexa\Contracts\Core\Repository\LocationService
     */
    protected $locationService;

    /**
     * Instance of Trash service.
     *
     * @var \Ibexa\Contracts\Core\Repository\TrashService
     */
    protected $trashService;

    /**
     * Instance of content type service.
     *
     * @var \Ibexa\Contracts\Core\Repository\ContentTypeService
     */
    protected $contentTypeService;

    /**
     * Instance of object state service.
     *
     * @var \Ibexa\Contracts\Core\Repository\ObjectStateService
     */
    protected $objectStateService;

    /**
     * Instance of field type service.
     *
     * @var \Ibexa\Contracts\Core\Repository\FieldTypeService
     */
    protected $fieldTypeService;

    /** @var \Ibexa\Core\FieldType\FieldTypeRegistry */
    private $fieldTypeRegistry;

    /**
     * Instance of name schema resolver service.
     *
     * @var \Ibexa\Core\Repository\Helper\NameSchemaService
     */
    protected $nameSchemaService;

    /**
     * Instance of relation processor service.
     *
     * @var \Ibexa\Core\Repository\Helper\RelationProcessor
     */
    protected $relationProcessor;

    /**
     * Instance of URL alias service.
     *
     * @var \Ibexa\Core\Repository\URLAliasService
     */
    protected $urlAliasService;

    /**
     * Instance of URL wildcard service.
     *
     * @var \Ibexa\Core\Repository\URLWildcardService
     */
    protected $urlWildcardService;

    /**
     * Instance of URL service.
     *
     * @var \Ibexa\Core\Repository\URLService
     */
    protected $urlService;

    /**
     * Instance of Bookmark service.
     *
     * @var \Ibexa\Contracts\Core\Repository\BookmarkService
     */
    protected $bookmarkService;

    /**
     * Instance of Notification service.
     *
     * @var \Ibexa\Contracts\Core\Repository\NotificationService
     */
    protected $notificationService;

    /**
     * Instance of User Preference service.
     *
     * @var \Ibexa\Contracts\Core\Repository\UserPreferenceService
     */
    protected $userPreferenceService;

    /**
     * Service settings, first level key is service name.
     *
     * @var array
     */
    protected $serviceSettings;

    /** @var \Ibexa\Core\Repository\Permission\LimitationService */
    protected $limitationService;

    /** @var \Ibexa\Core\Repository\Mapper\RoleDomainMapper */
    protected $roleDomainMapper;

    /** @var \Ibexa\Core\Repository\Mapper\ContentDomainMapper */
    protected $contentDomainMapper;

    /** @var \Ibexa\Core\Repository\Mapper\ContentTypeDomainMapper */
    protected $contentTypeDomainMapper;

    /** @var \Ibexa\Core\Search\Common\BackgroundIndexer|null */
    protected $backgroundIndexer;

    /** @var \Psr\Log\LoggerInterface */
    private $logger;

    /** @var \Ibexa\Contracts\Core\Repository\PasswordHashService */
    private $passwordHashService;

    /** @var \Ibexa\Contracts\Core\Repository\Strategy\ContentThumbnail\ThumbnailStrategy */
    private $thumbnailStrategy;

    /** @var \Ibexa\Core\Repository\ProxyFactory\ProxyDomainMapperFactory */
    private $proxyDomainMapperFactory;

    /** @var \Ibexa\Core\Repository\ProxyFactory\ProxyDomainMapperInterface|null */
    private $proxyDomainMapper;

    /** @var \Ibexa\Contracts\Core\Repository\LanguageResolver */
    private $languageResolver;

    /** @var \Ibexa\Contracts\Core\Repository\PermissionService */
    private $permissionService;

    /** @var \Ibexa\Core\Repository\Mapper\ContentMapper */
    private $contentMapper;

    /** @var \Ibexa\Contracts\Core\Repository\Validator\ContentValidator */
    private $contentValidator;

    /** @var \Ibexa\Contracts\Core\Persistence\Filter\Content\Handler */
    private $contentFilteringHandler;

    /** @var \Ibexa\Contracts\Core\Persistence\Filter\Location\Handler */
    private $locationFilteringHandler;

    /** @var \Ibexa\Core\Repository\User\PasswordValidatorInterface */
    private $passwordValidator;

    public function __construct(
        PersistenceHandler $persistenceHandler,
        SearchHandler $searchHandler,
        BackgroundIndexer $backgroundIndexer,
        RelationProcessor $relationProcessor,
        FieldTypeRegistry $fieldTypeRegistry,
        PasswordHashService $passwordHashGenerator,
        ThumbnailStrategy $thumbnailStrategy,
        ProxyDomainMapperFactoryInterface $proxyDomainMapperFactory,
        Mapper\ContentDomainMapper $contentDomainMapper,
        Mapper\ContentTypeDomainMapper $contentTypeDomainMapper,
        Mapper\RoleDomainMapper $roleDomainMapper,
        Mapper\ContentMapper $contentMapper,
        ContentValidator $contentValidator,
        LimitationService $limitationService,
        LanguageResolver $languageResolver,
        PermissionService $permissionService,
        ContentFilteringHandler $contentFilteringHandler,
        LocationFilteringHandler $locationFilteringHandler,
        PasswordValidatorInterface $passwordValidator,
        array $serviceSettings = [],
        ?LoggerInterface $logger = null
    ) {
        $this->persistenceHandler = $persistenceHandler;
        $this->searchHandler = $searchHandler;
        $this->backgroundIndexer = $backgroundIndexer;
        $this->relationProcessor = $relationProcessor;
        $this->fieldTypeRegistry = $fieldTypeRegistry;
        $this->passwordHashService = $passwordHashGenerator;
        $this->thumbnailStrategy = $thumbnailStrategy;
        $this->proxyDomainMapperFactory = $proxyDomainMapperFactory;
        $this->contentDomainMapper = $contentDomainMapper;
        $this->contentTypeDomainMapper = $contentTypeDomainMapper;
        $this->roleDomainMapper = $roleDomainMapper;
        $this->limitationService = $limitationService;
        $this->languageResolver = $languageResolver;
        $this->contentFilteringHandler = $contentFilteringHandler;
        $this->permissionService = $permissionService;
        $this->locationFilteringHandler = $locationFilteringHandler;

        $this->serviceSettings = $serviceSettings + [
                'content' => [],
                'contentType' => [],
                'location' => [],
                'section' => [],
                'role' => [],
                'user' => [
                    'anonymousUserID' => 10,
                ],
                'language' => [],
                'trash' => [],
                'io' => [],
                'objectState' => [],
                'search' => [],
                'urlAlias' => [],
                'urlWildcard' => [],
                'nameSchema' => [],
                'languages' => [],
                'proxy_factory' => [],
            ];

        if (!empty($this->serviceSettings['languages'])) {
            $this->serviceSettings['language']['languages'] = $this->serviceSettings['languages'];
        }

        $this->logger = null !== $logger ? $logger : new NullLogger();
        $this->contentMapper = $contentMapper;
        $this->contentValidator = $contentValidator;
        $this->passwordValidator = $passwordValidator;
    }

    /**
     * {@inheritdoc}
     */
    public function sudo(callable $callback, ?RepositoryInterface $outerRepository = null)
    {
        return $this->getPermissionResolver()->sudo($callback, $outerRepository ?? $this);
    }

    /**
     * Get Content Service.
     *
     * Get service object to perform operations on Content objects and it's aggregate members.
     *
     * @return \Ibexa\Contracts\Core\Repository\ContentService
     */
    public function getContentService(): ContentServiceInterface
    {
        if ($this->contentService !== null) {
            return $this->contentService;
        }

        $this->contentService = new ContentService(
            $this,
            $this->persistenceHandler,
            $this->contentDomainMapper,
            $this->getRelationProcessor(),
            $this->getNameSchemaService(),
            $this->fieldTypeRegistry,
            $this->getPermissionService(),
            $this->contentMapper,
            $this->contentValidator,
            $this->contentFilteringHandler,
            $this->serviceSettings['content'],
        );

        return $this->contentService;
    }

    /**
     * Get Content Language Service.
     *
     * Get service object to perform operations on Content language objects
     *
     * @return \Ibexa\Contracts\Core\Repository\LanguageService
     */
    public function getContentLanguageService(): LanguageServiceInterface
    {
        if ($this->languageService !== null) {
            return $this->languageService;
        }

        $this->languageService = new LanguageService(
            $this,
            $this->persistenceHandler->contentLanguageHandler(),
            $this->getPermissionResolver(),
            $this->serviceSettings['language']
        );

        return $this->languageService;
    }

    /**
     * Get Content Type Service.
     *
     * Get service object to perform operations on Content Type objects and it's aggregate members.
     * ( Group, Field & FieldCategory )
     *
     * @return \Ibexa\Contracts\Core\Repository\ContentTypeService
     */
    public function getContentTypeService(): ContentTypeServiceInterface
    {
        if ($this->contentTypeService !== null) {
            return $this->contentTypeService;
        }

        $this->contentTypeService = new ContentTypeService(
            $this,
            $this->persistenceHandler->contentTypeHandler(),
            $this->persistenceHandler->userHandler(),
            $this->contentDomainMapper,
            $this->contentTypeDomainMapper,
            $this->fieldTypeRegistry,
            $this->getPermissionResolver(),
            $this->serviceSettings['contentType']
        );

        return $this->contentTypeService;
    }

    /**
     * Get Content Location Service.
     *
     * Get service object to perform operations on Location objects and subtrees
     *
     * @return \Ibexa\Contracts\Core\Repository\LocationService
     */
    public function getLocationService(): LocationServiceInterface
    {
        if ($this->locationService !== null) {
            return $this->locationService;
        }

        $this->locationService = new LocationService(
            $this,
            $this->persistenceHandler,
            $this->contentDomainMapper,
            $this->getNameSchemaService(),
            $this->getPermissionCriterionResolver(),
            $this->getPermissionResolver(),
            $this->locationFilteringHandler,
            $this->getContentTypeService(),
            $this->serviceSettings['location'],
            $this->logger
        );

        return $this->locationService;
    }

    /**
     * Get Content Trash service.
     *
     * Trash service allows to perform operations related to location trash
     * (trash/untrash, load/list from trash...)
     *
     * @return \Ibexa\Contracts\Core\Repository\TrashService
     */
    public function getTrashService(): TrashServiceInterface
    {
        if ($this->trashService !== null) {
            return $this->trashService;
        }

        $this->trashService = new TrashService(
            $this,
            $this->persistenceHandler,
            $this->getNameSchemaService(),
            $this->getPermissionCriterionResolver(),
            $this->getPermissionResolver(),
            $this->getProxyDomainMapper(),
            $this->serviceSettings['trash']
        );

        return $this->trashService;
    }

    /**
     * Get Content Section Service.
     *
     * Get Section service that lets you manipulate section objects
     *
     * @return \Ibexa\Contracts\Core\Repository\SectionService
     */
    public function getSectionService(): SectionServiceInterface
    {
        if ($this->sectionService !== null) {
            return $this->sectionService;
        }

        $this->sectionService = new SectionService(
            $this,
            $this->persistenceHandler->sectionHandler(),
            $this->persistenceHandler->locationHandler(),
            $this->getPermissionCriterionResolver(),
            $this->serviceSettings['section']
        );

        return $this->sectionService;
    }

    /**
     * Get User Service.
     *
     * Get service object to perform operations on Users and UserGroup
     *
     * @return \Ibexa\Contracts\Core\Repository\UserService
     */
    public function getUserService(): UserServiceInterface
    {
        if ($this->userService !== null) {
            return $this->userService;
        }

        $this->userService = new UserService(
            $this,
            $this->getPermissionResolver(),
            $this->persistenceHandler->userHandler(),
            $this->persistenceHandler->locationHandler(),
            $this->passwordHashService,
            $this->passwordValidator,
            $this->serviceSettings['user']
        );

        return $this->userService;
    }

    /**
     * Get URLAliasService.
     *
     * @return \Ibexa\Contracts\Core\Repository\URLAliasService
     */
    public function getURLAliasService(): URLAliasServiceInterface
    {
        if ($this->urlAliasService !== null) {
            return $this->urlAliasService;
        }

        $this->urlAliasService = new URLAliasService(
            $this,
            $this->persistenceHandler->urlAliasHandler(),
            $this->getNameSchemaService(),
            $this->getPermissionResolver(),
            $this->languageResolver
        );

        return $this->urlAliasService;
    }

    /**
     * Get URLWildcardService.
     *
     * @return \Ibexa\Contracts\Core\Repository\URLWildcardService
     */
    public function getURLWildcardService(): URLWildcardServiceInterface
    {
        if ($this->urlWildcardService !== null) {
            return $this->urlWildcardService;
        }

        $this->urlWildcardService = new URLWildcardService(
            $this,
            $this->persistenceHandler->urlWildcardHandler(),
            $this->getPermissionResolver(),
            $this->serviceSettings['urlWildcard']
        );

        return $this->urlWildcardService;
    }

    /**
     * Get URLService.
     *
     * @return \Ibexa\Contracts\Core\Repository\URLService
     */
    public function getURLService(): URLServiceInterface
    {
        if ($this->urlService !== null) {
            return $this->urlService;
        }

        $this->urlService = new URLService(
            $this,
            $this->persistenceHandler->urlHandler(),
            $this->getPermissionResolver()
        );

        return $this->urlService;
    }

    /**
     * Get BookmarkService.
     *
     * @return \Ibexa\Contracts\Core\Repository\BookmarkService
     */
    public function getBookmarkService(): BookmarkServiceInterface
    {
        if ($this->bookmarkService === null) {
            $this->bookmarkService = new BookmarkService(
                $this,
                $this->persistenceHandler->bookmarkHandler()
            );
        }

        return $this->bookmarkService;
    }

    /**
     * Get UserPreferenceService.
     *
     * @return \Ibexa\Contracts\Core\Repository\UserPreferenceService
     */
    public function getUserPreferenceService(): UserPreferenceServiceInterface
    {
        if ($this->userPreferenceService === null) {
            $this->userPreferenceService = new UserPreferenceService(
                $this,
                $this->persistenceHandler->userPreferenceHandler()
            );
        }

        return $this->userPreferenceService;
    }

    /**
     * Get ObjectStateService.
     *
     * @return \Ibexa\Contracts\Core\Repository\ObjectStateService
     */
    public function getObjectStateService(): ObjectStateServiceInterface
    {
        if ($this->objectStateService !== null) {
            return $this->objectStateService;
        }

        $this->objectStateService = new ObjectStateService(
            $this,
            $this->persistenceHandler->objectStateHandler(),
            $this->getPermissionResolver(),
            $this->serviceSettings['objectState']
        );

        return $this->objectStateService;
    }

    /**
     * Get RoleService.
     *
     * @return \Ibexa\Contracts\Core\Repository\RoleService
     */
    public function getRoleService(): RoleServiceInterface
    {
        if ($this->roleService !== null) {
            return $this->roleService;
        }

        $this->roleService = new RoleService(
            $this,
            $this->persistenceHandler->userHandler(),
            $this->limitationService,
            $this->getRoleDomainMapper(),
            $this->serviceSettings['role']
        );

        return $this->roleService;
    }

    protected function getRoleDomainMapper(): Mapper\RoleDomainMapper
    {
        return $this->roleDomainMapper;
    }

    /**
     * Get SearchService.
     *
     * @return \Ibexa\Contracts\Core\Repository\SearchService
     */
    public function getSearchService(): SearchServiceInterface
    {
        if ($this->searchService !== null) {
            return $this->searchService;
        }

        $this->searchService = new SearchService(
            $this,
            $this->searchHandler,
            $this->contentDomainMapper,
            $this->getPermissionCriterionResolver(),
            $this->backgroundIndexer,
            $this->serviceSettings['search']
        );

        return $this->searchService;
    }

    /**
     * Get FieldTypeService.
     *
     * @return \Ibexa\Contracts\Core\Repository\FieldTypeService
     */
    public function getFieldTypeService(): FieldTypeServiceInterface
    {
        if ($this->fieldTypeService !== null) {
            return $this->fieldTypeService;
        }

        $this->fieldTypeService = new FieldTypeService($this->fieldTypeRegistry);

        return $this->fieldTypeService;
    }

    public function getPermissionService(): PermissionService
    {
        return $this->permissionService;
    }

    public function getPermissionResolver(): PermissionResolverInterface
    {
        return $this->getPermissionService();
    }

    /**
     * Get NameSchemaResolverService.
     *
     *
     * @todo Move out from this & other repo instances when services becomes proper services in DIC terms using factory.
     *
     * @internal
     * @private
     *
     * @return \Ibexa\Core\Repository\Helper\NameSchemaService
     */
    public function getNameSchemaService(): NameSchemaService
    {
        if ($this->nameSchemaService !== null) {
            return $this->nameSchemaService;
        }

        $this->nameSchemaService = new Helper\NameSchemaService(
            $this->persistenceHandler->contentTypeHandler(),
            $this->contentTypeDomainMapper,
            $this->fieldTypeRegistry,
            $this->serviceSettings['nameSchema']
        );

        return $this->nameSchemaService;
    }

    /**
     * @return \Ibexa\Contracts\Core\Repository\NotificationService
     */
    public function getNotificationService(): NotificationServiceInterface
    {
        if (null !== $this->notificationService) {
            return $this->notificationService;
        }

        $this->notificationService = new NotificationService(
            $this->persistenceHandler->notificationHandler(),
            $this->getPermissionResolver()
        );

        return $this->notificationService;
    }

    /**
     * Get RelationProcessor.
     *
     *
     * @todo Move out from this & other repo instances when services becomes proper services in DIC terms using factory.
     *
     * @return \Ibexa\Core\Repository\Helper\RelationProcessor
     */
    protected function getRelationProcessor(): RelationProcessor
    {
        return $this->relationProcessor;
    }

    protected function getProxyDomainMapper(): ProxyDomainMapperInterface
    {
        if ($this->proxyDomainMapper !== null) {
            return $this->proxyDomainMapper;
        }

        $this->proxyDomainMapper = $this->proxyDomainMapperFactory->create($this);

        return $this->proxyDomainMapper;
    }

    protected function getPermissionCriterionResolver(): PermissionCriterionResolverInterface
    {
        return $this->getPermissionService();
    }

    /**
     * Begin transaction.
     *
     * Begins an transaction, make sure you'll call commit or rollback when done,
     * otherwise work will be lost.
     */
    public function beginTransaction(): void
    {
        $this->persistenceHandler->beginTransaction();
    }

    /**
     * Commit transaction.
     *
     * Commit transaction, or throw exceptions if no transactions has been started.
     *
     * @throws \RuntimeException If no transaction has been started
     */
    public function commit(): void
    {
        try {
            $this->persistenceHandler->commit();
        } catch (Exception $e) {
            throw new RuntimeException($e->getMessage(), 0, $e);
        }
    }

    /**
     * Rollback transaction.
     *
     * Rollback transaction, or throw exceptions if no transactions has been started.
     *
     * @throws \RuntimeException If no transaction has been started
     */
    public function rollback(): void
    {
        try {
            $this->persistenceHandler->rollback();
        } catch (Exception $e) {
            throw new RuntimeException($e->getMessage(), 0, $e);
        }
    }
}

class_alias(Repository::class, 'eZ\Publish\Core\Repository\Repository');
