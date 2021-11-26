<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Core\Limitation;

use Ibexa\Contracts\Core\Persistence\Handler as SPIHandler;
use Ibexa\Contracts\Core\Repository\Values\User\User as APIUser;
use PHPUnit\Framework\TestCase;

abstract class Base extends TestCase
{
    /** @var \Ibexa\Contracts\Core\Persistence\Handler|\PHPUnit\Framework\MockObject\MockObject */
    private $persistenceHandlerMock;

    /** @var \Ibexa\Contracts\Core\Repository\Values\User\User|\PHPUnit\Framework\MockObject\MockObject */
    private $userMock;

    /**
     * @param array $mockMethods For specifying the methods to mock, all by default
     *
     * @return \Ibexa\Contracts\Core\Persistence\Handler|\PHPUnit\Framework\MockObject\MockObject
     */
    public function getPersistenceMock(array $mockMethods = [])
    {
        if ($this->persistenceHandlerMock !== null) {
            return $this->persistenceHandlerMock;
        }

        return $this->persistenceHandlerMock = $this->createMock(SPIHandler::class);
    }

    /**
     * @param array $mockMethods For specifying the methods to mock, all by default
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\User\User|\PHPUnit\Framework\MockObject\MockObject
     */
    public function getUserMock(array $mockMethods = [])
    {
        if ($this->userMock !== null) {
            return $this->userMock;
        }

        return $this->userMock = $this->getMockBuilder(APIUser::class)
            ->setConstructorArgs([])
            ->setMethods($mockMethods)
            ->getMock();
    }

    /**
     * unset properties.
     */
    protected function tearDown(): void
    {
        if ($this->persistenceHandlerMock !== null) {
            unset($this->persistenceHandlerMock);
        }

        if ($this->userMock !== null) {
            unset($this->userMock);
        }

        parent::tearDown();
    }
}

class_alias(Base::class, 'eZ\Publish\Core\Limitation\Tests\Base');
