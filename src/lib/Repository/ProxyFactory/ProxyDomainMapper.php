<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\Repository\ProxyFactory;

use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\Repository\Values\Content\Section;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeGroup;
use Ibexa\Contracts\Core\Repository\Values\User\User;
use ProxyManager\Proxy\LazyLoadingInterface;

/**
 * @internal
 */
final class ProxyDomainMapper implements ProxyDomainMapperInterface
{
    /** @var \Ibexa\Contracts\Core\Repository\Repository */
    private $repository;

    /** @var \ProxyManager\Factory\LazyLoadingValueHolderFactory */
    private $proxyGenerator;

    public function __construct(Repository $repository, ProxyGeneratorInterface $proxyGenerator)
    {
        $this->repository = $repository;
        $this->proxyGenerator = $proxyGenerator;
    }

    public function createContentProxy(
        int $contentId,
        array $prioritizedLanguages = Language::ALL,
        bool $useAlwaysAvailable = true
    ): Content {
        $initializer = function (
            &$wrappedObject,
            LazyLoadingInterface $proxy,
            $method,
            array $parameters,
            &$initializer
        ) use ($contentId, $prioritizedLanguages, $useAlwaysAvailable): bool {
            $initializer = null;
            $wrappedObject = $this->repository->getContentService()->loadContent(
                $contentId,
                $prioritizedLanguages,
                null,
                $useAlwaysAvailable
            );

            return true;
        };

        return $this->proxyGenerator->createProxy(Content::class, $initializer);
    }

    public function createContentInfoProxy(int $contentId): ContentInfo
    {
        $initializer = function (
            &$wrappedObject,
            LazyLoadingInterface $proxy,
            $method,
            array $parameters,
            &$initializer
        ) use ($contentId): bool {
            $initializer = null;
            $wrappedObject = $this->repository->getContentService()->loadContentInfo(
                $contentId
            );

            return true;
        };

        return $this->proxyGenerator->createProxy(ContentInfo::class, $initializer);
    }

    public function createContentTypeProxy(
        int $contentTypeId,
        array $prioritizedLanguages = Language::ALL
    ): ContentType {
        $initializer = function (
            &$wrappedObject,
            LazyLoadingInterface $proxy,
            $method,
            array $parameters,
            &$initializer
        ) use ($contentTypeId, $prioritizedLanguages): bool {
            $initializer = null;
            $wrappedObject = $this->repository->getContentTypeService()->loadContentType(
                $contentTypeId,
                $prioritizedLanguages
            );

            return true;
        };

        return $this->proxyGenerator->createProxy(ContentType::class, $initializer);
    }

    public function createContentTypeGroupProxy(
        int $contentTypeGroupId,
        array $prioritizedLanguages = Language::ALL
    ): ContentTypeGroup {
        $initializer = function (
            &$wrappedObject,
            LazyLoadingInterface $proxy,
            $method,
            array $parameters,
            &$initializer
        ) use ($contentTypeGroupId, $prioritizedLanguages): bool {
            $initializer = null;
            $wrappedObject = $this->repository->getContentTypeService()->loadContentTypeGroup(
                $contentTypeGroupId,
                $prioritizedLanguages
            );

            return true;
        };

        return $this->proxyGenerator->createProxy(ContentTypeGroup::class, $initializer);
    }

    public function createContentTypeGroupProxyList(
        array $contentTypeGroupIds,
        array $prioritizedLanguages = Language::ALL
    ): array {
        $groups = [];
        foreach ($contentTypeGroupIds as $contentTypeGroupId) {
            $groups[] = $this->createContentTypeGroupProxy($contentTypeGroupId, $prioritizedLanguages);
        }

        return $groups;
    }

    public function createLanguageProxy(string $languageCode): Language
    {
        $initializer = function (
            &$wrappedObject,
            LazyLoadingInterface $proxy,
            $method,
            array $parameters,
            &$initializer
        ) use ($languageCode): bool {
            $initializer = null;
            $wrappedObject = $this->repository->getContentLanguageService()->loadLanguage($languageCode);

            return true;
        };

        return $this->proxyGenerator->createProxy(Language::class, $initializer);
    }

    public function createLanguageProxyList(array $languageCodes): array
    {
        $languages = [];
        foreach ($languageCodes as $languageCode) {
            $languages[] = $this->createLanguageProxy($languageCode);
        }

        return $languages;
    }

    public function createLocationProxy(
        int $locationId,
        array $prioritizedLanguages = Language::ALL
    ): Location {
        $initializer = function (
            &$wrappedObject,
            LazyLoadingInterface $proxy,
            $method,
            array $parameters,
            &$initializer
        ) use ($locationId, $prioritizedLanguages): bool {
            $initializer = null;
            $wrappedObject = $this->repository->getLocationService()->loadLocation(
                $locationId,
                $prioritizedLanguages
            );

            return true;
        };

        return $this->proxyGenerator->createProxy(Location::class, $initializer);
    }

    public function createSectionProxy(int $sectionId): Section
    {
        $initializer = function (
            &$wrappedObject,
            LazyLoadingInterface $proxy,
            $method,
            array $parameters,
            &$initializer
        ) use ($sectionId): bool {
            $initializer = null;
            $wrappedObject = $this->repository->getSectionService()->loadSection($sectionId);

            return true;
        };

        return $this->proxyGenerator->createProxy(Section::class, $initializer);
    }

    public function createUserProxy(int $userId, array $prioritizedLanguages = Language::ALL): User
    {
        $initializer = function (
            &$wrappedObject,
            LazyLoadingInterface $proxy,
            $method,
            array $parameters,
            &$initializer
        ) use ($userId, $prioritizedLanguages): bool {
            $initializer = null;
            $wrappedObject = $this->repository->getUserService()->loadUser($userId, $prioritizedLanguages);

            return true;
        };

        return $this->proxyGenerator->createProxy(User::class, $initializer);
    }
}

class_alias(ProxyDomainMapper::class, 'eZ\Publish\Core\Repository\ProxyFactory\ProxyDomainMapper');
