<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Core\Repository\ProxyFactory;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\Repository\Values\Content\Section;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeGroup;
use Ibexa\Contracts\Core\Repository\Values\User\User;

/**
 * @internal
 */
interface ProxyDomainMapperInterface
{
    public function createContentProxy(int $contentId, array $prioritizedLanguages = Language::ALL, bool $useAlwaysAvailable = true): Content;

    public function createContentInfoProxy(int $contentId): ContentInfo;

    public function createContentTypeProxy(int $contentTypeId, array $prioritizedLanguages = Language::ALL): ContentType;

    public function createContentTypeGroupProxy(int $contentTypeGroupId, array $prioritizedLanguages = Language::ALL): ContentTypeGroup;

    public function createContentTypeGroupProxyList(array $contentTypeGroupIds, array $prioritizedLanguages = Language::ALL): array;

    public function createLanguageProxy(string $languageCode): Language;

    public function createLanguageProxyList(array $languageCodes): array;

    public function createLocationProxy(int $locationId, array $prioritizedLanguages = Language::ALL): Location;

    public function createSectionProxy(int $sectionId): Section;

    public function createUserProxy(int $userId, array $prioritizedLanguages = Language::ALL): User;
}

class_alias(ProxyDomainMapperInterface::class, 'eZ\Publish\Core\Repository\ProxyFactory\ProxyDomainMapperInterface');
