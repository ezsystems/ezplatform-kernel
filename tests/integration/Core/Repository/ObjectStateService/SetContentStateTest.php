<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Core\Repository\ObjectStateService;

use eZ\Publish\API\Repository\Exceptions\UnauthorizedException;
use eZ\Publish\API\Repository\Values\User\Limitation\ObjectStateLimitation;
use eZ\Publish\API\Repository\Values\User\Limitation\SubtreeLimitation;
use Ibexa\Tests\Integration\Core\RepositoryTestCase;

/**
 * @covers \eZ\Publish\API\Repository\ObjectStateService
 */
final class SetContentStateTest extends RepositoryTestCase
{
    /**
     * @dataProvider dataProviderForTestSetContentObjectStateWithSubtreeLimitation
     */
    public function testSetContentObjectStateWithSubtreeLimitation(
        ?string $subtreeLimitationValue,
        bool $isInsideLimitation
    ): void {
        $permissionResolver = self::getPermissionResolver();
        $objectStateService = self::getObjectStateService();

        $objectState = $objectStateService->loadObjectState(2);

        $subtreeLimitationFolder = $this->createFolder(['eng-GB' => 'Subtree limitation type'], 2);
        $contentInfo = $subtreeLimitationFolder->getVersionInfo()->getContentInfo();
        $mainLocation = $contentInfo->getMainLocation();

        $limitations = [
            new SubtreeLimitation(
                [
                    'limitationValues' => [$subtreeLimitationValue ?? $mainLocation->getPathString()],
                ],
            ),
            new ObjectStateLimitation(
                [
                    'limitationValues' => [1, 2],
                ],
            ),
        ];

        $user = $this->createUserWithPolicies(
            'object_state_user',
            [
                ['module' => 'content', 'function' => '*'],
                ['module' => 'state', 'function' => 'assign', 'limitations' => $limitations],
            ]
        );

        $permissionResolver->setCurrentUserReference($user);

        $childFolder = $this->createFolder(['eng-GB' => 'Child folder'], $mainLocation->id);
        $childContentInfo = $childFolder->getVersionInfo()->getContentInfo();

        if (!$isInsideLimitation) {
            self::expectException(UnauthorizedException::class);
        }

        $objectStateService->setContentState(
            $childContentInfo,
            $objectState->getObjectStateGroup(),
            $objectState,
            $childContentInfo->getMainLocation(),
        );

        $contentState = $objectStateService->getContentState($childContentInfo, $objectState->getObjectStateGroup());

        self::assertSame($objectState->identifier, $contentState->identifier);
    }

    public function dataProviderForTestSetContentObjectStateWithSubtreeLimitation(): iterable
    {
        yield 'inside subtree limitation' => [
            null,
            true,
        ];

        yield 'outside limitation passes' => [
            '/1/43',
            false,
        ];
    }
}
