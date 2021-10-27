<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Core\Repository\Service\Mock;

use Ibexa\Tests\Core\Repository\Service\Mock\Base as BaseServiceMockTest;

/**
 * Mock test case for Repository.
 */
class RepositoryTest extends BaseServiceMockTest
{
    /**
     * Test for the beginTransaction() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\Repository::beginTransaction
     */
    public function testBeginTransaction()
    {
        $mockedRepository = $this->getRepository();
        $persistenceHandlerMock = $this->getPersistenceMock();

        $persistenceHandlerMock->expects(
            $this->once()
        )->method(
            'beginTransaction'
        );

        $mockedRepository->beginTransaction();
    }

    /**
     * Test for the commit() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\Repository::commit
     */
    public function testCommit()
    {
        $mockedRepository = $this->getRepository();
        $persistenceHandlerMock = $this->getPersistenceMock();

        $persistenceHandlerMock->expects(
            $this->once()
        )->method(
            'commit'
        );

        $mockedRepository->commit();
    }

    /**
     * Test for the commit() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\Repository::commit
     */
    public function testCommitThrowsRuntimeException()
    {
        $this->expectException(\RuntimeException::class);

        $mockedRepository = $this->getRepository();
        $persistenceHandlerMock = $this->getPersistenceMock();

        $persistenceHandlerMock->expects(
            $this->once()
        )->method(
            'commit'
        )->will(
            $this->throwException(new \Exception())
        );

        $mockedRepository->commit();
    }

    /**
     * Test for the rollback() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\Repository::rollback
     */
    public function testRollback()
    {
        $mockedRepository = $this->getRepository();
        $persistenceHandlerMock = $this->getPersistenceMock();

        $persistenceHandlerMock->expects(
            $this->once()
        )->method(
            'rollback'
        );

        $mockedRepository->rollback();
    }

    /**
     * Test for the rollback() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\Repository::rollback
     */
    public function testRollbackThrowsRuntimeException()
    {
        $this->expectException(\RuntimeException::class);

        $mockedRepository = $this->getRepository();
        $persistenceHandlerMock = $this->getPersistenceMock();

        $persistenceHandlerMock->expects(
            $this->once()
        )->method(
            'rollback'
        )->will(
            $this->throwException(new \Exception())
        );

        $mockedRepository->rollback();
    }
}

class_alias(RepositoryTest::class, 'eZ\Publish\Core\Repository\Tests\Service\Mock\RepositoryTest');
