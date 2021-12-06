<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Core\Persistence\Legacy\Content\Language;

use Ibexa\Contracts\Core\Persistence\Content\Language;
use Ibexa\Contracts\Core\Persistence\Content\Language\CreateStruct as SPILanguageCreateStruct;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Core\Persistence\Legacy\Content\Language\Gateway as LanguageGateway;
use Ibexa\Core\Persistence\Legacy\Content\Language\Handler;
use Ibexa\Core\Persistence\Legacy\Content\Language\Mapper as LanguageMapper;
use Ibexa\Tests\Core\Persistence\Legacy\TestCase;

/**
 * @covers \Ibexa\Core\Persistence\Legacy\Content\Language\Handler
 */
class LanguageHandlerTest extends TestCase
{
    /**
     * Language handler.
     *
     * @var \Ibexa\Core\Persistence\Legacy\Content\Language\Handler
     */
    protected $languageHandler;

    /**
     * Language gateway mock.
     *
     * @var \Ibexa\Core\Persistence\Legacy\Content\Language\Gateway
     */
    protected $gatewayMock;

    /**
     * Language mapper mock.
     *
     * @var \Ibexa\Core\Persistence\Legacy\Content\Language\Mapper
     */
    protected $mapperMock;

    public function testCreate()
    {
        $handler = $this->getLanguageHandler();

        $mapperMock = $this->getMapperMock();
        $mapperMock->expects($this->once())
            ->method('createLanguageFromCreateStruct')
            ->with(
                $this->isInstanceOf(
                    SPILanguageCreateStruct::class
                )
            )->will($this->returnValue(new Language()));

        $gatewayMock = $this->getGatewayMock();
        $gatewayMock->expects($this->once())
            ->method('insertLanguage')
            ->with(
                $this->isInstanceOf(
                    Language::class
                )
            )->will($this->returnValue(2));

        $createStruct = $this->getCreateStructFixture();

        $result = $handler->create($createStruct);

        $this->assertInstanceOf(
            Language::class,
            $result
        );
        $this->assertEquals(
            2,
            $result->id
        );
    }

    /**
     * Returns a Language CreateStruct.
     *
     * @return \Ibexa\Contracts\Core\Persistence\Content\Language\CreateStruct
     */
    protected function getCreateStructFixture()
    {
        return new Language\CreateStruct();
    }

    public function testUpdate()
    {
        $handler = $this->getLanguageHandler();

        $gatewayMock = $this->getGatewayMock();
        $gatewayMock->expects($this->once())
            ->method('updateLanguage')
            ->with($this->isInstanceOf(Language::class));

        $handler->update($this->getLanguageFixture());
    }

    /**
     * Returns a Language.
     *
     * @return \Ibexa\Contracts\Core\Persistence\Content\Language
     */
    protected function getLanguageFixture()
    {
        return new Language();
    }

    public function testLoad()
    {
        $handler = $this->getLanguageHandler();
        $mapperMock = $this->getMapperMock();
        $gatewayMock = $this->getGatewayMock();

        $gatewayMock->expects($this->once())
            ->method('loadLanguageListData')
            ->with($this->equalTo([2]))
            ->will($this->returnValue([]));

        $mapperMock->expects($this->once())
            ->method('extractLanguagesFromRows')
            ->with($this->equalTo([]))
            ->will($this->returnValue([new Language()]));

        $result = $handler->load(2);

        $this->assertInstanceOf(
            Language::class,
            $result
        );
    }

    public function testLoadFailure()
    {
        $this->expectException(NotFoundException::class);

        $handler = $this->getLanguageHandler();
        $mapperMock = $this->getMapperMock();
        $gatewayMock = $this->getGatewayMock();

        $gatewayMock->expects($this->once())
            ->method('loadLanguageListData')
            ->with($this->equalTo([2]))
            ->will($this->returnValue([]));

        $mapperMock->expects($this->once())
            ->method('extractLanguagesFromRows')
            ->with($this->equalTo([]))
            // No language extracted
            ->will($this->returnValue([]));

        $result = $handler->load(2);
    }

    public function testLoadByLanguageCode()
    {
        $handler = $this->getLanguageHandler();
        $mapperMock = $this->getMapperMock();
        $gatewayMock = $this->getGatewayMock();

        $gatewayMock->expects($this->once())
            ->method('loadLanguageListDataByLanguageCode')
            ->with($this->equalTo(['eng-US']))
            ->will($this->returnValue([]));

        $mapperMock->expects($this->once())
            ->method('extractLanguagesFromRows')
            ->with($this->equalTo([]))
            ->will($this->returnValue([new Language()]));

        $result = $handler->loadByLanguageCode('eng-US');

        $this->assertInstanceOf(
            Language::class,
            $result
        );
    }

    public function testLoadByLanguageCodeFailure()
    {
        $this->expectException(NotFoundException::class);

        $handler = $this->getLanguageHandler();
        $mapperMock = $this->getMapperMock();
        $gatewayMock = $this->getGatewayMock();

        $gatewayMock->expects($this->once())
            ->method('loadLanguageListDataByLanguageCode')
            ->with($this->equalTo(['eng-US']))
            ->will($this->returnValue([]));

        $mapperMock->expects($this->once())
            ->method('extractLanguagesFromRows')
            ->with($this->equalTo([]))
            // No language extracted
            ->will($this->returnValue([]));

        $result = $handler->loadByLanguageCode('eng-US');
    }

    public function testLoadAll()
    {
        $handler = $this->getLanguageHandler();
        $mapperMock = $this->getMapperMock();
        $gatewayMock = $this->getGatewayMock();

        $gatewayMock->expects($this->once())
            ->method('loadAllLanguagesData')
            ->will($this->returnValue([]));

        $mapperMock->expects($this->once())
            ->method('extractLanguagesFromRows')
            ->with($this->equalTo([]))
            ->will($this->returnValue([new Language()]));

        $result = $handler->loadAll();

        $this->assertIsArray(
            $result
        );
    }

    public function testDeleteSuccess()
    {
        $handler = $this->getLanguageHandler();
        $gatewayMock = $this->getGatewayMock();

        $gatewayMock->expects($this->once())
            ->method('canDeleteLanguage')
            ->with($this->equalTo(2))
            ->will($this->returnValue(true));
        $gatewayMock->expects($this->once())
            ->method('deleteLanguage')
            ->with($this->equalTo(2));

        $result = $handler->delete(2);
    }

    public function testDeleteFail()
    {
        $this->expectException(\LogicException::class);

        $handler = $this->getLanguageHandler();
        $gatewayMock = $this->getGatewayMock();

        $gatewayMock->expects($this->once())
            ->method('canDeleteLanguage')
            ->with($this->equalTo(2))
            ->will($this->returnValue(false));
        $gatewayMock->expects($this->never())
            ->method('deleteLanguage');

        $result = $handler->delete(2);
    }

    /**
     * Returns the language handler to test.
     *
     * @return \Ibexa\Core\Persistence\Legacy\Content\Language\Handler
     */
    protected function getLanguageHandler()
    {
        if (!isset($this->languageHandler)) {
            $this->languageHandler = new Handler(
                $this->getGatewayMock(),
                $this->getMapperMock()
            );
        }

        return $this->languageHandler;
    }

    /**
     * Returns a language mapper mock.
     *
     * @return \Ibexa\Core\Persistence\Legacy\Content\Language\Mapper
     */
    protected function getMapperMock()
    {
        if (!isset($this->mapperMock)) {
            $this->mapperMock = $this->createMock(LanguageMapper::class);
        }

        return $this->mapperMock;
    }

    /**
     * Returns a mock for the language gateway.
     *
     * @return \Ibexa\Core\Persistence\Legacy\Content\Language\Gateway
     */
    protected function getGatewayMock()
    {
        if (!isset($this->gatewayMock)) {
            $this->gatewayMock = $this->getMockForAbstractClass(LanguageGateway::class);
        }

        return $this->gatewayMock;
    }
}

class_alias(LanguageHandlerTest::class, 'eZ\Publish\Core\Persistence\Legacy\Tests\Content\Language\LanguageHandlerTest');
