<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Core\Repository\Limitation\PermissionResolver;

use Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo;
use Ibexa\Contracts\Core\Repository\Values\User\Limitation;

/**
 * Test mix of chosen core Content Limitations.
 */
class ContentLimitationsMixIntegrationTest extends BaseLimitationIntegrationTest
{
    public const LIMITATION_VALUES = 'limitationValues';

    /**
     * Provides lists of:.
     *
     * <code>[string $module, string $function, array $limitations, bool $expectedResult]</code>
     *
     * This provider also checks if all registered Limitations are used.
     */
    public function providerForCanUser(): array
    {
        $commonLimitations = $this->getCommonLimitations();
        $contentCreateLimitations = array_merge(
            $commonLimitations,
            [
                new Limitation\ParentContentTypeLimitation([self::LIMITATION_VALUES => [1]]),
                new Limitation\ParentDepthLimitation([self::LIMITATION_VALUES => [2]]),
                new Limitation\LanguageLimitation([self::LIMITATION_VALUES => ['eng-US']]),
            ]
        );

        $contentEditLimitations = array_merge(
            $commonLimitations,
            [
                new Limitation\ObjectStateLimitation(
                    [self::LIMITATION_VALUES => [1, 2]]
                ),
                new Limitation\LanguageLimitation([self::LIMITATION_VALUES => ['eng-US']]),
            ]
        );

        $contentVersionReadLimitations = array_merge(
            $commonLimitations,
            [
                new Limitation\StatusLimitation(
                    [self::LIMITATION_VALUES => [VersionInfo::STATUS_PUBLISHED]]
                ),
            ]
        );

        return [
            ['content', 'create', $contentCreateLimitations, true],
            ['content', 'edit', $contentEditLimitations, true],
            ['content', 'publish', $contentEditLimitations, true],
            ['content', 'versionread', $contentVersionReadLimitations, true],
        ];
    }

    /**
     * @dataProvider providerForCanUser
     *
     * @param string $module
     * @param string $function
     * @param array $limitations
     * @param bool $expectedResult
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\BadStateException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\ForbiddenException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function testCanUser(
        string $module,
        string $function,
        array $limitations,
        bool $expectedResult
    ): void {
        $repository = $this->getRepository();
        $locationService = $repository->getLocationService();

        $folder = $this->createFolder(['eng-US' => 'Folder'], 2);
        $location = $locationService->loadLocation($folder->contentInfo->mainLocationId);

        $this->loginAsEditorUserWithLimitations($module, $function, $limitations);

        $this->assertCanUser(
            $expectedResult,
            $module,
            $function,
            $limitations,
            $folder,
            [$location]
        );
    }

    /**
     * Get a list of Limitations common to all test cases.
     *
     * @return \Ibexa\Contracts\Core\Repository\Values\User\Limitation[]
     */
    private function getCommonLimitations(): array
    {
        return [
            new Limitation\ContentTypeLimitation([self::LIMITATION_VALUES => [1]]),
            new Limitation\SectionLimitation([self::LIMITATION_VALUES => [1]]),
            new Limitation\SubtreeLimitation([self::LIMITATION_VALUES => ['/1/2']]),
        ];
    }
}

class_alias(ContentLimitationsMixIntegrationTest::class, 'eZ\Publish\API\Repository\Tests\Limitation\PermissionResolver\ContentLimitationsMixIntegrationTest');
