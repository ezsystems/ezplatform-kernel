<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Core\MVC\Symfony\Security;

use DateTimeImmutable;
use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Contracts\Core\Repository\Values\User\PasswordInfo;
use Ibexa\Core\MVC\Symfony\Security\User;
use Ibexa\Core\MVC\Symfony\Security\UserChecker;
use Ibexa\Core\Repository\Values\Content\Content;
use Ibexa\Core\Repository\Values\Content\VersionInfo;
use Ibexa\Core\Repository\Values\User\User as APIUser;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Exception\CredentialsExpiredException;
use Symfony\Component\Security\Core\Exception\DisabledException;
use Throwable;

final class UserCheckerTest extends TestCase
{
    private const EXAMPLE_USER_ID = 14;

    /** @var \PHPUnit\Framework\MockObject\MockObject */
    private $userServiceMock;

    /** @var \Ibexa\Core\MVC\Symfony\Security\UserChecker */
    private $userChecker;

    protected function setUp(): void
    {
        $this->userServiceMock = $this->createMock(UserService::class);
        $this->userChecker = new UserChecker($this->userServiceMock);
    }

    public function testCheckPreAuthWithEnabledUser(): void
    {
        $apiUser = new APIUser(
            [
                'content' => new Content(
                    [
                        'versionInfo' => new VersionInfo(
                            [
                                'contentInfo' => new ContentInfo(
                                    [
                                        'id' => self::EXAMPLE_USER_ID,
                                    ]
                                ),
                            ]
                        ),
                    ]
                ),
                'enabled' => true,
            ]
        );

        try {
            $this->userChecker->checkPreAuth(new User($apiUser));
        } catch (Throwable $t) {
            self::fail('Error was not expected to be raised.');
        }
    }

    public function testCheckPreAuthWithDisabledUser(): void
    {
        $apiUser = new APIUser(
            [
                'content' => new Content(
                    [
                        'versionInfo' => new VersionInfo(
                            [
                                'contentInfo' => new ContentInfo(
                                    [
                                        'id' => self::EXAMPLE_USER_ID,
                                    ]
                                ),
                            ]
                        ),
                    ]
                ),
                'enabled' => false,
            ]
        );

        $this->expectException(DisabledException::class);
        $this->expectExceptionMessage('User account is locked.');

        $this->userChecker->checkPreAuth(new User($apiUser));
    }

    public function testCheckPostAuthWithNonExpiredUser(): void
    {
        $apiUser = new APIUser(
            [
                'content' => new Content(
                    [
                        'versionInfo' => new VersionInfo(
                            [
                                'contentInfo' => new ContentInfo(
                                    [
                                        'id' => self::EXAMPLE_USER_ID,
                                    ]
                                ),
                            ]
                        ),
                    ]
                ),
            ]
        );

        $this->userServiceMock
            ->expects(self::once())
            ->method('getPasswordInfo')
            ->with(self::identicalTo($apiUser))
            ->willReturn(new PasswordInfo());

        try {
            $this->userChecker->checkPostAuth(new User($apiUser));
        } catch (Throwable $t) {
            self::fail('Error was not expected to be raised.');
        }
    }

    public function testCheckPostAuthWithExpiredUser(): void
    {
        $apiUser = new APIUser(
            [
                'content' => new Content(
                    [
                        'versionInfo' => new VersionInfo(
                            [
                                'contentInfo' => new ContentInfo(
                                    [
                                        'id' => self::EXAMPLE_USER_ID,
                                    ]
                                ),
                            ]
                        ),
                    ]
                ),
            ]
        );

        $this->userServiceMock
            ->expects(self::once())
            ->method('getPasswordInfo')
            ->with(self::identicalTo($apiUser))
            ->willReturn(
                new PasswordInfo(
                    DateTimeImmutable::createFromFormat('Y-m-d', '2019-01-01')
                )
            );

        $this->expectException(CredentialsExpiredException::class);
        $this->expectExceptionMessage('User account has expired.');

        $this->userChecker->checkPostAuth(new User($apiUser));
    }
}

class_alias(UserCheckerTest::class, 'eZ\Publish\Core\MVC\Symfony\Security\Tests\UserCheckerTest');
