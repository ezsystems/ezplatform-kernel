<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace eZ\Publish\Core\Repository\Tests\Service\Mock;

use Exception;
use eZ\Publish\API\Repository\ContentService as APIContentService;
use eZ\Publish\API\Repository\PasswordHashService;
use eZ\Publish\API\Repository\Repository;
use eZ\Publish\API\Repository\UserService as APIUserService;
use eZ\Publish\API\Repository\Values\Content\ContentInfo as APIContentInfo;
use eZ\Publish\API\Repository\Values\Content\VersionInfo as APIVersionInfo;
use eZ\Publish\API\Repository\Values\User\User;
use eZ\Publish\API\Repository\Values\User\User as APIUser;
use eZ\Publish\Core\Repository\Tests\Service\Mock\Base as BaseServiceMockTest;
use eZ\Publish\Core\Repository\User\PasswordValidatorInterface;
use eZ\Publish\Core\Repository\UserService;
use eZ\Publish\SPI\Persistence\User\Handler as PersistenceUserHandler;
use eZ\Publish\SPI\Persistence\User\RoleAssignment;

/**
 * @covers \eZ\Publish\Core\Repository\UserService
 */
class UserTest extends BaseServiceMockTest
{
    private const MOCKED_USER_ID = 42;

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
        $contentInfo = $this->createMock(APIContentInfo::class);
        $this->mockDeleteUserFlow($repository, $userService, $contentService, $user, $contentInfo, $userHandler);

        $contentService->expects(self::once())->method('deleteContent')->with($contentInfo);
        $userHandler->expects(self::once())->method('delete')->with(self::MOCKED_USER_ID);
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
        /* @var \eZ\Publish\SPI\Persistence\User\Handler&\PHPUnit\Framework\MockObject\MockObject $userHandler */
        $userHandler = $this->getPersistenceMock()->userHandler();

        $user = $this->createMock(APIUser::class);
        $contentInfo = $this->createMock(APIContentInfo::class);
        $this->mockDeleteUserFlow($repository, $userService, $contentService, $user, $contentInfo, $userHandler);

        $exception = new Exception();
        $contentService->expects(self::once())
            ->method('deleteContent')
            ->with($contentInfo)
            ->willThrowException($exception);

        $repository->expects(self::once())->method('rollback');

        $this->expectExceptionObject($exception);
        $userService->deleteUser($user);
    }

    /**
     * Returns the User service to test with $methods mocked.
     *
     * Injected Repository comes from {@see getRepositoryMock()} and persistence handler from {@see getPersistenceMock()}
     *
     * @param string[] $methods
     *
     * @return \eZ\Publish\API\Repository\UserService&\PHPUnit\Framework\MockObject\MockObject
     */
    protected function getPartlyMockedUserService(array $methods = null): APIUserService
    {
        return $this->getMockBuilder(UserService::class)
            ->onlyMethods($methods)
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

    /**
     * @param \eZ\Publish\API\Repository\Repository&\PHPUnit\Framework\MockObject\MockObject $repository
     * @param \eZ\Publish\API\Repository\UserService&\PHPUnit\Framework\MockObject\MockObject $userService
     * @param \eZ\Publish\API\Repository\ContentService&\PHPUnit\Framework\MockObject\MockObject $contentService
     * @param \eZ\Publish\API\Repository\Values\User\User&\PHPUnit\Framework\MockObject\MockObject $user
     * @param \eZ\Publish\API\Repository\Values\Content\ContentInfo&\PHPUnit\Framework\MockObject\MockObject $contentInfo
     * @param \eZ\Publish\SPI\Persistence\User\Handler&\PHPUnit\Framework\MockObject\MockObject $userHandler
     */
    private function mockDeleteUserFlow(
        Repository $repository,
        APIUserService $userService,
        APIContentService $contentService,
        User $user,
        APIContentInfo $contentInfo,
        PersistenceUserHandler $userHandler
    ): void {
        $loadedUser = $this->createMock(APIUser::class);
        $versionInfo = $this->createMock(APIVersionInfo::class);

        $user->method('__get')->with('id')->willReturn(self::MOCKED_USER_ID);
        $versionInfo->method('getContentInfo')->willReturn($contentInfo);
        $loadedUser->method('getVersionInfo')->willReturn($versionInfo);
        $loadedUser->method('__get')->with('id')->willReturn(self::MOCKED_USER_ID);

        $userService->method('loadUser')->with(self::MOCKED_USER_ID)->willReturn($loadedUser);

        $userHandler
            ->expects(self::once())
            ->method('loadRoleAssignmentsByGroupId')
            ->with(self::MOCKED_USER_ID)
            ->willReturn([new RoleAssignment(['id' => 1])]);

        $userHandler->method('removeRoleAssignment')->with(1);

        $repository->expects(self::once())->method('beginTransaction');
        $repository->expects(self::once())->method('getContentService')->willReturn($contentService);
    }
}
