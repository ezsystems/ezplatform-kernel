<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace eZ\Publish\Core\Repository\Tests\Service\Mock;

use eZ\Publish\API\Repository\LanguageResolver;
use eZ\Publish\API\Repository\PasswordHashService;
use eZ\Publish\API\Repository\PermissionService;
use eZ\Publish\API\Repository\Repository as APIRepository;
use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\Core\FieldType\FieldTypeRegistry;
use eZ\Publish\Core\Repository\FieldTypeService;
use eZ\Publish\Core\Repository\Helper\RelationProcessor;
use eZ\Publish\Core\Repository\Mapper\ContentDomainMapper;
use eZ\Publish\Core\Repository\Mapper\ContentMapper;
use eZ\Publish\Core\Repository\Mapper\ContentTypeDomainMapper;
use eZ\Publish\Core\Repository\Mapper\RoleDomainMapper;
use eZ\Publish\Core\Repository\Permission\LimitationService;
use eZ\Publish\Core\Repository\ProxyFactory\ProxyDomainMapperFactoryInterface;
use eZ\Publish\Core\Repository\Repository;
use eZ\Publish\Core\Repository\Strategy\ContentValidator\ContentValidatorStrategy;
use eZ\Publish\Core\Repository\User\PasswordValidatorInterface;
use eZ\Publish\Core\Repository\Validator\ContentCreateStructValidator;
use eZ\Publish\Core\Repository\Validator\ContentUpdateStructValidator;
use eZ\Publish\Core\Repository\Validator\VersionValidator;
use eZ\Publish\Core\Repository\Values\Content\Content;
use eZ\Publish\Core\Repository\Values\Content\VersionInfo;
use eZ\Publish\Core\Repository\Values\User\User;
use eZ\Publish\Core\Search\Common\BackgroundIndexer\NullIndexer;
use eZ\Publish\SPI\Persistence\Filter\Content\Handler as ContentFilteringHandler;
use eZ\Publish\SPI\Persistence\Filter\Location\Handler as LocationFilteringHandler;
use eZ\Publish\SPI\Persistence\Handler;
use eZ\Publish\SPI\Repository\Strategy\ContentThumbnail\ThumbnailStrategy;
use eZ\Publish\SPI\Repository\Validator\ContentValidator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Base test case for tests on services using Mock testing.
 */
abstract class Base extends TestCase
{
    /** @var \eZ\Publish\API\Repository\Repository */
    private $repository;

    /** @var \eZ\Publish\API\Repository\Repository|\PHPUnit\Framework\MockObject\MockObject */
    private $repositoryMock;

    /** @var \eZ\Publish\API\Repository\PermissionService|\PHPUnit\Framework\MockObject\MockObject */
    private $permissionServiceMock;

    /** @var \eZ\Publish\SPI\Persistence\Handler|\PHPUnit\Framework\MockObject\MockObject */
    private $persistenceMock;

    /** @var \eZ\Publish\SPI\Repository\Strategy\ContentThumbnail\ThumbnailStrategy|\PHPUnit\Framework\MockObject\MockObject */
    private $thumbnailStrategyMock;

    /**
     * The Content / Location / Search ... handlers for the persistence / Search / .. handler mocks.
     *
     * @var \PHPUnit\Framework\MockObject\MockObject[] Key is relative to "\eZ\Publish\SPI\"
     *
     * @see getPersistenceMockHandler()
     */
    private $spiMockHandlers = [];

    /** @var \PHPUnit\Framework\MockObject\MockObject|\eZ\Publish\Core\Repository\Mapper\ContentTypeDomainMapper */
    private $contentTypeDomainMapperMock;

    /** @var \PHPUnit\Framework\MockObject\MockObject|\eZ\Publish\Core\Repository\Mapper\ContentDomainMapper */
    private $contentDomainMapperMock;

    /** @var \PHPUnit\Framework\MockObject\MockObject|\eZ\Publish\Core\Repository\Permission\LimitationService */
    private $limitationServiceMock;

    /** @var \eZ\Publish\API\Repository\LanguageResolver|\PHPUnit\Framework\MockObject\MockObject */
    private $languageResolverMock;

    /** @var \eZ\Publish\Core\Repository\Mapper\RoleDomainMapper|\PHPUnit\Framework\MockObject\MockObject */
    protected $roleDomainMapperMock;

    /** @var \eZ\Publish\Core\Repository\Mapper\ContentMapper|\PHPUnit\Framework\MockObject\MockObject */
    protected $contentMapperMock;

    /** @var \eZ\Publish\SPI\Repository\Validator\ContentValidator|\PHPUnit\Framework\MockObject\MockObject */
    protected $contentValidatorStrategyMock;

    /** @var \eZ\Publish\SPI\Persistence\Filter\Content\Handler|\PHPUnit\Framework\MockObject\MockObject */
    private $contentFilteringHandlerMock;

    /** @var \eZ\Publish\SPI\Persistence\Filter\Location\Handler|\PHPUnit\Framework\MockObject\MockObject */
    private $locationFilteringHandlerMock;

    /**
     * Get Real repository with mocked dependencies.
     *
     * @param array $serviceSettings If set then non shared instance of Repository is returned
     *
     * @return \eZ\Publish\API\Repository\Repository
     */
    protected function getRepository(array $serviceSettings = [])
    {
        if ($this->repository === null || !empty($serviceSettings)) {
            $repository = new Repository(
                $this->getPersistenceMock(),
                $this->getSPIMockHandler('Search\\Handler'),
                new NullIndexer(),
                $this->getRelationProcessorMock(),
                $this->getFieldTypeRegistryMock(),
                $this->createMock(PasswordHashService::class),
                $this->getThumbnailStrategy(),
                $this->createMock(ProxyDomainMapperFactoryInterface::class),
                $this->getContentDomainMapperMock(),
                $this->getContentTypeDomainMapperMock(),
                $this->getRoleDomainMapperMock(),
                $this->getContentMapper(),
                $this->getContentValidatorStrategy(),
                $this->getLimitationServiceMock(),
                $this->getLanguageResolverMock(),
                $this->getPermissionServiceMock(),
                $this->getContentFilteringHandlerMock(),
                $this->getLocationFilteringHandlerMock(),
                $this->createMock(PasswordValidatorInterface::class),
                $serviceSettings,
            );

            if (!empty($serviceSettings)) {
                return $repository;
            }

            $this->repository = $repository;
        }

        return $this->repository;
    }

    protected $fieldTypeServiceMock;

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\eZ\Publish\API\Repository\FieldTypeService
     */
    protected function getFieldTypeServiceMock()
    {
        if (!isset($this->fieldTypeServiceMock)) {
            $this->fieldTypeServiceMock = $this->createMock(FieldTypeService::class);
        }

        return $this->fieldTypeServiceMock;
    }

    protected $fieldTypeRegistryMock;

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\eZ\Publish\Core\FieldType\FieldTypeRegistry
     */
    protected function getFieldTypeRegistryMock()
    {
        if (!isset($this->fieldTypeRegistryMock)) {
            $this->fieldTypeRegistryMock = $this->createMock(FieldTypeRegistry::class);
        }

        return $this->fieldTypeRegistryMock;
    }

    /**
     * @return \eZ\Publish\SPI\Repository\Strategy\ContentThumbnail\ThumbnailStrategy|\PHPUnit\Framework\MockObject\MockObject
     */
    protected function getThumbnailStrategy()
    {
        if (!isset($this->thumbnailStrategyMock)) {
            $this->thumbnailStrategyMock = $this->createMock(ThumbnailStrategy::class);
        }

        return $this->thumbnailStrategyMock;
    }

    /**
     * @return \eZ\Publish\API\Repository\Repository|\PHPUnit\Framework\MockObject\MockObject
     */
    protected function getRepositoryMock()
    {
        if (!isset($this->repositoryMock)) {
            $this->repositoryMock = $this->createMock(APIRepository::class);
        }

        return $this->repositoryMock;
    }

    /**
     * @return \eZ\Publish\API\Repository\PermissionResolver|\PHPUnit\Framework\MockObject\MockObject
     */
    protected function getPermissionResolverMock()
    {
        return $this->getPermissionServiceMock();
    }

    /**
     * @return \eZ\Publish\API\Repository\PermissionService|\PHPUnit\Framework\MockObject\MockObject
     */
    protected function getPermissionServiceMock(): PermissionService
    {
        if (!isset($this->permissionServiceMock)) {
            $this->permissionServiceMock = $this->createMock(PermissionService::class);
        }

        return $this->permissionServiceMock;
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\eZ\Publish\Core\Repository\Mapper\ContentDomainMapper
     */
    protected function getContentDomainMapperMock(): MockObject
    {
        if (!isset($this->contentDomainMapperMock)) {
            $this->contentDomainMapperMock = $this->createMock(ContentDomainMapper::class);
        }

        return $this->contentDomainMapperMock;
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\eZ\Publish\Core\Repository\Mapper\ContentTypeDomainMapper
     */
    protected function getContentTypeDomainMapperMock()
    {
        if (!isset($this->contentTypeDomainMapperMock)) {
            $this->contentTypeDomainMapperMock = $this->createMock(ContentTypeDomainMapper::class);
        }

        return $this->contentTypeDomainMapperMock;
    }

    /**
     * Returns a persistence Handler mock.
     *
     * @return \eZ\Publish\SPI\Persistence\Handler|\PHPUnit\Framework\MockObject\MockObject
     */
    protected function getPersistenceMock()
    {
        if (!isset($this->persistenceMock)) {
            $this->persistenceMock = $this->createMock(Handler::class);

            $this->persistenceMock->expects($this->any())
                ->method('contentHandler')
                ->will($this->returnValue($this->getPersistenceMockHandler('Content\\Handler')));

            $this->persistenceMock->expects($this->any())
                ->method('contentTypeHandler')
                ->will($this->returnValue($this->getPersistenceMockHandler('Content\\Type\\Handler')));

            $this->persistenceMock->expects($this->any())
                ->method('contentLanguageHandler')
                ->will($this->returnValue($this->getPersistenceMockHandler('Content\\Language\\Handler')));

            $this->persistenceMock->expects($this->any())
                ->method('locationHandler')
                ->will($this->returnValue($this->getPersistenceMockHandler('Content\\Location\\Handler')));

            $this->persistenceMock->expects($this->any())
                ->method('objectStateHandler')
                ->will($this->returnValue($this->getPersistenceMockHandler('Content\\ObjectState\\Handler')));

            $this->persistenceMock->expects($this->any())
                ->method('trashHandler')
                ->will($this->returnValue($this->getPersistenceMockHandler('Content\\Location\\Trash\\Handler')));

            $this->persistenceMock->expects($this->any())
                ->method('userHandler')
                ->will($this->returnValue($this->getPersistenceMockHandler('User\\Handler')));

            $this->persistenceMock->expects($this->any())
                ->method('sectionHandler')
                ->will($this->returnValue($this->getPersistenceMockHandler('Content\\Section\\Handler')));

            $this->persistenceMock->expects($this->any())
                ->method('urlAliasHandler')
                ->will($this->returnValue($this->getPersistenceMockHandler('Content\\UrlAlias\\Handler')));

            $this->persistenceMock->expects($this->any())
                ->method('urlWildcardHandler')
                ->will($this->returnValue($this->getPersistenceMockHandler('Content\\UrlWildcard\\Handler')));

            $this->persistenceMock->expects($this->any())
                ->method('urlWildcardHandler')
                ->will($this->returnValue($this->getPersistenceMockHandler('URL\\Handler')));
        }

        return $this->persistenceMock;
    }

    protected function getRelationProcessorMock()
    {
        return $this->createMock(RelationProcessor::class);
    }

    /**
     * Returns a SPI Handler mock.
     *
     * @param string $handler For instance "Content\Type\Handler" or "Search\Handler", must be relative to "eZ\Publish\SPI"
     *
     * @return \PHPUnit\Framework\MockObject\MockObject
     */
    protected function getSPIMockHandler($handler)
    {
        if (!isset($this->spiMockHandlers[$handler])) {
            $this->spiMockHandlers[$handler] = $this->getMockBuilder("eZ\\Publish\\SPI\\{$handler}")
                ->setMethods([])
                ->disableOriginalConstructor()
                ->setConstructorArgs([])
                ->getMock();
        }

        return $this->spiMockHandlers[$handler];
    }

    /**
     * Returns a persistence Handler mock.
     *
     * @param string $handler For instance "Content\Type\Handler", must be relative to "eZ\Publish\SPI\Persistence"
     *
     * @return \PHPUnit\Framework\MockObject\MockObject
     */
    protected function getPersistenceMockHandler($handler)
    {
        return $this->getSPIMockHandler("Persistence\\{$handler}");
    }

    /**
     * Returns User stub with $id as User/Content id.
     *
     * @param int $id
     *
     * @return \eZ\Publish\API\Repository\Values\User\User
     */
    protected function getStubbedUser($id)
    {
        return new User(
            [
                'content' => new Content(
                    [
                        'versionInfo' => new VersionInfo(
                            [
                                'contentInfo' => new ContentInfo(['id' => $id]),
                            ]
                        ),
                        'internalFields' => [],
                    ]
                ),
            ]
        );
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\eZ\Publish\Core\Repository\Permission\LimitationService
     */
    protected function getLimitationServiceMock(): MockObject
    {
        if ($this->limitationServiceMock === null) {
            $this->limitationServiceMock = $this->createMock(LimitationService::class);
        }

        return $this->limitationServiceMock;
    }

    protected function getLanguageResolverMock(): LanguageResolver
    {
        if ($this->languageResolverMock === null) {
            $this->languageResolverMock = $this->createMock(LanguageResolver::class);
        }

        return $this->languageResolverMock;
    }

    /**
     * @param string[] $methods
     *
     * @return \eZ\Publish\Core\Repository\Mapper\RoleDomainMapper|\PHPUnit\Framework\MockObject\MockObject
     */
    protected function getRoleDomainMapperMock(array $methods = []): RoleDomainMapper
    {
        if ($this->roleDomainMapperMock === null) {
            $mockBuilder = $this->getMockBuilder(RoleDomainMapper::class);
            if (!empty($methods)) {
                $mockBuilder->onlyMethods($methods);
            }
            $this->roleDomainMapperMock = $mockBuilder
                ->disableOriginalConstructor()
                ->getMock();
        }

        return $this->roleDomainMapperMock;
    }

    protected function getContentMapper(): ContentMapper
    {
        return new ContentMapper(
            $this->getPersistenceMock()->contentLanguageHandler(),
            $this->getFieldTypeRegistryMock()
        );
    }

    protected function getContentValidatorStrategy(): ContentValidator
    {
        $validators = [
            new ContentCreateStructValidator(
                $this->getContentMapper(),
                $this->getFieldTypeRegistryMock()
            ),
            new ContentUpdateStructValidator(
                $this->getContentMapper(),
                $this->getFieldTypeRegistryMock(),
                $this->getPersistenceMock()->contentLanguageHandler()
            ),
            new VersionValidator(
                $this->getFieldTypeRegistryMock(),
            ),
        ];

        return new ContentValidatorStrategy($validators);
    }

    protected function getContentFilteringHandlerMock(): ContentFilteringHandler
    {
        if (null === $this->contentFilteringHandlerMock) {
            $this->contentFilteringHandlerMock = $this->createMock(ContentFilteringHandler::class);
        }

        return $this->contentFilteringHandlerMock;
    }

    private function getLocationFilteringHandlerMock(): LocationFilteringHandler
    {
        if (null === $this->locationFilteringHandlerMock) {
            $this->locationFilteringHandlerMock = $this->createMock(LocationFilteringHandler::class);
        }

        return $this->locationFilteringHandlerMock;
    }
}
