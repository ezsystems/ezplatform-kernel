<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Test;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\LanguageService;
use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\ObjectStateService;
use eZ\Publish\API\Repository\PermissionResolver;
use eZ\Publish\API\Repository\RoleService;
use eZ\Publish\API\Repository\SearchService;
use eZ\Publish\API\Repository\SectionService;
use eZ\Publish\API\Repository\UserService;
use eZ\Publish\SPI\Persistence\TransactionHandler;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

abstract class IbexaKernelTestCase extends KernelTestCase
{
    protected static function getKernelClass(): string
    {
        return IbexaTestKernel::class;
    }

    /**
     * @template T
     * @phpstan-param class-string<T> $className
     * @phpstan-return T
     */
    protected static function getServiceByClassName(string $className, ?string $id = null)
    {
        $serviceId = IbexaTestKernel::SERVICE_ID_TO_TEST_SERVICE_MAP[$id ?? $className] ?? $id;
        assert(is_string($serviceId));

        $service = self::$container->get($serviceId);
        assert(is_object($service) && is_a($service, $className));

        return $service;
    }

    protected static function getContentTypeService(): ContentTypeService
    {
        return self::getServiceByClassName(ContentTypeService::class);
    }

    protected static function getContentService(): ContentService
    {
        return self::getServiceByClassName(ContentService::class);
    }

    protected static function getLocationService(): LocationService
    {
        return self::getServiceByClassName(LocationService::class);
    }

    protected static function getPermissionResolver(): PermissionResolver
    {
        return self::getServiceByClassName(PermissionResolver::class);
    }

    protected static function getRoleService(): RoleService
    {
        return self::getServiceByClassName(RoleService::class);
    }

    protected static function getSearchService(): SearchService
    {
        return self::getServiceByClassName(SearchService::class);
    }

    protected static function getTransactionHandler(): TransactionHandler
    {
        return self::getServiceByClassName(TransactionHandler::class);
    }

    protected static function getUserService(): UserService
    {
        return self::getServiceByClassName(UserService::class);
    }

    protected static function getObjectStateService(): ObjectStateService
    {
        return self::getServiceByClassName(ObjectStateService::class);
    }

    protected static function getLanguageService(): LanguageService
    {
        return self::getServiceByClassName(LanguageService::class);
    }

    protected static function getSectionService(): SectionService
    {
        return self::getServiceByClassName(SectionService::class);
    }
}
