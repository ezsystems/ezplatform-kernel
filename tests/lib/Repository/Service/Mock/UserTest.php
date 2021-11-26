<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Core\Repository\Service\Mock;

use Ibexa\Contracts\Core\Repository\ContentService as APIContentService;
use Ibexa\Contracts\Core\Repository\PasswordHashService;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo as APIContentInfo;
use Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo as APIVersionInfo;
use Ibexa\Contracts\Core\Repository\Values\User\User as APIUser;
use Ibexa\Core\Repository\User\PasswordValidatorInterface;
use Ibexa\Core\Repository\UserService;
use Ibexa\Tests\Core\Repository\Service\Mock\Base as BaseServiceMockTest;

/**
 * Mock test case for User Service.
 */
class UserTest extends BaseServiceMockTest
{
    /**
     * Test for the deleteUser() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\UserService::deleteUser
     */
    public function testDeleteUser()
    {
        $repository = $this->getRepositoryMock();
        $userService = $this->getPartlyMockedUserService(['loadUser']);
        $contentService = $this->createMock(APIContentService::class);
        $userHandler = $this->getPersistenceMock()->userHandler();

        $user = $this->createMock(APIUser::class);
        $loadedUser = $this->createMock(APIUser::class);
        $versionInfo = $this->createMock(APIVersionInfo::class);
        $contentInfo = $this->createMock(APIContentInfo::class);

        $user->expects($this->once())
            ->method('__get')
            ->with('id')
            ->will($this->returnValue(42));

        $versionInfo->expects($this->once())
            ->method('getContentInfo')
            ->will($this->returnValue($contentInfo));

        $loadedUser->expects($this->once())
            ->method('getVersionInfo')
            ->will($this->returnValue($versionInfo));

        $loadedUser->expects($this->once())
            ->method('__get')
            ->with('id')
            ->will($this->returnValue(42));

        $userService->expects($this->once())
            ->method('loadUser')
            ->with(42)
            ->will($this->returnValue($loadedUser));

        $repository->expects($this->once())->method('beginTransaction');

        $contentService->expects($this->once())
            ->method('deleteContent')
            ->with($contentInfo);

        $repository->expects($this->once())
            ->method('getContentService')
            ->will($this->returnValue($contentService));

        /* @var \PHPUnit\Framework\MockObject\MockObject $userHandler */
        $userHandler->expects($this->once())
            ->method('delete')
            ->with(42);

        $repository->expects($this->once())->method('commit');

        /* @var \Ibexa\Contracts\Core\Repository\Values\User\User $user */
        $userService->deleteUser($user);
    }

    /**
     * Test for the deleteUser() method.
     *
     * @covers \Ibexa\Contracts\Core\Repository\UserService::deleteUser
     */
    public function testDeleteUserWithRollback()
    {
        $this->expectException(\Exception::class);

        $repository = $this->getRepositoryMock();
        $userService = $this->getPartlyMockedUserService(['loadUser']);
        $contentService = $this->createMock(APIContentService::class);

        $user = $this->createMock(APIUser::class);
        $loadedUser = $this->createMock(APIUser::class);
        $versionInfo = $this->createMock(APIVersionInfo::class);
        $contentInfo = $this->createMock(APIContentInfo::class);

        $user->expects($this->once())
            ->method('__get')
            ->with('id')
            ->will($this->returnValue(42));

        $versionInfo->expects($this->once())
            ->method('getContentInfo')
            ->will($this->returnValue($contentInfo));

        $loadedUser->expects($this->once())
            ->method('getVersionInfo')
            ->will($this->returnValue($versionInfo));

        $userService->expects($this->once())
            ->method('loadUser')
            ->with(42)
            ->will($this->returnValue($loadedUser));

        $repository->expects($this->once())->method('beginTransaction');

        $contentService->expects($this->once())
            ->method('deleteContent')
            ->with($contentInfo)
            ->will($this->throwException(new \Exception()));

        $repository->expects($this->once())
            ->method('getContentService')
            ->will($this->returnValue($contentService));

        $repository->expects($this->once())->method('rollback');

        /* @var \Ibexa\Contracts\Core\Repository\Values\User\User $user */
        $userService->deleteUser($user);
    }

    /**
     * Returns the User service to test with $methods mocked.
     *
     * Injected Repository comes from {@see getRepositoryMock()} and persistence handler from {@see getPersistenceMock()}
     *
     * @param string[] $methods
     *
     * @return \Ibexa\Core\Repository\UserService|\PHPUnit\Framework\MockObject\MockObject
     */
    protected function getPartlyMockedUserService(array $methods = null)
    {
        return $this->getMockBuilder(UserService::class)
            ->setMethods($methods)
            ->setConstructorArgs(
                [
                    $this->getRepositoryMock(),
                    $this->getPermissionResolverMock(),
                    $this->getPersistenceMock()->userHandler(),
                    $this->getPersistenceMock()->locationHandler(),
                    $this->createMock(PasswordHashService::class),
                    $this->createMock(PasswordValidatorInterface::class),
                ]
            )
            ->getMock();
    }
}

class_alias(UserTest::class, 'eZ\Publish\Core\Repository\Tests\Service\Mock\UserTest');
