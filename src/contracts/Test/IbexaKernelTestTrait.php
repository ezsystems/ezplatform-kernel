<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Test;

use Doctrine\DBAL\Connection;
use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\LanguageService;
use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\ObjectStateService;
use eZ\Publish\API\Repository\PermissionResolver;
use eZ\Publish\API\Repository\RoleService;
use eZ\Publish\API\Repository\SearchService;
use eZ\Publish\API\Repository\SectionService;
use eZ\Publish\API\Repository\Tests\LegacySchemaImporter;
use eZ\Publish\API\Repository\UserService;
use eZ\Publish\Core\Repository\Values\User\UserReference;
use eZ\Publish\SPI\Persistence\TransactionHandler;
use eZ\Publish\SPI\Tests\Persistence\FixtureImporter;
use RuntimeException;

/**
 *  @experimental
 */
trait IbexaKernelTestTrait
{
    private static $anonymousUserId = 10;

    private static $adminUserId = 10;

    final protected static function loadSchema(): void
    {
        $schemaImporter = self::getContainer()->get(LegacySchemaImporter::class);
        foreach (static::getSchemaFiles() as $schemaFile) {
            $schemaImporter->importSchema($schemaFile);
        }
    }

    /**
     * @return iterable<string>
     */
    protected static function getSchemaFiles(): iterable
    {
        yield from self::$kernel->getSchemaFiles();
    }

    final protected static function loadFixtures(): void
    {
        $fixtureImporter = self::getContainer()->get(FixtureImporter::class);
        foreach (static::getFixtures() as $fixture) {
            $fixtureImporter->import($fixture);
        }

        static::postLoadFixtures();
    }

    protected static function postLoadFixtures(): void
    {
    }

    /**
     * @return iterable<\eZ\Publish\SPI\Tests\Persistence\Fixture>
     */
    protected static function getFixtures(): iterable
    {
        yield from self::$kernel->getFixtures();
    }

    /**
     * @template T of object
     * @phpstan-param class-string<T> $className
     *
     * @return T
     */
    final protected static function getServiceByClassName(string $className, ?string $id = null): object
    {
        if (!self::$booted) {
            static::bootKernel();
        }

        $serviceId = self::getTestServiceId($id, $className);
        $service = self::getContainer()->get($serviceId);
        assert(is_object($service) && is_a($service, $className));

        return $service;
    }

    protected static function getTestServiceId(?string $id, string $className): string
    {
        $kernel = self::$kernel;
        if (!$kernel instanceof IbexaTestKernel) {
            throw new RuntimeException(sprintf(
                'Expected %s to be an instance of %s.',
                get_class($kernel),
                IbexaTestKernel::class,
            ));
        }

        $id = $id ?? $className;

        return $kernel->getAliasServiceId($id);
    }

    protected static function getDoctrineConnection(): Connection
    {
        return self::getServiceByClassName(Connection::class);
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

    protected static function setAnonymousUser(): void
    {
        self::getPermissionResolver()->setCurrentUserReference(new UserReference(self::$anonymousUserId));
    }

    protected static function setAdministratorUser(): void
    {
        self::getPermissionResolver()->setCurrentUserReference(new UserReference(self::$adminUserId));
    }
}
