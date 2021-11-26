<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Core\Persistence\Legacy\Setting;

use Ibexa\Contracts\Core\Persistence\Setting\Setting;
use Ibexa\Core\Persistence\Legacy\Setting\Gateway;
use Ibexa\Core\Persistence\Legacy\Setting\Handler;
use Ibexa\Tests\Core\Persistence\Legacy\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * @covers \Ibexa\Core\Persistence\Legacy\Setting\Handler::create
 */
class SettingHandlerTest extends TestCase
{
    /** @var \Ibexa\Core\Persistence\Legacy\Setting\Handler */
    protected $settingHandler;

    /** @var \Ibexa\Core\Persistence\Legacy\Setting\Gateway */
    protected $gatewayMock;

    public function testCreate()
    {
        $handler = $this->getSettingHandler();
        $gatewayMock = $this->getGatewayMock();

        $gatewayMock->expects($this->once())
            ->method('insertSetting')
            ->with(
                $this->equalTo('group_a1'),
                $this->equalTo('identifier_b2'),
                $this->equalTo('value_c3')
            )->will($this->returnValue(123));

        $gatewayMock->expects($this->once())
            ->method('loadSettingById')
            ->with(
                $this->equalTo(123)
            )
            ->will($this->returnValue([
                'group' => 'group_a1',
                'identifier' => 'identifier_b2',
                'value' => 'value_c3',
            ]));

        $settingRef = new Setting([
            'group' => 'group_a1',
            'identifier' => 'identifier_b2',
            'serializedValue' => 'value_c3',
        ]);

        $result = $handler->create(
            'group_a1',
            'identifier_b2',
            'value_c3',
        );

        $this->assertEquals(
            $settingRef,
            $result
        );
    }

    /**
     * @throws \Ibexa\Core\Base\Exceptions\NotFoundException
     */
    public function testUpdate()
    {
        $handler = $this->getSettingHandler();
        $gatewayMock = $this->getGatewayMock();

        $gatewayMock->expects($this->once())
            ->method('updateSetting')
            ->with(
                $this->equalTo('group_d1'),
                $this->equalTo('identifier_e2'),
                $this->equalTo('value_f3')
            );

        $gatewayMock->expects($this->once())
            ->method('loadSetting')
            ->with(
                $this->equalTo('group_d1'),
                $this->equalTo('identifier_e2')
            )
            ->will($this->returnValue([
                'group' => 'group_d1',
                'identifier' => 'identifier_e2',
                'value' => 'value_f3',
            ]));

        $settingRef = new Setting([
            'group' => 'group_d1',
            'identifier' => 'identifier_e2',
            'serializedValue' => 'value_f3',
        ]);

        $result = $handler->update(
            'group_d1',
            'identifier_e2',
            'value_f3'
        );

        $this->assertEquals(
            $settingRef,
            $result
        );
    }

    /**
     * @throws \Ibexa\Core\Base\Exceptions\NotFoundException
     */
    public function testLoad()
    {
        $handler = $this->getSettingHandler();

        $gatewayMock = $this->getGatewayMock();

        $gatewayMock->expects($this->once())
            ->method('loadSetting')
            ->with(
                $this->equalTo('group_a1'),
                $this->equalTo('identifier_b2')
            )->will(
                $this->returnValue([
                    'group' => 'group_a1',
                    'identifier' => 'identifier_b2',
                    'value' => 'value_c3',
                ])
            );

        $settingRef = new Setting([
            'group' => 'group_a1',
            'identifier' => 'identifier_b2',
            'serializedValue' => 'value_c3',
        ]);

        $result = $handler->load(
            'group_a1',
            'identifier_b2'
        );

        $this->assertEquals(
            $settingRef,
            $result
        );
    }

    public function testDelete()
    {
        $handler = $this->getSettingHandler();
        $gatewayMock = $this->getGatewayMock();

        $gatewayMock->expects($this->once())
            ->method('deleteSetting')
            ->with(
                $this->equalTo('group_a1'),
                $this->equalTo('identifier_b2')
            );

        $handler->delete(
            'group_a1',
            'identifier_b2'
        );
    }

    protected function getSettingHandler(): Handler
    {
        if (!isset($this->settingHandler)) {
            $this->settingHandler = new Handler(
                $this->getGatewayMock()
            );
        }

        return $this->settingHandler;
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\Ibexa\Core\Persistence\Legacy\Setting\Gateway
     */
    protected function getGatewayMock(): MockObject
    {
        if (!isset($this->gatewayMock)) {
            $this->gatewayMock = $this->getMockForAbstractClass(Gateway::class);
        }

        return $this->gatewayMock;
    }
}

class_alias(SettingHandlerTest::class, 'eZ\Publish\Core\Persistence\Legacy\Tests\Setting\SettingHandlerTest');
