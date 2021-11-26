<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Core\Repository\Service\Mock;

use ArrayIterator;
use Ibexa\Contracts\Core\Limitation\Type as SPIType;
use Ibexa\Contracts\Core\Persistence\User as SPIUser;
use Ibexa\Contracts\Core\Persistence\User\Role as SPIRole;
use Ibexa\Contracts\Core\Repository\Exceptions\BadStateException;
use Ibexa\Contracts\Core\Repository\Exceptions\LimitationValidationException;
use Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException;
use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\Contracts\Core\Repository\Values\User\Limitation\RoleLimitation;
use Ibexa\Contracts\Core\Repository\Values\User\PolicyCreateStruct;
use Ibexa\Contracts\Core\Repository\Values\User\PolicyDraft;
use Ibexa\Contracts\Core\Repository\Values\User\PolicyUpdateStruct;
use Ibexa\Contracts\Core\Repository\Values\User\Role;
use Ibexa\Contracts\Core\Repository\Values\User\RoleCreateStruct;
use Ibexa\Contracts\Core\Repository\Values\User\RoleDraft;
use Ibexa\Contracts\Core\Repository\Values\User\User;
use Ibexa\Contracts\Core\Repository\Values\User\UserGroup;
use Ibexa\Core\Base\Exceptions\NotFoundException;
use Ibexa\Core\Repository\Mapper\RoleDomainMapper;
use Ibexa\Core\Repository\Permission\LimitationService;
use Ibexa\Core\Repository\RoleService;
use Ibexa\Tests\Core\Repository\Service\Mock\Base as BaseServiceMockTest;

/**
 * @covers \Ibexa\Contracts\Core\Repository\RoleService
 * @covers \Ibexa\Core\Repository\Permission\LimitationService::validateLimitations
 * @covers \Ibexa\Core\Repository\Permission\LimitationService::validateLimitation
 */
class RoleTest extends BaseServiceMockTest
{
    /**
     * Test for the createRole() method.
     */
    public function testCreateRoleThrowsLimitationValidationException()
    {
        $this->expectException(LimitationValidationException::class);

        $limitationMock = $this->createMock(RoleLimitation::class);
        $limitationTypeMock = $this->createMock(SPIType::class);

        $limitationMock->expects($this->any())
            ->method('getIdentifier')
            ->will($this->returnValue('mockIdentifier'));

        $limitationTypeMock->expects($this->once())
            ->method('acceptValue')
            ->with($this->equalTo($limitationMock));
        $limitationTypeMock->expects($this->once())
            ->method('validate')
            ->with($this->equalTo($limitationMock))
            ->will($this->returnValue([42]));

        $settings = [
            'policyMap' => ['mockModule' => ['mockFunction' => ['mockIdentifier' => true]]],
            'limitationTypes' => ['mockIdentifier' => $limitationTypeMock],
        ];

        $roleServiceMock = $this->getPartlyMockedRoleService(['loadRoleByIdentifier'], $settings);

        /** @var \Ibexa\Contracts\Core\Repository\Values\User\RoleCreateStruct $roleCreateStructMock */
        $roleCreateStructMock = $this->createMock(RoleCreateStruct::class);
        $policyCreateStructMock = $this->createMock(PolicyCreateStruct::class);

        /* @var \Ibexa\Contracts\Core\Repository\Values\User\PolicyCreateStruct $policyCreateStructMock */
        $policyCreateStructMock->module = 'mockModule';
        $policyCreateStructMock->function = 'mockFunction';
        $roleCreateStructMock->identifier = 'mockIdentifier';
        $roleServiceMock->expects($this->once())
            ->method('loadRoleByIdentifier')
            ->with($this->equalTo('mockIdentifier'))
            ->will($this->throwException(new NotFoundException('Role', 'mockIdentifier')));

        /* @var \PHPUnit\Framework\MockObject\MockObject $roleCreateStructMock */
        $roleCreateStructMock->expects($this->once())
            ->method('getPolicies')
            ->will($this->returnValue([$policyCreateStructMock]));

        /* @var \PHPUnit\Framework\MockObject\MockObject $policyCreateStructMock */
        $policyCreateStructMock->expects($this->once())
            ->method('getLimitations')
            ->will($this->returnValue([$limitationMock]));

        $permissionResolverMock = $this->getPermissionResolverMock();
        $permissionResolverMock->expects($this->once())
            ->method('canUser')
            ->with(
                $this->equalTo('role'),
                $this->equalTo('create'),
                $this->equalTo($roleCreateStructMock)
            )->will($this->returnValue(true));

        /* @var \Ibexa\Contracts\Core\Repository\Values\User\RoleCreateStruct $roleCreateStructMock */
        $roleServiceMock->createRole($roleCreateStructMock);
    }

    /**
     * Test for the addPolicy() method.
     */
    public function testAddPolicyThrowsLimitationValidationException()
    {
        $this->expectException(LimitationValidationException::class);

        $limitationMock = $this->createMock(RoleLimitation::class);
        $limitationTypeMock = $this->createMock(SPIType::class);

        $limitationTypeMock->expects($this->once())
            ->method('acceptValue')
            ->with($this->equalTo($limitationMock));
        $limitationTypeMock->expects($this->once())
            ->method('validate')
            ->with($this->equalTo($limitationMock))
            ->will($this->returnValue([42]));

        $limitationMock->expects($this->any())
            ->method('getIdentifier')
            ->will($this->returnValue('mockIdentifier'));

        $settings = [
            'policyMap' => ['mockModule' => ['mockFunction' => ['mockIdentifier' => true]]],
            'limitationTypes' => ['mockIdentifier' => $limitationTypeMock],
        ];

        $roleServiceMock = $this->getPartlyMockedRoleService(['loadRoleDraft'], $settings);

        $roleDraftMock = $this->createMock(RoleDraft::class);
        $policyCreateStructMock = $this->createMock(PolicyCreateStruct::class);

        $roleDraftMock->expects($this->any())
            ->method('__get')
            ->with('id')
            ->will($this->returnValue(42));
        /* @var \Ibexa\Contracts\Core\Repository\Values\User\PolicyCreateStruct $policyCreateStructMock */
        $policyCreateStructMock->module = 'mockModule';
        $policyCreateStructMock->function = 'mockFunction';

        $roleServiceMock->expects($this->once())
            ->method('loadRoleDraft')
            ->with($this->equalTo(42))
            ->will($this->returnValue($roleDraftMock));

        /* @var \PHPUnit\Framework\MockObject\MockObject $policyCreateStructMock */
        $policyCreateStructMock->expects($this->once())
            ->method('getLimitations')
            ->will($this->returnValue([$limitationMock]));

        $permissionResolverMock = $this->getPermissionResolverMock();
        $permissionResolverMock->expects($this->once())
            ->method('canUser')
            ->with(
                $this->equalTo('role'),
                $this->equalTo('update'),
                $this->equalTo($roleDraftMock)
            )->will($this->returnValue(true));

        /* @var \Ibexa\Contracts\Core\Repository\Values\User\Role $roleDraftMock */
        /* @var \Ibexa\Contracts\Core\Repository\Values\User\PolicyCreateStruct $policyCreateStructMock */
        $roleServiceMock->addPolicyByRoleDraft($roleDraftMock, $policyCreateStructMock);
    }

    /**
     * Test for the updatePolicyByRoleDraft() method.
     */
    public function testUpdatePolicyThrowsLimitationValidationException()
    {
        $this->expectException(LimitationValidationException::class);

        $limitationMock = $this->createMock(RoleLimitation::class);
        $limitationTypeMock = $this->createMock(SPIType::class);

        $limitationTypeMock->expects($this->once())
            ->method('acceptValue')
            ->with($this->equalTo($limitationMock));
        $limitationTypeMock->expects($this->once())
            ->method('validate')
            ->with($this->equalTo($limitationMock))
            ->will($this->returnValue([42]));

        $limitationMock->expects($this->any())
            ->method('getIdentifier')
            ->will($this->returnValue('mockIdentifier'));

        $settings = [
            'policyMap' => ['mockModule' => ['mockFunction' => ['mockIdentifier' => true]]],
            'limitationTypes' => ['mockIdentifier' => $limitationTypeMock],
        ];

        $roleServiceMock = $this->getPartlyMockedRoleService(['loadRole'], $settings);

        $roleDraftMock = $this->createMock(RoleDraft::class);
        $policyDraftMock = $this->createMock(PolicyDraft::class);
        $policyUpdateStructMock = $this->createMock(PolicyUpdateStruct::class);

        $policyDraftMock->expects($this->any())
            ->method('__get')
            ->will(
                $this->returnCallback(
                    static function ($propertyName) {
                        switch ($propertyName) {
                            case 'module':
                                return 'mockModule';
                            case 'function':
                                return 'mockFunction';
                        }

                        return null;
                    }
                )
            );

        /* @var \PHPUnit\Framework\MockObject\MockObject $policyCreateStructMock */
        $policyUpdateStructMock->expects($this->once())
            ->method('getLimitations')
            ->will($this->returnValue([$limitationMock]));

        $permissionResolverMock = $this->getPermissionResolverMock();
        $permissionResolverMock->expects($this->once())
            ->method('canUser')
            ->with(
                $this->equalTo('role'),
                $this->equalTo('update'),
                $this->equalTo($roleDraftMock)
            )->will($this->returnValue(true));

        /* @var \Ibexa\Contracts\Core\Repository\Values\User\Policy $policyDraftMock */
        /* @var \Ibexa\Contracts\Core\Repository\Values\User\PolicyUpdateStruct $policyUpdateStructMock */
        $roleServiceMock->updatePolicyByRoleDraft(
            $roleDraftMock,
            $policyDraftMock,
            $policyUpdateStructMock
        );
    }

    /**
     * Test for the assignRoleToUser() method.
     */
    public function testAssignRoleToUserThrowsUnauthorizedException()
    {
        $this->expectException(UnauthorizedException::class);

        $roleServiceMock = $this->getPartlyMockedRoleService();
        /** @var \Ibexa\Contracts\Core\Repository\Values\User\Role $roleMock */
        $roleMock = $this->createMock(Role::class);
        /** @var \Ibexa\Contracts\Core\Repository\Values\User\User $userMock */
        $userMock = $this->createMock(User::class);

        $permissionResolverMock = $this->getPermissionResolverMock();
        $permissionResolverMock->expects($this->once())
            ->method('canUser')
            ->with(
                $this->equalTo('role'),
                $this->equalTo('assign'),
                $this->equalTo($userMock),
                $this->equalTo([$roleMock])
            )->will($this->returnValue(false));

        $roleServiceMock->assignRoleToUser($roleMock, $userMock, null);
    }

    /**
     * Test for the assignRoleToUser() method.
     */
    public function testAssignRoleToUserThrowsLimitationValidationException()
    {
        $this->expectException(LimitationValidationException::class);

        $limitationMock = $this->createMock(RoleLimitation::class);
        $limitationTypeMock = $this->createMock(SPIType::class);

        $limitationTypeMock->expects($this->once())
            ->method('acceptValue')
            ->with($this->equalTo($limitationMock));
        $limitationTypeMock->expects($this->once())
            ->method('validate')
            ->with($this->equalTo($limitationMock))
            ->will($this->returnValue([42]));

        $limitationMock->expects($this->once())
            ->method('getIdentifier')
            ->will($this->returnValue('testIdentifier'));

        $settings = [
            'limitationTypes' => ['testIdentifier' => $limitationTypeMock],
        ];

        $roleServiceMock = $this->getPartlyMockedRoleService(null, $settings);

        /** @var \Ibexa\Contracts\Core\Repository\Values\User\Role $roleMock */
        $roleMock = $this->createMock(Role::class);
        /** @var \Ibexa\Contracts\Core\Repository\Values\User\User $userMock */
        $userMock = $this->createMock(User::class);

        $permissionResolverMock = $this->getPermissionResolverMock();
        $permissionResolverMock->expects($this->once())
            ->method('canUser')
            ->with(
                $this->equalTo('role'),
                $this->equalTo('assign'),
                $this->equalTo($userMock),
                $this->equalTo([$roleMock])
            )->will($this->returnValue(true));

        /* @var \Ibexa\Contracts\Core\Repository\Values\User\Limitation\RoleLimitation $limitationMock */
        $roleServiceMock->assignRoleToUser($roleMock, $userMock, $limitationMock);
    }

    /**
     * Test for the assignRoleToUser() method.
     */
    public function testAssignRoleToUserThrowsBadStateException()
    {
        $this->expectException(BadStateException::class);

        $roleServiceMock = $this->getPartlyMockedRoleService();
        /** @var \Ibexa\Contracts\Core\Repository\Values\User\Role $roleMock */
        $roleMock = $this->createMock(Role::class);
        /** @var \Ibexa\Contracts\Core\Repository\Values\User\User $userMock */
        $userMock = $this->createMock(User::class);
        $limitationMock = $this->createMock(RoleLimitation::class);

        $limitationMock->expects($this->once())
            ->method('getIdentifier')
            ->will($this->returnValue('testIdentifier'));

        $permissionResolverMock = $this->getPermissionResolverMock();
        $permissionResolverMock->expects($this->once())
            ->method('canUser')
            ->with(
                $this->equalTo('role'),
                $this->equalTo('assign'),
                $this->equalTo($userMock),
                $this->equalTo([$roleMock])
            )->will($this->returnValue(true));

        /* @var \Ibexa\Contracts\Core\Repository\Values\User\Limitation\RoleLimitation $limitationMock */
        $roleServiceMock->assignRoleToUser($roleMock, $userMock, $limitationMock);
    }

    /**
     * Test for the assignRoleToUser() method.
     */
    public function testAssignRoleToUser()
    {
        $limitationMock = $this->createMock(RoleLimitation::class);
        $limitationTypeMock = $this->createMock(SPIType::class);

        $limitationTypeMock->expects($this->once())
            ->method('acceptValue')
            ->with($this->equalTo($limitationMock));
        $limitationTypeMock->expects($this->once())
            ->method('validate')
            ->with($this->equalTo($limitationMock))
            ->will($this->returnValue([]));

        $limitationMock->expects($this->exactly(2))
            ->method('getIdentifier')
            ->will($this->returnValue('testIdentifier'));

        $settings = [
            'limitationTypes' => ['testIdentifier' => $limitationTypeMock],
        ];

        $roleServiceMock = $this->getPartlyMockedRoleService(['checkAssignmentAndFilterLimitationValues'], $settings);

        $repository = $this->getRepositoryMock();
        $roleMock = $this->createMock(Role::class);
        $userMock = $this->createMock(User::class);
        $userHandlerMock = $this->getPersistenceMockHandler('User\\Handler');

        $userMock->expects($this->any())
            ->method('__get')
            ->with('id')
            ->will($this->returnValue(24));

        $permissionResolverMock = $this->getPermissionResolverMock();
        $permissionResolverMock->expects($this->once())
            ->method('canUser')
            ->with(
                $this->equalTo('role'),
                $this->equalTo('assign'),
                $this->equalTo($userMock),
                $this->equalTo([$roleMock])
            )->will($this->returnValue(true));

        $roleMock->expects($this->any())
            ->method('__get')
            ->with('id')
            ->will($this->returnValue(42));

        $userHandlerMock->expects($this->once())
            ->method('loadRole')
            ->with($this->equalTo(42))
            ->will($this->returnValue(new SPIRole(['id' => 42])));

        $userHandlerMock->expects($this->once())
            ->method('load')
            ->with($this->equalTo(24))
            ->will($this->returnValue(new SPIUser(['id' => 24])));

        $roleServiceMock->expects($this->once())
            ->method('checkAssignmentAndFilterLimitationValues')
            ->with(24, $this->isInstanceOf(SPIRole::class), ['testIdentifier' => []])
            ->will($this->returnValue(['testIdentifier' => []]));

        $repository->expects($this->once())->method('beginTransaction');
        $userHandlerMock = $this->getPersistenceMockHandler('User\\Handler');
        $userHandlerMock->expects($this->once())
            ->method('assignRole')
            ->with(
                $this->equalTo(24),
                $this->equalTo(42),
                $this->equalTo(['testIdentifier' => []])
            );
        $repository->expects($this->once())->method('commit');

        /* @var \Ibexa\Contracts\Core\Repository\Values\User\Role $roleMock */
        /* @var \Ibexa\Contracts\Core\Repository\Values\User\User $userMock */
        /* @var \Ibexa\Contracts\Core\Repository\Values\User\Limitation\RoleLimitation $limitationMock */
        $roleServiceMock->assignRoleToUser($roleMock, $userMock, $limitationMock);
    }

    /**
     * Test for the assignRoleToUser() method.
     */
    public function testAssignRoleToUserWithNullLimitation()
    {
        $repository = $this->getRepositoryMock();
        $roleServiceMock = $this->getPartlyMockedRoleService(['checkAssignmentAndFilterLimitationValues']);
        $roleMock = $this->createMock(Role::class);
        $userMock = $this->createMock(User::class);
        $userHandlerMock = $this->getPersistenceMockHandler('User\\Handler');

        $userMock->expects($this->any())
            ->method('__get')
            ->with('id')
            ->will($this->returnValue(24));

        $permissionResolverMock = $this->getPermissionResolverMock();
        $permissionResolverMock->expects($this->once())
            ->method('canUser')
            ->with(
                $this->equalTo('role'),
                $this->equalTo('assign'),
                $this->equalTo($userMock),
                $this->equalTo([$roleMock])
            )->will($this->returnValue(true));

        $roleMock->expects($this->any())
            ->method('__get')
            ->with('id')
            ->will($this->returnValue(42));

        $userHandlerMock->expects($this->once())
            ->method('loadRole')
            ->with($this->equalTo(42))
            ->will($this->returnValue(new SPIRole(['id' => 42])));

        $userHandlerMock->expects($this->once())
            ->method('load')
            ->with($this->equalTo(24))
            ->will($this->returnValue(new SPIUser(['id' => 24])));

        $roleServiceMock->expects($this->once())
            ->method('checkAssignmentAndFilterLimitationValues')
            ->with(24, $this->isInstanceOf(SPIRole::class), null)
            ->will($this->returnValue(null));

        $repository->expects($this->once())->method('beginTransaction');
        $userHandlerMock = $this->getPersistenceMockHandler('User\\Handler');
        $userHandlerMock->expects($this->once())
            ->method('assignRole')
            ->with(
                $this->equalTo(24),
                $this->equalTo(42),
                $this->equalTo(null)
            );
        $repository->expects($this->once())->method('commit');

        /* @var \Ibexa\Contracts\Core\Repository\Values\User\Role $roleMock */
        /* @var \Ibexa\Contracts\Core\Repository\Values\User\User $userMock */
        $roleServiceMock->assignRoleToUser($roleMock, $userMock, null);
    }

    /**
     * Test for the assignRoleToUser() method.
     */
    public function testAssignRoleToUserWithRollback()
    {
        $this->expectException(\Exception::class);

        $repository = $this->getRepositoryMock();
        $roleServiceMock = $this->getPartlyMockedRoleService(['checkAssignmentAndFilterLimitationValues']);
        $roleMock = $this->createMock(Role::class);
        $userMock = $this->createMock(User::class);
        $userHandlerMock = $this->getPersistenceMockHandler('User\\Handler');

        $userMock->expects($this->any())
            ->method('__get')
            ->with('id')
            ->will($this->returnValue(24));

        $permissionResolverMock = $this->getPermissionResolverMock();
        $permissionResolverMock->expects($this->once())
            ->method('canUser')
            ->with(
                $this->equalTo('role'),
                $this->equalTo('assign'),
                $this->equalTo($userMock),
                $this->equalTo([$roleMock])
            )->will($this->returnValue(true));

        $roleMock->expects($this->any())
            ->method('__get')
            ->with('id')
            ->will($this->returnValue(42));

        $userHandlerMock->expects($this->once())
            ->method('loadRole')
            ->with($this->equalTo(42))
            ->will($this->returnValue(new SPIRole(['id' => 42])));

        $userHandlerMock->expects($this->once())
            ->method('load')
            ->with($this->equalTo(24))
            ->will($this->returnValue(new SPIUser(['id' => 24])));

        $roleServiceMock->expects($this->once())
            ->method('checkAssignmentAndFilterLimitationValues')
            ->with(24, $this->isInstanceOf(SPIRole::class), null)
            ->will($this->returnValue(null));

        $repository->expects($this->once())->method('beginTransaction');
        $userHandlerMock = $this->getPersistenceMockHandler('User\\Handler');
        $userHandlerMock->expects($this->once())
            ->method('assignRole')
            ->with(
                $this->equalTo(24),
                $this->equalTo(42),
                $this->equalTo(null)
            )->will($this->throwException(new \Exception()));
        $repository->expects($this->once())->method('rollback');

        /* @var \Ibexa\Contracts\Core\Repository\Values\User\Role $roleMock */
        /* @var \Ibexa\Contracts\Core\Repository\Values\User\User $userMock */
        $roleServiceMock->assignRoleToUser($roleMock, $userMock, null);
    }

    /**
     * Test for the assignRoleToUserGroup() method.
     */
    public function testAssignRoleToUserGroupThrowsUnauthorizedException()
    {
        $this->expectException(UnauthorizedException::class);

        $repository = $this->getRepositoryMock();
        $roleServiceMock = $this->getPartlyMockedRoleService();
        /** @var \Ibexa\Contracts\Core\Repository\Values\User\Role $roleMock */
        $roleMock = $this->createMock(Role::class);
        /** @var \Ibexa\Contracts\Core\Repository\Values\User\UserGroup $userGroupMock */
        $userGroupMock = $this->createMock(UserGroup::class);

        $permissionResolverMock = $this->getPermissionResolverMock();
        $permissionResolverMock->expects($this->once())
            ->method('canUser')
            ->with(
                $this->equalTo('role'),
                $this->equalTo('assign'),
                $this->equalTo($userGroupMock),
                $this->equalTo([$roleMock])
            )->will($this->returnValue(false));

        $roleServiceMock->assignRoleToUserGroup($roleMock, $userGroupMock, null);
    }

    /**
     * Test for the assignRoleToUserGroup() method.
     */
    public function testAssignRoleToUserGroupThrowsLimitationValidationException()
    {
        $this->expectException(LimitationValidationException::class);

        $limitationMock = $this->createMock(RoleLimitation::class);
        $limitationTypeMock = $this->createMock(SPIType::class);

        $limitationTypeMock->expects($this->once())
            ->method('acceptValue')
            ->with($this->equalTo($limitationMock));
        $limitationTypeMock->expects($this->once())
            ->method('validate')
            ->with($this->equalTo($limitationMock))
            ->will($this->returnValue([42]));

        $limitationMock->expects($this->once())
            ->method('getIdentifier')
            ->will($this->returnValue('testIdentifier'));

        $settings = [
            'limitationTypes' => ['testIdentifier' => $limitationTypeMock],
        ];

        $roleServiceMock = $this->getPartlyMockedRoleService(null, $settings);

        $repository = $this->getRepositoryMock();
        /** @var \Ibexa\Contracts\Core\Repository\Values\User\Role $roleMock */
        $roleMock = $this->createMock(Role::class);
        /** @var \Ibexa\Contracts\Core\Repository\Values\User\UserGroup $userGroupMock */
        $userGroupMock = $this->createMock(UserGroup::class);

        $permissionResolverMock = $this->getPermissionResolverMock();
        $permissionResolverMock->expects($this->once())
            ->method('canUser')
            ->with(
                $this->equalTo('role'),
                $this->equalTo('assign'),
                $this->equalTo($userGroupMock),
                $this->equalTo([$roleMock])
            )->will($this->returnValue(true));

        /* @var \Ibexa\Contracts\Core\Repository\Values\User\Limitation\RoleLimitation $limitationMock */
        $roleServiceMock->assignRoleToUserGroup($roleMock, $userGroupMock, $limitationMock);
    }

    /**
     * Test for the assignRoleToUserGroup() method.
     */
    public function testAssignRoleGroupToUserThrowsBadStateException()
    {
        $this->expectException(BadStateException::class);

        $repository = $this->getRepositoryMock();
        $roleServiceMock = $this->getPartlyMockedRoleService();
        /** @var \Ibexa\Contracts\Core\Repository\Values\User\Role $roleMock */
        $roleMock = $this->createMock(Role::class);
        /** @var \Ibexa\Contracts\Core\Repository\Values\User\UserGroup $userGroupMock */
        $userGroupMock = $this->createMock(UserGroup::class);
        $limitationMock = $this->createMock(RoleLimitation::class);

        $limitationMock->expects($this->once())
            ->method('getIdentifier')
            ->will($this->returnValue('testIdentifier'));

        $permissionResolverMock = $this->getPermissionResolverMock();
        $permissionResolverMock->expects($this->once())
            ->method('canUser')
            ->with(
                $this->equalTo('role'),
                $this->equalTo('assign'),
                $this->equalTo($userGroupMock),
                $this->equalTo([$roleMock])
            )->will($this->returnValue(true));

        /* @var \Ibexa\Contracts\Core\Repository\Values\User\Limitation\RoleLimitation $limitationMock */
        $roleServiceMock->assignRoleToUserGroup($roleMock, $userGroupMock, $limitationMock);
    }

    /**
     * Test for the assignRoleToUserGroup() method.
     */
    public function testAssignRoleToUserGroup()
    {
        $limitationMock = $this->createMock(RoleLimitation::class);
        $limitationTypeMock = $this->createMock(SPIType::class);

        $limitationTypeMock->expects($this->once())
            ->method('acceptValue')
            ->with($this->equalTo($limitationMock));
        $limitationTypeMock->expects($this->once())
            ->method('validate')
            ->with($this->equalTo($limitationMock))
            ->will($this->returnValue([]));

        $limitationMock->expects($this->exactly(2))
            ->method('getIdentifier')
            ->will($this->returnValue('testIdentifier'));

        $settings = [
            'limitationTypes' => ['testIdentifier' => $limitationTypeMock],
        ];

        $roleServiceMock = $this->getPartlyMockedRoleService(['checkAssignmentAndFilterLimitationValues'], $settings);

        $repository = $this->getRepositoryMock();
        $roleMock = $this->createMock(Role::class);
        $userGroupMock = $this->createMock(UserGroup::class);
        $userServiceMock = $this->createMock(UserService::class);
        $userHandlerMock = $this->getPersistenceMockHandler('User\\Handler');

        $repository->expects($this->once())
            ->method('getUserService')
            ->will($this->returnValue($userServiceMock));
        $userGroupMock->expects($this->any())
            ->method('__get')
            ->with('id')
            ->will($this->returnValue(24));

        $permissionResolverMock = $this->getPermissionResolverMock();
        $permissionResolverMock->expects($this->once())
            ->method('canUser')
            ->with(
                $this->equalTo('role'),
                $this->equalTo('assign'),
                $this->equalTo($userGroupMock),
                $this->equalTo([$roleMock])
            )->will($this->returnValue(true));

        $roleMock->expects($this->any())
            ->method('__get')
            ->with('id')
            ->will($this->returnValue(42));

        $userHandlerMock->expects($this->once())
            ->method('loadRole')
            ->with($this->equalTo(42))
            ->will($this->returnValue(new SPIRole(['id' => 42])));

        $userServiceMock->expects($this->once())
            ->method('loadUserGroup')
            ->with($this->equalTo(24))
            ->will($this->returnValue($userGroupMock));

        $roleServiceMock->expects($this->once())
            ->method('checkAssignmentAndFilterLimitationValues')
            ->with(24, $this->isInstanceOf(SPIRole::class), ['testIdentifier' => []])
            ->will($this->returnValue(['testIdentifier' => []]));

        $repository->expects($this->once())->method('beginTransaction');
        $userHandlerMock = $this->getPersistenceMockHandler('User\\Handler');
        $userHandlerMock->expects($this->once())
            ->method('assignRole')
            ->with(
                $this->equalTo(24),
                $this->equalTo(42),
                $this->equalTo(['testIdentifier' => []])
            );
        $repository->expects($this->once())->method('commit');

        /* @var \Ibexa\Contracts\Core\Repository\Values\User\Role $roleMock */
        /* @var \Ibexa\Contracts\Core\Repository\Values\User\UserGroup $userGroupMock */
        /* @var \Ibexa\Contracts\Core\Repository\Values\User\Limitation\RoleLimitation $limitationMock */
        $roleServiceMock->assignRoleToUserGroup($roleMock, $userGroupMock, $limitationMock);
    }

    /**
     * Test for the assignRoleToUserGroup() method.
     */
    public function testAssignRoleToUserGroupWithNullLimitation()
    {
        $repository = $this->getRepositoryMock();
        $roleServiceMock = $this->getPartlyMockedRoleService(['checkAssignmentAndFilterLimitationValues']);
        $roleMock = $this->createMock(Role::class);
        $userGroupMock = $this->createMock(UserGroup::class);
        $userServiceMock = $this->createMock(UserService::class);
        $userHandlerMock = $this->getPersistenceMockHandler('User\\Handler');

        $repository->expects($this->once())
            ->method('getUserService')
            ->will($this->returnValue($userServiceMock));
        $userGroupMock->expects($this->any())
            ->method('__get')
            ->with('id')
            ->will($this->returnValue(24));

        $permissionResolverMock = $this->getPermissionResolverMock();
        $permissionResolverMock->expects($this->once())
            ->method('canUser')
            ->with(
                $this->equalTo('role'),
                $this->equalTo('assign'),
                $this->equalTo($userGroupMock),
                $this->equalTo([$roleMock])
            )->will($this->returnValue(true));

        $roleMock->expects($this->any())
            ->method('__get')
            ->with('id')
            ->will($this->returnValue(42));

        $userHandlerMock->expects($this->once())
            ->method('loadRole')
            ->with($this->equalTo(42))
            ->will($this->returnValue(new SPIRole(['id' => 42])));

        $userServiceMock->expects($this->once())
            ->method('loadUserGroup')
            ->with($this->equalTo(24))
            ->will($this->returnValue($userGroupMock));

        $roleServiceMock->expects($this->once())
            ->method('checkAssignmentAndFilterLimitationValues')
            ->with(24, $this->isInstanceOf(SPIRole::class), null)
            ->will($this->returnValue(null));

        $repository->expects($this->once())->method('beginTransaction');
        $userHandlerMock = $this->getPersistenceMockHandler('User\\Handler');
        $userHandlerMock->expects($this->once())
            ->method('assignRole')
            ->with(
                $this->equalTo(24),
                $this->equalTo(42),
                $this->equalTo(null)
            );
        $repository->expects($this->once())->method('commit');

        /* @var \Ibexa\Contracts\Core\Repository\Values\User\Role $roleMock */
        /* @var \Ibexa\Contracts\Core\Repository\Values\User\UserGroup $userGroupMock */
        $roleServiceMock->assignRoleToUserGroup($roleMock, $userGroupMock, null);
    }

    /**
     * Test for the assignRoleToUserGroup() method.
     */
    public function testAssignRoleToUserGroupWithRollback()
    {
        $this->expectException(\Exception::class);

        $repository = $this->getRepositoryMock();
        $roleServiceMock = $this->getPartlyMockedRoleService(['checkAssignmentAndFilterLimitationValues']);
        $roleMock = $this->createMock(Role::class);
        $userGroupMock = $this->createMock(UserGroup::class);
        $userServiceMock = $this->createMock(UserService::class);
        $userHandlerMock = $this->getPersistenceMockHandler('User\\Handler');

        $repository->expects($this->once())
            ->method('getUserService')
            ->will($this->returnValue($userServiceMock));
        $userGroupMock->expects($this->any())
            ->method('__get')
            ->with('id')
            ->will($this->returnValue(24));

        $permissionResolverMock = $this->getPermissionResolverMock();
        $permissionResolverMock->expects($this->once())
            ->method('canUser')
            ->with(
                $this->equalTo('role'),
                $this->equalTo('assign'),
                $this->equalTo($userGroupMock),
                $this->equalTo([$roleMock])
            )->will($this->returnValue(true));

        $roleMock->expects($this->any())
            ->method('__get')
            ->with('id')
            ->will($this->returnValue(42));

        $userHandlerMock->expects($this->once())
            ->method('loadRole')
            ->with($this->equalTo(42))
            ->will($this->returnValue(new SPIRole(['id' => 42])));

        $userServiceMock->expects($this->once())
            ->method('loadUserGroup')
            ->with($this->equalTo(24))
            ->will($this->returnValue($userGroupMock));

        $roleServiceMock->expects($this->once())
            ->method('checkAssignmentAndFilterLimitationValues')
            ->with(24, $this->isInstanceOf(SPIRole::class), null)
            ->will($this->returnValue(null));

        $repository->expects($this->once())->method('beginTransaction');
        $userHandlerMock = $this->getPersistenceMockHandler('User\\Handler');
        $userHandlerMock->expects($this->once())
            ->method('assignRole')
            ->with(
                $this->equalTo(24),
                $this->equalTo(42),
                $this->equalTo(null)
            )->will($this->throwException(new \Exception()));
        $repository->expects($this->once())->method('rollback');

        /* @var \Ibexa\Contracts\Core\Repository\Values\User\Role $roleMock */
        /* @var \Ibexa\Contracts\Core\Repository\Values\User\UserGroup $userGroupMock */
        $roleServiceMock->assignRoleToUserGroup($roleMock, $userGroupMock, null);
    }

    public function testRemovePolicyByRoleDraftThrowsUnauthorizedException()
    {
        $this->expectException(UnauthorizedException::class);

        $roleDraftMock = $this->createMock(RoleDraft::class);
        $roleDomainMapper = $this->createMock(RoleDomainMapper::class);
        $roleDomainMapper
            ->method('buildDomainRoleObject')
            ->willReturn($roleDraftMock);

        $roleServiceMock = $this->getPartlyMockedRoleService(null, [], $roleDomainMapper);
        $policyDraftMock = $this->createMock(PolicyDraft::class);

        $policyDraftMock->expects($this->any())
            ->method('__get')
            ->will(
                $this->returnValueMap(
                    [
                        ['roleId', 17],
                    ]
                )
            );

        $permissionResolverMock = $this->getPermissionResolverMock();
        $permissionResolverMock->expects($this->once())
            ->method('canUser')
            ->with(
                $this->equalTo('role'),
                $this->equalTo('update'),
                $this->equalTo($roleDraftMock)
            )->will($this->returnValue(false));

        /* @var \Ibexa\Contracts\Core\Repository\Values\User\Policy $policyMock */
        $roleServiceMock->removePolicyByRoleDraft($roleDraftMock, $policyDraftMock);
    }

    /**
     * Test for the removePolicyByRoleDraft() method.
     */
    public function testRemovePolicyByRoleDraftWithRollback()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Handler threw an exception');

        $repository = $this->getRepositoryMock();
        $roleDraftMock = $this->createMock(RoleDraft::class);
        $roleDraftMock->expects($this->any())
            ->method('__get')
            ->with('id')
            ->willReturn(17);

        $roleDomainMapper = $this->createMock(RoleDomainMapper::class);
        $roleDomainMapper
            ->method('buildDomainRoleObject')
            ->willReturn($roleDraftMock);
        $roleServiceMock = $this->getPartlyMockedRoleService(null, [], $roleDomainMapper);

        $policyDraftMock = $this->createMock(PolicyDraft::class);
        $policyDraftMock->expects($this->any())
            ->method('__get')
            ->will(
                $this->returnValueMap(
                    [
                        ['id', 42],
                        ['roleId', 17],
                    ]
                )
            );

        $permissionResolverMock = $this->getPermissionResolverMock();
        $permissionResolverMock->expects($this->once())
            ->method('canUser')
            ->with(
                $this->equalTo('role'),
                $this->equalTo('update'),
                $this->equalTo($roleDraftMock)
            )->will($this->returnValue(true));

        $repository->expects($this->once())->method('beginTransaction');

        $userHandlerMock = $this->getPersistenceMockHandler('User\\Handler');

        $userHandlerMock->expects($this->once())
            ->method('deletePolicy')
            ->with(
                $this->equalTo(42)
            )->will($this->throwException(new \Exception('Handler threw an exception')));

        $repository->expects($this->once())->method('rollback');

        /* @var \Ibexa\Contracts\Core\Repository\Values\User\Policy $policyDraftMock */
        $roleServiceMock->removePolicyByRoleDraft($roleDraftMock, $policyDraftMock);
    }

    public function testRemovePolicyByRoleDraft()
    {
        $repository = $this->getRepositoryMock();
        $roleDraftMock = $this->createMock(RoleDraft::class);
        $roleDraftMock
            ->expects($this->any())
            ->method('__get')
            ->with('id')
            ->willReturn(17);

        $roleDomainMapper = $this->createMock(RoleDomainMapper::class);
        $roleDomainMapper
            ->method('buildDomainRoleObject')
            ->willReturn($roleDraftMock);

        $roleServiceMock = $this->getPartlyMockedRoleService(['loadRoleDraft'], [], $roleDomainMapper);

        $policyDraftMock = $this->createMock(PolicyDraft::class);
        $policyDraftMock->expects($this->any())
            ->method('__get')
            ->will(
                $this->returnValueMap(
                    [
                        ['id', 42],
                        ['roleId', 17],
                    ]
                )
            );

        $permissionResolverMock = $this->getPermissionResolverMock();
        $permissionResolverMock->expects($this->once())
            ->method('canUser')
            ->with(
                $this->equalTo('role'),
                $this->equalTo('update'),
                $this->equalTo($roleDraftMock)
            )->will($this->returnValue(true));

        $repository->expects($this->once())->method('beginTransaction');

        $userHandlerMock = $this->getPersistenceMockHandler('User\\Handler');

        $userHandlerMock->expects($this->once())
            ->method('deletePolicy')
            ->with(
                $this->equalTo(42)
            );

        $roleServiceMock->expects($this->once())
            ->method('loadRoleDraft')
            ->with($this->equalTo(17))
            ->will($this->returnValue($roleDraftMock));

        $repository->expects($this->once())->method('commit');

        /* @var \Ibexa\Contracts\Core\Repository\Values\User\PolicyDraft $policyDraftMock */
        $roleServiceMock->removePolicyByRoleDraft($roleDraftMock, $policyDraftMock);
    }

    /** @var \Ibexa\Core\Repository\RoleService */
    protected $partlyMockedRoleService;

    /**
     * Returns the role service to test with $methods mocked.
     *
     * Injected Repository comes from {@see getRepositoryMock()} and persistence handler from {@see getPersistenceMock()}
     *
     * @param string[] $methods
     * @param array $settings
     * @param \Ibexa\Core\Repository\Mapper\RoleDomainMapper|null $roleDomainMapper
     *
     * @return \Ibexa\Core\Repository\RoleService|\PHPUnit\Framework\MockObject\MockObject
     */
    protected function getPartlyMockedRoleService(
        array $methods = null,
        array $settings = [],
        ?RoleDomainMapper $roleDomainMapper = null
    ) {
        if (!isset($this->partlyMockedRoleService) || !empty($settings) || $roleDomainMapper) {
            $limitationService = new LimitationService(
                new ArrayIterator($settings['limitationTypes'] ?? [])
            );
            if ($roleDomainMapper === null) {
                $roleDomainMapper = $this->getMockBuilder(RoleDomainMapper::class)
                    ->setMethods([])
                    ->setConstructorArgs([$limitationService])
                    ->getMock();
            }

            $this->partlyMockedRoleService = $this->getMockBuilder(RoleService::class)
                ->setMethods($methods)
                ->setConstructorArgs(
                    [
                        $this->getRepositoryMock(),
                        $this->getPersistenceMockHandler('User\\Handler'),
                        $limitationService,
                        $roleDomainMapper,
                        $settings,
                    ]
                )
                ->getMock();
        }

        return $this->partlyMockedRoleService;
    }

    /**
     * @return \Ibexa\Contracts\Core\Repository\Repository|\PHPUnit\Framework\MockObject\MockObject
     */
    protected function getRepositoryMock(): Repository
    {
        $repositoryMock = parent::getRepositoryMock();
        $repositoryMock
            ->expects($this->any())
            ->method('getPermissionResolver')
            ->willReturn($this->getPermissionResolverMock());

        return $repositoryMock;
    }
}

class_alias(RoleTest::class, 'eZ\Publish\Core\Repository\Tests\Service\Mock\RoleTest');
