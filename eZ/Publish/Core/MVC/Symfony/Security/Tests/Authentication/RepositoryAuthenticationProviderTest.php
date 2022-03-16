<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace eZ\Publish\Core\MVC\Symfony\Security\Tests\Authentication;

use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Compiler\SecurityPass;
use eZ\Publish\API\Repository\Exceptions\PasswordInUnsupportedFormatException;
use eZ\Publish\API\Repository\PermissionResolver;
use eZ\Publish\API\Repository\UserService;
use eZ\Publish\API\Repository\Values\User\User as APIUser;
use eZ\Publish\Core\MVC\Symfony\Security\Authentication\RepositoryAuthenticationProvider;
use eZ\Publish\Core\MVC\Symfony\Security\User;
use eZ\Publish\Core\Repository\User\Exception\UnsupportedPasswordHashType;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Stopwatch\Stopwatch;

class RepositoryAuthenticationProviderTest extends TestCase
{
    private const UNSUPPORTED_USER_PASSWORD_HASH_TYPE = 5;

    /** @var \PHPUnit\Framework\MockObject\MockObject|\Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface */
    private $encoderFactory;

    /** @var \eZ\Publish\Core\MVC\Symfony\Security\Authentication\RepositoryAuthenticationProvider */
    private $authProvider;

    /** @var \eZ\Publish\API\Repository\PermissionResolver|\PHPUnit\Framework\MockObject\MockObject */
    private $permissionResolver;

    /**
     * @var \Symfony\Component\Security\Core\User\UserProviderInterface
     */
    private $userProvider;

    /** @var \eZ\Publish\API\Repository\UserService|\PHPUnit\Framework\MockObject\MockObject */
    private $userService;

    /** @var \Psr\Log\LoggerInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $logger;

    protected function setUp(): void
    {
        parent::setUp();
        $this->encoderFactory = $this->createMock(EncoderFactoryInterface::class);
        $this->userProvider = $this->createMock(UserProviderInterface::class);
        $this->authProvider = new RepositoryAuthenticationProvider(
            $this->userProvider,
            $this->createMock(UserCheckerInterface::class),
            'foo',
            $this->encoderFactory
        );
        $this->permissionResolver = $this->createMock(PermissionResolver::class);
        $this->userService = $this->createMock(UserService::class);
        $this->authProvider->setPermissionResolver($this->permissionResolver);
        $this->authProvider->setUserService($this->userService);

        $this->logger = $this->createMock(LoggerInterface::class);
        $this->authProvider->setLogger($this->logger);
    }

    public function testAuthenticationNotEzUser()
    {
        $password = 'some_encoded_password';
        $user = $this->createMock(UserInterface::class);
        $user
            ->expects($this->any())
            ->method('getPassword')
            ->will($this->returnValue($password));

        $tokenUser = $this->createMock(UserInterface::class);
        $tokenUser
            ->expects($this->any())
            ->method('getPassword')
            ->will($this->returnValue($password));
        $token = new UsernamePasswordToken($tokenUser, 'foo', 'bar');

        $method = new \ReflectionMethod($this->authProvider, 'checkAuthentication');
        $method->setAccessible(true);
        $method->invoke($this->authProvider, $user, $token);
    }

    public function testCheckAuthenticationCredentialsChanged()
    {
        $this->expectException(\Symfony\Component\Security\Core\Exception\BadCredentialsException::class);

        $apiUser = $this->getMockBuilder(APIUser::class)
            ->setConstructorArgs([['passwordHash' => 'some_encoded_password']])
            ->setMethods(['getUserId'])
            ->getMockForAbstractClass();
        $apiUser
            ->expects($this->once())
            ->method('getUserId')
            ->will($this->returnValue(456));
        $tokenUser = new User($apiUser);
        $token = new UsernamePasswordToken($tokenUser, 'foo', 'bar');

        $renewedApiUser = $this->getMockBuilder(APIUser::class)
            ->setConstructorArgs([['passwordHash' => 'renewed_encoded_password']])
            ->getMockForAbstractClass();

        $user = $this->createMock(User::class);
        $user
            ->expects($this->any())
            ->method('getAPIUser')
            ->will($this->returnValue($renewedApiUser));

        $method = new \ReflectionMethod($this->authProvider, 'checkAuthentication');
        $method->setAccessible(true);
        $method->invoke($this->authProvider, $user, $token);
    }

    public function testCheckAuthenticationAlreadyLoggedIn()
    {
        $password = 'encoded_password';

        $apiUser = $this->getMockBuilder(APIUser::class)
            ->setConstructorArgs([['passwordHash' => $password]])
            ->setMethods(['getUserId'])
            ->getMockForAbstractClass();
        $tokenUser = new User($apiUser);
        $token = new UsernamePasswordToken($tokenUser, 'foo', 'bar');

        $user = $this->createMock(User::class);
        $user
            ->expects($this->once())
            ->method('getAPIUser')
            ->will($this->returnValue($apiUser));

        $this->permissionResolver
            ->expects($this->once())
            ->method('setCurrentUserReference')
            ->with($apiUser);

        $method = new \ReflectionMethod($this->authProvider, 'checkAuthentication');
        $method->setAccessible(true);
        $method->invoke($this->authProvider, $user, $token);
    }

    public function testCheckAuthenticationFailed()
    {
        $this->expectException(\Symfony\Component\Security\Core\Exception\BadCredentialsException::class);

        $apiUser = $this->createMock(APIUser::class);
        $user = $this->createMock(User::class);
        $user->method('getAPIUser')
            ->willReturn($apiUser);
        $userName = 'my_username';
        $password = 'foo';
        $token = new UsernamePasswordToken($userName, $password, 'bar');

        $this->userService
            ->expects($this->once())
            ->method('checkUserCredentials')
            ->with($apiUser, $password)
            ->willReturn(false);

        $method = new \ReflectionMethod($this->authProvider, 'checkAuthentication');
        $method->setAccessible(true);
        $method->invoke($this->authProvider, $user, $token);
    }

    public function testCheckAuthentication()
    {
        $userName = 'my_username';
        $password = 'foo';
        $token = new UsernamePasswordToken($userName, $password, 'bar');

        $apiUser = $this->createMock(APIUser::class);
        $user = $this->createMock(User::class);
        $user->method('getAPIUser')
            ->willReturn($apiUser);

        $this->userService
            ->expects($this->once())
            ->method('checkUserCredentials')
            ->with($apiUser, $password)
            ->willReturn(true);

        $this->permissionResolver
            ->expects($this->once())
            ->method('setCurrentUserReference')
            ->with($apiUser);

        $method = new \ReflectionMethod($this->authProvider, 'checkAuthentication');
        $method->setAccessible(true);
        $method->invoke($this->authProvider, $user, $token);
    }

    public function testCheckAuthenticationFailedWhenPasswordInUnsupportedFormat()
    {
        $this->expectException(PasswordInUnsupportedFormatException::class);

        $apiUser = $this->createMock(APIUser::class);
        $user = $this->createMock(User::class);
        $user->method('getAPIUser')
            ->willReturn($apiUser);
        $userName = 'my_username';
        $password = 'foo';
        $token = new UsernamePasswordToken($userName, $password, 'foo');
        $this->userProvider
            ->method('loadUserByUsername')
            ->willReturn($user);

        $this->userService
            ->expects($this->once())
            ->method('checkUserCredentials')
            ->with($apiUser, $password)
            ->willThrowException(new UnsupportedPasswordHashType(self::UNSUPPORTED_USER_PASSWORD_HASH_TYPE));

        $this->authProvider->authenticate($token);
    }

    public function testAuthenticateInConstantTime(): void
    {
        $this->authProvider->setConstantAuthTime(SecurityPass::CONSTANT_AUTH_TIME_DEFAULT); // a reasonable value

        $token = new UsernamePasswordToken('my_username', 'my_password', 'bar');

        $stopwatch = new Stopwatch();
        $stopwatch->start('authenticate_constant_time_test');

        try {
            $this->authProvider->authenticate($token);
        } catch (\Exception $e) {
            // We don't care, we just need test execution to continue
        }

        $duration = $stopwatch->stop('authenticate_constant_time_test')->getDuration();
        self::assertGreaterThanOrEqual(SecurityPass::CONSTANT_AUTH_TIME_DEFAULT * 1000, $duration);
    }

    public function testAuthenticateWarningOnConstantTimeExceeded(): void
    {
        $this->authProvider->setConstantAuthTime(0.0000001); // much too short, but not zero, which would disable the check

        $token = new UsernamePasswordToken('my_username', 'my_password', 'bar');

        $this->logger
            ->expects(self::atLeastOnce())
            ->method('warning')
            ->with('Authentication took longer than the configured constant time. Consider increasing the value of ' . SecurityPass::CONSTANT_AUTH_TIME_SETTING);

        $this->expectException(AuthenticationException::class);
        $this->authProvider->authenticate($token);
    }

    public function testAuthenticateConstantTimeDisabled(): void
    {
        $this->authProvider->setConstantAuthTime(0.0); // zero disables the check

        $token = new UsernamePasswordToken('my_username', 'my_password', 'bar');

        $this->logger
            ->expects(self::never())
            ->method('warning');

        $this->expectException(AuthenticationException::class);
        $this->authProvider->authenticate($token);
    }
}
