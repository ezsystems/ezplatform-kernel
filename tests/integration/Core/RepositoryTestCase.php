<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Core;

use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\User\Role;
use eZ\Publish\API\Repository\Values\User\User;
use Ibexa\Contracts\Core\Test\IbexaKernelTestCase;
use InvalidArgumentException;

abstract class RepositoryTestCase extends IbexaKernelTestCase
{
    public const CONTENT_TREE_ROOT_ID = 2;

    private const CONTENT_TYPE_FOLDER_IDENTIFIER = 'folder';

    protected function setUp(): void
    {
        parent::setUp();

        self::loadSchema();
        self::loadFixtures();

        self::setAdministratorUser();
    }

    /**
     * @param array<string, string> $names
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\Exception
     */
    public function createFolder(array $names, int $parentLocationId = self::CONTENT_TREE_ROOT_ID): Content
    {
        $contentService = self::getContentService();
        $draft = $this->createFolderDraft($names, $parentLocationId);

        return $contentService->publishVersion($draft->getVersionInfo());
    }

    /**
     * @param array<string, string> $names
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\Exception
     */
    public function createFolderDraft(array $names, int $parentLocationId = self::CONTENT_TREE_ROOT_ID): Content
    {
        if (empty($names)) {
            throw new InvalidArgumentException(__METHOD__ . ' requires $names to be not empty');
        }

        $contentService = self::getContentService();
        $contentTypeService = self::getContentTypeService();
        $locationService = self::getLocationService();

        $folderType = $contentTypeService->loadContentTypeByIdentifier(self::CONTENT_TYPE_FOLDER_IDENTIFIER);
        $mainLanguageCode = array_keys($names)[0];
        $contentCreateStruct = $contentService->newContentCreateStruct($folderType, $mainLanguageCode);
        foreach ($names as $languageCode => $name) {
            $contentCreateStruct->setField('name', $name, $languageCode);
        }

        return $contentService->createContent(
            $contentCreateStruct,
            [
                $locationService->newLocationCreateStruct($parentLocationId),
            ]
        );
    }

    /**
     * @param array<mixed> $policiesData
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\LimitationValidationException
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     */
    public function createRoleWithPolicies(string $roleName, array $policiesData): Role
    {
        $roleService = self::getRoleService();

        $roleCreateStruct = $roleService->newRoleCreateStruct($roleName);
        foreach ($policiesData as $policyData) {
            $policyCreateStruct = $roleService->newPolicyCreateStruct(
                $policyData['module'],
                $policyData['function']
            );

            if (isset($policyData['limitations'])) {
                foreach ($policyData['limitations'] as $limitation) {
                    $policyCreateStruct->addLimitation($limitation);
                }
            }

            $roleCreateStruct->addPolicy($policyCreateStruct);
        }

        $roleDraft = $roleService->createRole($roleCreateStruct);

        $roleService->publishRoleDraft($roleDraft);

        return $roleService->loadRole($roleDraft->id);
    }

    /**
     * @param array<array<array-key, array<string|array<\eZ\Publish\API\Repository\Values\User\Limitation>>>> $policiesData
     *
     * @throws \eZ\Publish\API\Repository\Exceptions\LimitationValidationException
     * @throws \eZ\Publish\API\Repository\Exceptions\NotFoundException
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     * @throws \eZ\Publish\API\Repository\Exceptions\UnauthorizedException
     * @throws \eZ\Publish\API\Repository\Exceptions\ContentFieldValidationException
     * @throws \eZ\Publish\API\Repository\Exceptions\ContentValidationException
     */
    public function createUserWithPolicies(string $login, array $policiesData): User
    {
        $roleService = self::getRoleService();
        $userService = self::getUserService();

        $userCreateStruct = $userService->newUserCreateStruct(
            $login,
            "{$login}@test.dev",
            $login,
            'eng-GB'
        );

        $userCreateStruct->setField('first_name', $login);
        $userCreateStruct->setField('last_name', $login);
        $user = $userService->createUser($userCreateStruct, []);

        $role = $this->createRoleWithPolicies(
            uniqid('role_for_' . $login . '_', true),
            $policiesData
        );
        $roleService->assignRoleToUser($role, $user);

        return $user;
    }
}
