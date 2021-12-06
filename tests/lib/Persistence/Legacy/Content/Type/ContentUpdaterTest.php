<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Core\Persistence\Legacy\Content\Type;

use Ibexa\Contracts\Core\Persistence\Content\Type;
use Ibexa\Core\Persistence\Legacy\Content\FieldValue\Converter;
use Ibexa\Core\Persistence\Legacy\Content\FieldValue\ConverterRegistry;
use Ibexa\Core\Persistence\Legacy\Content\Gateway;
use Ibexa\Core\Persistence\Legacy\Content\Mapper;
use Ibexa\Core\Persistence\Legacy\Content\StorageHandler;
use Ibexa\Core\Persistence\Legacy\Content\Type\ContentUpdater;
use Ibexa\Core\Persistence\Legacy\Content\Type\ContentUpdater\Action;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ibexa\Core\Persistence\Legacy\Content\Type\ContentUpdater
 */
class ContentUpdaterTest extends TestCase
{
    /**
     * Content gateway mock.
     *
     * @var \Ibexa\Core\Persistence\Legacy\Content\Gateway
     */
    protected $contentGatewayMock;

    /**
     * FieldValue converter registry mock.
     *
     * @var \Ibexa\Core\Persistence\Legacy\Content\FieldValue\ConverterRegistry
     */
    protected $converterRegistryMock;

    /**
     * Search handler mock.
     *
     * @var \Ibexa\Core\Search\Legacy\Content\Handler
     */
    protected $searchHandlerMock;

    /**
     * Content StorageHandler mock.
     *
     * @var \Ibexa\Core\Persistence\Legacy\Content\StorageHandler
     */
    protected $contentStorageHandlerMock;

    /**
     * Content Updater to test.
     *
     * @var \Ibexa\Core\Persistence\Legacy\Content\Type\ContentUpdater
     */
    protected $contentUpdater;

    /** @var \Ibexa\Core\Persistence\Legacy\Content\Mapper */
    protected $contentMapperMock;

    public function testDetermineActions()
    {
        $fromType = $this->getFromTypeFixture();
        $toType = $this->getToTypeFixture();

        $converterRegMock = $this->getConverterRegistryMock();
        $converterRegMock->expects($this->once())
            ->method('getConverter')
            ->with('ezstring')
            ->will(
                $this->returnValue(
                    ($converterMock = $this->createMock(Converter::class))
                )
            );

        $updater = $this->getContentUpdater();

        $actions = $updater->determineActions($fromType, $toType);

        $this->assertEquals(
            [
                new ContentUpdater\Action\RemoveField(
                    $this->getContentGatewayMock(),
                    $fromType->fieldDefinitions[0],
                    $this->getContentStorageHandlerMock(),
                    $this->getContentMapperMock()
                ),
                new ContentUpdater\Action\AddField(
                    $this->getContentGatewayMock(),
                    $toType->fieldDefinitions[2],
                    $converterMock,
                    $this->getContentStorageHandlerMock(),
                    $this->getContentMapperMock()
                ),
            ],
            $actions
        );
    }

    public function testApplyUpdates()
    {
        $updater = $this->getContentUpdater();

        $actionA = $this->getMockForAbstractClass(
            Action::class,
            [],
            '',
            false
        );
        $actionA->expects($this->at(0))
            ->method('apply')
            ->with(11);
        $actionA->expects($this->at(1))
            ->method('apply')
            ->with(22);
        $actionB = $this->getMockForAbstractClass(
            Action::class,
            [],
            '',
            false
        );
        $actionB->expects($this->at(0))
            ->method('apply')
            ->with(11);
        $actionB->expects($this->at(1))
            ->method('apply')
            ->with(22);

        $actions = [$actionA, $actionB];

        $this->getContentGatewayMock()
            ->expects($this->once())
            ->method('getContentIdsByContentTypeId')
            ->with(23)
            ->will(
                $this->returnValue([11, 22])
            );

        $updater->applyUpdates(23, $actions);
    }

    /**
     * Returns a fixture for the from Type.
     *
     * @return \Ibexa\Contracts\Core\Persistence\Content\Type
     */
    protected function getFromTypeFixture()
    {
        $type = new Type();

        $fieldA = new Type\FieldDefinition();
        $fieldA->id = 1;
        $fieldA->fieldType = 'ezstring';

        $fieldB = new Type\FieldDefinition();
        $fieldB->id = 2;
        $fieldB->fieldType = 'ezstring';

        $type->fieldDefinitions = [
            $fieldA, $fieldB,
        ];

        return $type;
    }

    /**
     * Returns a fixture for the to Type.
     *
     * @return \Ibexa\Contracts\Core\Persistence\Content\Type
     */
    protected function getToTypeFixture()
    {
        $type = clone $this->getFromTypeFixture();

        unset($type->fieldDefinitions[0]);

        $fieldC = new Type\FieldDefinition();
        $fieldC->id = 3;
        $fieldC->fieldType = 'ezstring';

        $type->fieldDefinitions[] = $fieldC;

        return $type;
    }

    /**
     * Returns a Content Gateway mock.
     *
     * @return \Ibexa\Core\Persistence\Legacy\Content\Gateway
     */
    protected function getContentGatewayMock()
    {
        if (!isset($this->contentGatewayMock)) {
            $this->contentGatewayMock = $this->createMock(Gateway::class);
        }

        return $this->contentGatewayMock;
    }

    /**
     * Returns a FieldValue Converter registry mock.
     *
     * @return \Ibexa\Core\Persistence\Legacy\Content\FieldValue\ConverterRegistry
     */
    protected function getConverterRegistryMock()
    {
        if (!isset($this->converterRegistryMock)) {
            $this->converterRegistryMock = $this->createMock(ConverterRegistry::class);
        }

        return $this->converterRegistryMock;
    }

    /**
     * Returns a Content StorageHandler mock.
     *
     * @return \Ibexa\Core\Persistence\Legacy\Content\StorageHandler
     */
    protected function getContentStorageHandlerMock()
    {
        if (!isset($this->contentStorageHandlerMock)) {
            $this->contentStorageHandlerMock = $this->createMock(StorageHandler::class);
        }

        return $this->contentStorageHandlerMock;
    }

    /**
     * Returns a Content mapper mock.
     *
     * @return \Ibexa\Core\Persistence\Legacy\Content\Mapper
     */
    protected function getContentMapperMock()
    {
        if (!isset($this->contentMapperMock)) {
            $this->contentMapperMock = $this->createMock(Mapper::class);
        }

        return $this->contentMapperMock;
    }

    /**
     * Returns the content updater to test.
     *
     * @return \Ibexa\Core\Persistence\Legacy\Content\Type\ContentUpdater
     */
    protected function getContentUpdater()
    {
        if (!isset($this->contentUpdater)) {
            $this->contentUpdater = new ContentUpdater(
                $this->getContentGatewayMock(),
                $this->getConverterRegistryMock(),
                $this->getContentStorageHandlerMock(),
                $this->getContentMapperMock()
            );
        }

        return $this->contentUpdater;
    }
}

class_alias(ContentUpdaterTest::class, 'eZ\Publish\Core\Persistence\Legacy\Tests\Content\Type\ContentUpdaterTest');
