<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Integration\Core\Repository\Values\User\Limitation;

use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException;
use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Core\Repository\Values\ObjectState\ObjectStateGroup;
use Ibexa\Contracts\Core\Repository\Values\User\Limitation\ObjectStateLimitation;
use Ibexa\Contracts\Core\Repository\Values\User\RoleCreateStruct;

/**
 * @covers \Ibexa\Contracts\Core\Repository\Values\User\Limitation\ObjectStateLimitation
 * @group integration
 * @group limitation
 */
class ObjectStateLimitationTest extends BaseLimitationTest
{
    /**
     * @throws \ErrorException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\ForbiddenException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function testObjectStateLimitationAllow()
    {
        $repository = $this->getRepository();
        $permissionResolver = $repository->getPermissionResolver();

        $notLockedState = $this->generateId('objectstate', 2);

        $contentService = $repository->getContentService();
        /* BEGIN: Use Case */
        $user = $this->createUserVersion1();

        $roleService = $repository->getRoleService();

        $role = $roleService->loadRoleByIdentifier('Editor');
        $roleDraft = $roleService->createRoleDraft($role);
        // Search for the new policy instance
        /** @var \Ibexa\Contracts\Core\Repository\Values\User\PolicyDraft $policy */
        $removePolicy = null;
        foreach ($roleDraft->getPolicies() as $policy) {
            if ('content' != $policy->module || 'remove' != $policy->function) {
                continue;
            }
            $removePolicy = $policy;
            break;
        }

        if (null === $removePolicy) {
            throw new \ErrorException('No content:remove policy found.');
        }

        // Only allow deletion of content with default state
        $policyUpdate = $roleService->newPolicyUpdateStruct();
        $policyUpdate->addLimitation(
            new ObjectStateLimitation(
                [
                    'limitationValues' => [
                        $notLockedState,
                    ],
                ]
            )
        );
        $roleService->updatePolicyByRoleDraft(
            $roleDraft,
            $removePolicy,
            $policyUpdate
        );

        // Allow user to create everything
        $policyCreate = $roleService->newPolicyCreateStruct('content', 'create');

        $roleService->addPolicyByRoleDraft(
            $roleDraft,
            $policyCreate
        );
        $roleService->publishRoleDraft($roleDraft);

        $roleService->assignRoleToUser($role, $user);

        $permissionResolver->setCurrentUserReference($user);

        $draft = $this->createWikiPageDraft();

        $contentService->deleteContent($draft->contentInfo);
        /* END: Use Case */

        $this->expectException(NotFoundException::class);

        $contentService->loadContent($draft->id);
    }

    /**
     * Tests a ObjectStateLimitation.
     *
     * @covers \Ibexa\Contracts\Core\Repository\Values\User\Limitation\ObjectStateLimitation
     *
     * @throws \ErrorException
     */
    public function testObjectStateLimitationForbid()
    {
        $this->expectException(UnauthorizedException::class);

        $repository = $this->getRepository();
        $permissionResolver = $repository->getPermissionResolver();

        $lockedState = $this->generateId('objectstate', 1);

        $contentService = $repository->getContentService();
        /* BEGIN: Use Case */
        $user = $this->createUserVersion1();

        $roleService = $repository->getRoleService();

        $role = $roleService->loadRoleByIdentifier('Editor');
        $roleDraft = $roleService->createRoleDraft($role);
        // Search for the new policy instance
        /** @var \Ibexa\Contracts\Core\Repository\Values\User\PolicyDraft $policy */
        $removePolicy = null;
        foreach ($roleDraft->getPolicies() as $policy) {
            if ('content' != $policy->module || 'remove' != $policy->function) {
                continue;
            }
            $removePolicy = $policy;
            break;
        }

        if (null === $removePolicy) {
            throw new \ErrorException('No content:remove policy found.');
        }

        // Only allow deletion of content with default state
        $policyUpdate = $roleService->newPolicyUpdateStruct();
        $policyUpdate->addLimitation(
            new ObjectStateLimitation(
                [
                    'limitationValues' => [
                        $lockedState,
                    ],
                ]
            )
        );
        $roleService->updatePolicyByRoleDraft(
            $roleDraft,
            $removePolicy,
            $policyUpdate
        );

        // Allow user to create everything
        $policyCreate = $roleService->newPolicyCreateStruct('content', 'create');

        $roleService->addPolicyByRoleDraft(
            $roleDraft,
            $policyCreate
        );
        $roleService->publishRoleDraft($roleDraft);

        $roleService->assignRoleToUser($role, $user);

        $permissionResolver->setCurrentUserReference($user);

        $draft = $this->createWikiPageDraft();

        $contentService->deleteContent($draft->contentInfo);
        /* END: Use Case */
    }

    /**
     * Tests an ObjectStateLimitation.
     *
     * Checks if the action is correctly forbidden when using ObjectStateLimitation
     * with limitation values from two different StateGroups.
     *
     * @throws \ErrorException
     */
    public function testObjectStateLimitationForbidVariant()
    {
        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessage('\'remove\' \'content\'');

        $repository = $this->getRepository();
        $objectStateGroup = $this->createObjectStateGroup();
        $objectState = $this->createObjectState($objectStateGroup);

        $lockedState = $this->generateId('objectstate', 1);
        $defaultStateFromAnotherGroup = $this->generateId('objectstate', $objectState->id);

        $contentService = $repository->getContentService();
        /* BEGIN: Use Case */
        $user = $this->createUserVersion1();

        $roleService = $repository->getRoleService();

        $role = $roleService->loadRoleByIdentifier('Editor');
        $roleDraft = $roleService->createRoleDraft($role);
        // Search for the new policy instance
        /** @var \Ibexa\Contracts\Core\Repository\Values\User\PolicyDraft $policy */
        $removePolicy = null;
        foreach ($roleDraft->getPolicies() as $policy) {
            if ('content' !== $policy->module || 'remove' !== $policy->function) {
                continue;
            }
            $removePolicy = $policy;
            break;
        }

        $this->assertNotNull($removePolicy);

        // Only allow deletion of content with locked state and the default state from another State Group
        $policyUpdate = $roleService->newPolicyUpdateStruct();
        $policyUpdate->addLimitation(
            new ObjectStateLimitation(
                [
                    'limitationValues' => [
                        $lockedState,
                        $defaultStateFromAnotherGroup,
                    ],
                ]
            )
        );
        $roleService->updatePolicyByRoleDraft(
            $roleDraft,
            $removePolicy,
            $policyUpdate
        );

        // Allow user to create everything
        $policyCreate = $roleService->newPolicyCreateStruct('content', 'create');

        $roleService->addPolicyByRoleDraft(
            $roleDraft,
            $policyCreate
        );
        $roleService->publishRoleDraft($roleDraft);

        $roleService->assignRoleToUser($role, $user);

        $repository->getPermissionResolver()->setCurrentUserReference($user);

        $draft = $this->createWikiPageDraft();

        $contentService->deleteContent($draft->contentInfo);
        /* END: Use Case */
    }

    /**
     * Create new State Group.
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\ObjectState\ObjectStateGroup
     */
    private function createObjectStateGroup()
    {
        $objectStateService = $this->getRepository()->getObjectStateService();

        $objectStateGroupCreateStruct = $objectStateService->newObjectStateGroupCreateStruct('second_group');
        $objectStateGroupCreateStruct->defaultLanguageCode = 'eng-US';
        $objectStateGroupCreateStruct->names = ['eng-US' => 'Second Group'];

        return $objectStateService->createObjectStateGroup($objectStateGroupCreateStruct);
    }

    /**
     * Create new State and assign it to the $objectStateGroup.
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\ObjectState\ObjectStateGroup $objectStateGroup
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\ObjectState\ObjectState
     */
    private function createObjectState(ObjectStateGroup $objectStateGroup)
    {
        $objectStateService = $this->getRepository()->getObjectStateService();

        $objectStateCreateStruct = $objectStateService->newObjectStateCreateStruct('default_state');
        $objectStateCreateStruct->defaultLanguageCode = 'eng-US';
        $objectStateCreateStruct->names = ['eng-US' => 'Default state'];

        return $objectStateService->createObjectState($objectStateGroup, $objectStateCreateStruct);
    }

    /**
     * Tests an ObjectStateLimitation.
     *
     * Checks if the search results are correctly filtered when using ObjectStateLimitation
     * with limitation values from two different StateGroups.
     */
    public function testObjectStateLimitationSearch()
    {
        $repository = $this->getRepository();
        $objectStateGroup = $this->createObjectStateGroup();
        $objectState = $this->createObjectState($objectStateGroup);

        $lockedState = $this->generateId('objectstate', 1);
        $defaultStateFromAnotherGroup = $this->generateId('objectstate', $objectState->id);

        $roleService = $repository->getRoleService();
        $roleName = 'role_with_object_state_limitation';
        $roleCreateStruct = $roleService->newRoleCreateStruct($roleName);
        $this->addPolicyToNewRole($roleCreateStruct, 'content', 'read', [
            new ObjectStateLimitation([
                'limitationValues' => [$lockedState, $defaultStateFromAnotherGroup],
            ]),
        ]);
        $roleService->publishRoleDraft(
            $roleService->createRole($roleCreateStruct)
        );

        $permissionResolver = $repository->getPermissionResolver();
        $user = $this->createCustomUserVersion1('Test group', $roleName);
        $adminUser = $permissionResolver->getCurrentUserReference();

        $wikiPage = $this->createWikiPage();

        $permissionResolver->setCurrentUserReference($user);

        $query = new Query();
        $query->filter = new Criterion\MatchAll();
        $query->limit = 50;

        $this->refreshSearch($repository);
        $searchResultsBefore = $repository->getSearchService()->findContent($query);

        $permissionResolver->setCurrentUserReference($adminUser);

        //change the Object State to the one that doesn't match the Limitation
        $stateService = $repository->getObjectStateService();
        $stateService->setContentState(
            $wikiPage->contentInfo,
            $stateService->loadObjectStateGroup(2),
            $stateService->loadObjectState(2)
        );

        $permissionResolver->setCurrentUserReference($user);

        $this->refreshSearch($repository);
        $searchResultsAfter = $repository->getSearchService()->findContent($query);

        $this->assertEquals($searchResultsBefore->totalCount - 1, $searchResultsAfter->totalCount);
    }

    /**
     * Add policy to a new role.
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\User\RoleCreateStruct $roleCreateStruct
     * @param string $module
     * @param string $function
     * @param \Ibexa\Contracts\Core\Repository\Values\User\Limitation[] $limitations
     */
    private function addPolicyToNewRole(RoleCreateStruct $roleCreateStruct, $module, $function, array $limitations)
    {
        $roleService = $this->getRepository()->getRoleService();
        $policyCreateStruct = $roleService->newPolicyCreateStruct($module, $function);
        foreach ($limitations as $limitation) {
            $policyCreateStruct->addLimitation($limitation);
        }
        $roleCreateStruct->addPolicy($policyCreateStruct);
    }
}

class_alias(ObjectStateLimitationTest::class, 'eZ\Publish\API\Repository\Tests\Values\User\Limitation\ObjectStateLimitationTest');
