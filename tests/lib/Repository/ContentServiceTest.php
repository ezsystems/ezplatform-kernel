<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Core\Repository;

use eZ\Publish\API\Repository\PermissionService;
use eZ\Publish\API\Repository\Repository;
use eZ\Publish\API\Repository\Values\Filter\Filter;
use eZ\Publish\Core\FieldType\FieldTypeRegistry;
use eZ\Publish\Core\Repository\ContentService;
use eZ\Publish\Core\Repository\Helper\NameSchemaService;
use eZ\Publish\Core\Repository\Helper\RelationProcessor;
use eZ\Publish\Core\Repository\Mapper\ContentDomainMapper;
use eZ\Publish\Core\Repository\Mapper\ContentMapper;
use eZ\Publish\SPI\Persistence\Filter\Content\Handler as ContentFilteringHandler;
use eZ\Publish\SPI\Persistence\Handler as PersistenceHandler;
use eZ\Publish\SPI\Repository\Validator\ContentValidator;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
final class ContentServiceTest extends TestCase
{
    /** @var \eZ\Publish\API\Repository\ContentService */
    private $contentService;

    protected function setUp(): void
    {
        $this->contentService = new ContentService(
            $this->createMock(Repository::class),
            $this->createMock(PersistenceHandler::class),
            $this->createMock(ContentDomainMapper::class),
            $this->createMock(RelationProcessor::class),
            $this->createMock(NameSchemaService::class),
            $this->createMock(FieldTypeRegistry::class),
            $this->createMock(PermissionService::class),
            $this->createMock(ContentMapper::class),
            $this->createMock(ContentValidator::class),
            $this->createMock(ContentFilteringHandler::class)
        );
    }

    public function testFindDoesNotModifyFilter(): void
    {
        $filter = new Filter();
        $originalFilter = clone $filter;
        $this->contentService->find($filter, ['eng-GB']);
        self::assertEquals($originalFilter, $filter);
    }

    public function testCountDoesNotModifyFilter(): void
    {
        $filter = new Filter();
        $originalFilter = clone $filter;
        $this->contentService->count($filter, ['eng-GB']);
        self::assertEquals($originalFilter, $filter);
    }
}
