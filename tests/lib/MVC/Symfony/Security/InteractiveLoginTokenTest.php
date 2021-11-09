<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Core\MVC\Symfony\Security;

use Ibexa\Core\MVC\Symfony\Security\InteractiveLoginToken;
use Ibexa\Core\MVC\Symfony\Security\UserInterface;
use PHPUnit\Framework\TestCase;

class InteractiveLoginTokenTest extends TestCase
{
    public function testConstruct()
    {
        $user = $this->createMock(UserInterface::class);
        $originalTokenType = 'FooBar';
        $credentials = 'my_credentials';
        $providerKey = 'key';
        $roles = ['ROLE_USER', 'ROLE_TEST', 'ROLE_FOO'];
        $expectedRoles = [];
        foreach ($roles as $role) {
            if (is_string($role)) {
                $expectedRoles[] = $role;
            } else {
                $expectedRoles[] = $role;
            }
        }

        $token = new InteractiveLoginToken($user, $originalTokenType, $credentials, $providerKey, $roles);
        $this->assertSame($user, $token->getUser());
        $this->assertTrue($token->isAuthenticated());
        $this->assertSame($originalTokenType, $token->getOriginalTokenType());
        $this->assertSame($credentials, $token->getCredentials());
        $this->assertSame($providerKey, $token->getProviderKey());
        $this->assertEquals($expectedRoles, $token->getRoleNames());
    }

    public function testSerialize()
    {
        $user = $this->createMock(UserInterface::class);
        $originalTokenType = 'FooBar';
        $credentials = 'my_credentials';
        $providerKey = 'key';
        $roles = ['ROLE_USER', 'ROLE_TEST', 'ROLE_FOO'];

        $token = new InteractiveLoginToken($user, $originalTokenType, $credentials, $providerKey, $roles);
        $serialized = serialize($token);
        $unserializedToken = unserialize($serialized);
        $this->assertEquals($token, $unserializedToken);
    }
}

class_alias(InteractiveLoginTokenTest::class, 'eZ\Publish\Core\MVC\Symfony\Security\Tests\InteractiveLoginTokenTest');
