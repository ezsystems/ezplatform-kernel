<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Core\Repository;

use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\PermissionCriterionResolver;
use eZ\Publish\API\Repository\PermissionResolver;
use eZ\Publish\API\Repository\Repository;
use eZ\Publish\API\Repository\Values\Filter\Filter;
use eZ\Publish\Core\Repository\Helper\NameSchemaService;
use eZ\Publish\Core\Repository\LocationService;
use eZ\Publish\Core\Repository\Mapper\ContentDomainMapper;
use eZ\Publish\SPI\Persistence\Filter\Location\Handler as LocationFilteringHandler;
use eZ\Publish\SPI\Persistence\Handler as PersistenceHandler;
use PHPUnit\Framework\TestCase;

final class LocationServiceTest extends TestCase
{
    /** @var \eZ\Publish\API\Repository\LocationService */
    private $locationService;

    protected function setUp(): void
    {
        $this->locationService = new LocationService(
            $this->createMock(Repository::class),
            $this->createMock(PersistenceHandler::class),
            $this->createMock(ContentDomainMapper::class),
            $this->createMock(NameSchemaService::class),
            $this->createMock(PermissionCriterionResolver::class),
            $this->createMock(PermissionResolver::class),
            $this->createMock(LocationFilteringHandler::class),
            $this->createMock(ContentTypeService::class)
        );
    }

    /**
     * @throws \eZ\Publish\API\Repository\Exceptions\Exception
     */
    public function testFindDoesNotModifyFilter(): void
    {
        $filter = new Filter();
        $originalFilter = clone $filter;
        $this->locationService->find($filter, ['eng-GB']);
        self::assertEquals($originalFilter, $filter);
    }

    public function testCountDoesNotModifyFilter(): void
    {
        $filter = new Filter();
        $originalFilter = clone $filter;
        $this->locationService->count($filter, ['eng-GB']);
        self::assertEquals($originalFilter, $filter);
    }
}
