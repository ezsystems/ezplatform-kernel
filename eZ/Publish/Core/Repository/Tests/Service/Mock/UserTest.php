<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace eZ\Publish\Core\Repository\Tests\Service\Mock;

use eZ\Publish\API\Repository\ContentService as APIContentService;
use eZ\Publish\API\Repository\PasswordHashService;
use eZ\Publish\API\Repository\Values\Content\ContentInfo as APIContentInfo;
use eZ\Publish\API\Repository\Values\Content\VersionInfo as APIVersionInfo;
use eZ\Publish\API\Repository\Values\User\User as APIUser;
use eZ\Publish\Core\Repository\Tests\Service\Mock\Base as BaseServiceMockTest;
use eZ\Publish\Core\Repository\User\PasswordValidatorInterface;
use eZ\Publish\Core\Repository\UserService;
use eZ\Publish\SPI\Persistence\User\RoleAssignment;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * @covers \eZ\Publish\Core\Repository\UserService
 */
class UserTest extends BaseServiceMockTest
{
    /**
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    public function testDeleteUser(): void
    {
        $repository = $this->getRepositoryMock();
        $userService = $this->getPartlyMockedUserService(['loadUser']);
        $contentService = $this->createMock(APIContentService::class);
        /* @var \PHPUnit\Framework\MockObject\MockObject $userHandler */
        $userHandler = $this->getPersistenceMock()->userHandler();

        $user = $this->createMock(APIUser::class);
        $loadedUser = $this->createMock(APIUser::class);
        $versionInfo = $this->createMock(APIVersionInfo::class);
        $contentInfo = $this->createMock(APIContentInfo::class);

        $userId = 42;
        $user->method('__get')->with('id')->willReturn($userId);
        $versionInfo->method('getContentInfo')->willReturn($contentInfo);
        $loadedUser->method('getVersionInfo')->willReturn($versionInfo);
        $loadedUser->method('__get')->with('id')->willReturn($userId);
        $userService->method('loadUser')->with($userId)->willReturn($loadedUser);

        $repository->expects(self::once())->method('beginTransaction');

        $this->mockRoleAssignmentRemoval($userHandler, $userId);

        $contentService->expects(self::once())->method('deleteContent')->with($contentInfo);
        $repository->expects(self::once())->method('getContentService')->willReturn($contentService);
        $userHandler->expects(self::once())->method('delete')->with($userId);

        $repository->expects(self::once())->method('commit');

        $userService->deleteUser($user);
    }

    /**
     * @covers \eZ\Publish\Core\Repository\UserService::deleteUser
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    public function testDeleteUserWithRollback(): void
    {
        $repository = $this->getRepositoryMock();
        $userService = $this->getPartlyMockedUserService(['loadUser']);
        $contentService = $this->createMock(APIContentService::class);
        /* @var \PHPUnit\Framework\MockObject\MockObject $userHandler */
        $userHandler = $this->getPersistenceMock()->userHandler();

        $user = $this->createMock(APIUser::class);
        $loadedUser = $this->createMock(APIUser::class);
        $versionInfo = $this->createMock(APIVersionInfo::class);
        $contentInfo = $this->createMock(APIContentInfo::class);

        $userId = 42;
        $user->method('__get')->with('id')->willReturn($userId);
        $versionInfo->method('getContentInfo')->willReturn($contentInfo);
        $loadedUser->method('getVersionInfo')->willReturn($versionInfo);
        $userService->method('loadUser')->with($userId)->willReturn($loadedUser);

        $repository->expects(self::once())->method('beginTransaction');

        $this->mockRoleAssignmentRemoval($userHandler, $userId);

        $exception = new \Exception();
        $contentService->expects(self::once())
            ->method('deleteContent')
            ->with($contentInfo)
            ->will($this->throwException($exception));

        $repository->expects(self::once())->method('getContentService')->willReturn($contentService);

        $repository->expects(self::once())->method('rollback');

        $this->expectExceptionObject($exception);
        $userService->deleteUser($user);
    }

    private function mockRoleAssignmentRemoval(MockObject $userHandler, int $userId): void
    {
        $userHandler
            ->expects(self::once())
            ->method('loadRoleAssignmentsByGroupId')
            ->with($userId)
            ->willReturn([new RoleAssignment(['id' => 1])]);

        $userHandler->method('removeRoleAssignment')->with(1);
    }

    /**
     * Returns the User service to test with $methods mocked.
     *
     * Injected Repository comes from {@see getRepositoryMock()} and persistence handler from {@see getPersistenceMock()}
     *
     * @param string[] $methods
     *
     * @return \eZ\Publish\Core\Repository\UserService|\PHPUnit\Framework\MockObject\MockObject
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
