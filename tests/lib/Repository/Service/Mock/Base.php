<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Core\Repository\Service\Mock;

use Ibexa\Contracts\Core\Persistence\Filter\Content\Handler as ContentFilteringHandler;
use Ibexa\Contracts\Core\Persistence\Filter\Location\Handler as LocationFilteringHandler;
use Ibexa\Contracts\Core\Persistence\Handler;
use Ibexa\Contracts\Core\Repository\LanguageResolver;
use Ibexa\Contracts\Core\Repository\PasswordHashService;
use Ibexa\Contracts\Core\Repository\PermissionService;
use Ibexa\Contracts\Core\Repository\Repository as APIRepository;
use Ibexa\Contracts\Core\Repository\Strategy\ContentThumbnail\ThumbnailStrategy;
use Ibexa\Contracts\Core\Repository\Validator\ContentValidator;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Core\FieldType\FieldTypeRegistry;
use Ibexa\Core\Repository\FieldTypeService;
use Ibexa\Core\Repository\Helper\RelationProcessor;
use Ibexa\Core\Repository\Mapper\ContentDomainMapper;
use Ibexa\Core\Repository\Mapper\ContentMapper;
use Ibexa\Core\Repository\Mapper\ContentTypeDomainMapper;
use Ibexa\Core\Repository\Mapper\RoleDomainMapper;
use Ibexa\Core\Repository\Permission\LimitationService;
use Ibexa\Core\Repository\ProxyFactory\ProxyDomainMapperFactoryInterface;
use Ibexa\Core\Repository\Repository;
use Ibexa\Core\Repository\Strategy\ContentValidator\ContentValidatorStrategy;
use Ibexa\Core\Repository\User\PasswordValidatorInterface;
use Ibexa\Core\Repository\Validator\ContentCreateStructValidator;
use Ibexa\Core\Repository\Validator\ContentUpdateStructValidator;
use Ibexa\Core\Repository\Validator\VersionValidator;
use Ibexa\Core\Repository\Values\Content\Content;
use Ibexa\Core\Repository\Values\Content\VersionInfo;
use Ibexa\Core\Repository\Values\User\User;
use Ibexa\Core\Search\Common\BackgroundIndexer\NullIndexer;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Base test case for tests on services using Mock testing.
 */
abstract class Base extends TestCase
{
    /** @var \Ibexa\Contracts\Core\Repository\Repository */
    private $repository;

    /** @var \Ibexa\Contracts\Core\Repository\Repository|\PHPUnit\Framework\MockObject\MockObject */
    private $repositoryMock;

    /** @var \Ibexa\Contracts\Core\Repository\PermissionService|\PHPUnit\Framework\MockObject\MockObject */
    private $permissionServiceMock;

    /** @var \Ibexa\Contracts\Core\Persistence\Handler|\PHPUnit\Framework\MockObject\MockObject */
    private $persistenceMock;

    /** @var \Ibexa\Contracts\Core\Repository\Strategy\ContentThumbnail\ThumbnailStrategy|\PHPUnit\Framework\MockObject\MockObject */
    private $thumbnailStrategyMock;

    /**
     * The Content / Location / Search ... handlers for the persistence / Search / .. handler mocks.
     *
     * @var \PHPUnit\Framework\MockObject\MockObject[] Key is relative to "Ibexa\Contracts\Core\"
     *
     * @see getPersistenceMockHandler()
     */
    private $spiMockHandlers = [];

    /** @var \PHPUnit\Framework\MockObject\MockObject|\Ibexa\Core\Repository\Mapper\ContentTypeDomainMapper */
    private $contentTypeDomainMapperMock;

    /** @var \PHPUnit\Framework\MockObject\MockObject|\Ibexa\Core\Repository\Mapper\ContentDomainMapper */
    private $contentDomainMapperMock;

    /** @var \PHPUnit\Framework\MockObject\MockObject|\Ibexa\Core\Repository\Permission\LimitationService */
    private $limitationServiceMock;

    /** @var \Ibexa\Contracts\Core\Repository\LanguageResolver|\PHPUnit\Framework\MockObject\MockObject */
    private $languageResolverMock;

    /** @var \Ibexa\Core\Repository\Mapper\RoleDomainMapper|\PHPUnit\Framework\MockObject\MockObject */
    protected $roleDomainMapperMock;

    /** @var \Ibexa\Core\Repository\Mapper\ContentMapper|\PHPUnit\Framework\MockObject\MockObject */
    protected $contentMapperMock;

    /** @var \Ibexa\Contracts\Core\Repository\Validator\ContentValidator|\PHPUnit\Framework\MockObject\MockObject */
    protected $contentValidatorStrategyMock;

    /** @var \Ibexa\Contracts\Core\Persistence\Filter\Content\Handler|\PHPUnit\Framework\MockObject\MockObject */
    private $contentFilteringHandlerMock;

    /** @var \Ibexa\Contracts\Core\Persistence\Filter\Location\Handler|\PHPUnit\Framework\MockObject\MockObject */
    private $locationFilteringHandlerMock;

    /**
     * Get Real repository with mocked dependencies.
     *
     * @param array $serviceSettings If set then non shared instance of Repository is returned
     *
     * @return \Ibexa\Contracts\Core\Repository\Repository
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
     * @return \PHPUnit\Framework\MockObject\MockObject|\Ibexa\Contracts\Core\Repository\FieldTypeService
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
     * @return \PHPUnit\Framework\MockObject\MockObject|\Ibexa\Core\FieldType\FieldTypeRegistry
     */
    protected function getFieldTypeRegistryMock()
    {
        if (!isset($this->fieldTypeRegistryMock)) {
            $this->fieldTypeRegistryMock = $this->createMock(FieldTypeRegistry::class);
        }

        return $this->fieldTypeRegistryMock;
    }

    /**
     * @return \Ibexa\Contracts\Core\Repository\Strategy\ContentThumbnail\ThumbnailStrategy|\PHPUnit\Framework\MockObject\MockObject
     */
    protected function getThumbnailStrategy()
    {
        if (!isset($this->thumbnailStrategyMock)) {
            $this->thumbnailStrategyMock = $this->createMock(ThumbnailStrategy::class);
        }

        return $this->thumbnailStrategyMock;
    }

    /**
     * @return \Ibexa\Contracts\Core\Repository\Repository|\PHPUnit\Framework\MockObject\MockObject
     */
    protected function getRepositoryMock()
    {
        if (!isset($this->repositoryMock)) {
            $this->repositoryMock = $this->createMock(APIRepository::class);
        }

        return $this->repositoryMock;
    }

    /**
     * @return \Ibexa\Contracts\Core\Repository\PermissionResolver|\PHPUnit\Framework\MockObject\MockObject
     */
    protected function getPermissionResolverMock()
    {
        return $this->getPermissionServiceMock();
    }

    /**
     * @return \Ibexa\Contracts\Core\Repository\PermissionService|\PHPUnit\Framework\MockObject\MockObject
     */
    protected function getPermissionServiceMock(): PermissionService
    {
        if (!isset($this->permissionServiceMock)) {
            $this->permissionServiceMock = $this->createMock(PermissionService::class);
        }

        return $this->permissionServiceMock;
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\Ibexa\Core\Repository\Mapper\ContentDomainMapper
     */
    protected function getContentDomainMapperMock(): MockObject
    {
        if (!isset($this->contentDomainMapperMock)) {
            $this->contentDomainMapperMock = $this->createMock(ContentDomainMapper::class);
        }

        return $this->contentDomainMapperMock;
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\Ibexa\Core\Repository\Mapper\ContentTypeDomainMapper
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
     * @return \Ibexa\Contracts\Core\Persistence\Handler|\PHPUnit\Framework\MockObject\MockObject
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
     * @param string $handler For instance "Content\Type\Handler" or "Search\Handler", must be relative to "Ibexa\Contracts\Core"
     *
     * @return \PHPUnit\Framework\MockObject\MockObject
     */
    protected function getSPIMockHandler($handler)
    {
        if (!isset($this->spiMockHandlers[$handler])) {
            $this->spiMockHandlers[$handler] = $this->getMockBuilder("Ibexa\\Contracts\\Core\\{$handler}")
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
     * @param string $handler For instance "Content\Type\Handler", must be relative to "Ibexa\Contracts\Core\Persistence"
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
     * @return \Ibexa\Contracts\Core\Repository\Values\User\User
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
     * @return \PHPUnit\Framework\MockObject\MockObject|\Ibexa\Core\Repository\Permission\LimitationService
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
     * @return \Ibexa\Core\Repository\Mapper\RoleDomainMapper|\PHPUnit\Framework\MockObject\MockObject
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

class_alias(Base::class, 'eZ\Publish\Core\Repository\Tests\Service\Mock\Base');
