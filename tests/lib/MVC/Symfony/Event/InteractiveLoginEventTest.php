<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Core\MVC\Symfony\Event;

use Ibexa\Contracts\Core\Repository\Values\User\User;
use Ibexa\Core\MVC\Symfony\Event\InteractiveLoginEvent;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class InteractiveLoginEventTest extends TestCase
{
    public function testGetSetAPIUser()
    {
        $event = new InteractiveLoginEvent(new Request(), $this->createMock(TokenInterface::class));
        $this->assertFalse($event->hasAPIUser());
        $apiUser = $this->createMock(User::class);
        $event->setApiUser($apiUser);
        $this->assertTrue($event->hasAPIUser());
        $this->assertSame($apiUser, $event->getAPIUser());
    }
}

class_alias(InteractiveLoginEventTest::class, 'eZ\Publish\Core\MVC\Symfony\Event\Tests\InteractiveLoginEventTest');
