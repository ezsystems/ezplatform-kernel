<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Core\MVC\Symfony\Security\Voter;

use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Core\MVC\Symfony\Controller\Content\ViewController;
use Ibexa\Core\MVC\Symfony\Security\Authorization\Attribute;
use Ibexa\Core\MVC\Symfony\Security\Authorization\Voter\CoreVoter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class CoreVoterTest extends TestCase
{
    /** @var \Ibexa\Contracts\Core\Repository\PermissionResolver|\PHPUnit\Framework\MockObject\MockObject */
    private $permissionResolver;

    protected function setUp(): void
    {
        parent::setUp();
        $this->permissionResolver = $this->createMock(PermissionResolver::class);
    }

    /**
     * @dataProvider supportsAttributeProvider
     */
    public function testSupportsAttribute($attribute, $expectedResult)
    {
        $voter = new CoreVoter($this->permissionResolver);
        $this->assertSame($expectedResult, $voter->supportsAttribute($attribute));
    }

    public function supportsAttributeProvider()
    {
        return [
            ['foo', false],
            [new Attribute('foo', 'bar'), true],
            [new Attribute('foo', 'bar', ['some' => 'thing']), false],
            [new \stdClass(), false],
            [['foo'], false],
            [
                new Attribute(
                    'foo',
                    'bar',
                    ['valueObject' => $this->getMockForAbstractClass(ValueObject::class)]
                ),
                false,
            ],
        ];
    }

    /**
     * @dataProvider supportsClassProvider
     */
    public function testSupportsClass($class)
    {
        $voter = new CoreVoter($this->permissionResolver);
        $this->assertTrue($voter->supportsClass($class));
    }

    public function supportsClassProvider()
    {
        return [
            ['foo'],
            ['bar'],
            [ValueObject::class],
            [ViewController::class],
        ];
    }

    /**
     * @dataProvider voteInvalidAttributeProvider
     */
    public function testVoteInvalidAttribute(array $attributes)
    {
        $voter = new CoreVoter($this->permissionResolver);
        $this->assertSame(
            VoterInterface::ACCESS_ABSTAIN,
            $voter->vote(
                $this->createMock(TokenInterface::class),
                new \stdClass(),
                $attributes
            )
        );
    }

    public function voteInvalidAttributeProvider()
    {
        return [
            [[]],
            [['foo']],
            [['foo', 'bar', ['some' => 'thing']]],
            [[new \stdClass()]],
            [
                [
                    new Attribute(
                        'foo',
                        'bar',
                        ['valueObject' => $this->getMockForAbstractClass(ValueObject::class)]
                    ),
                ],
                false,
            ],
        ];
    }

    /**
     * @dataProvider voteProvider
     */
    public function testVote(Attribute $attribute, $repositoryCanUser, $expectedResult)
    {
        $voter = new CoreVoter($this->permissionResolver);
        if ($repositoryCanUser !== null) {
            $this->permissionResolver
                ->expects($this->once())
                ->method('hasAccess')
                ->with($attribute->module, $attribute->function)
                ->will($this->returnValue($repositoryCanUser));
        } else {
            $this->permissionResolver
                ->expects($this->never())
                ->method('hasAccess');
        }

        $this->assertSame(
            $expectedResult,
            $voter->vote(
                $this->createMock(TokenInterface::class),
                new \stdClass(),
                [$attribute]
            )
        );
    }

    public function voteProvider()
    {
        return [
            [
                new Attribute('content', 'read'),
                true,
                VoterInterface::ACCESS_GRANTED,
            ],
            [
                new Attribute('foo', 'bar'),
                true,
                VoterInterface::ACCESS_GRANTED,
            ],
            [
                new Attribute('content', 'read'),
                false,
                VoterInterface::ACCESS_DENIED,
            ],
            [
                new Attribute('some', 'thing'),
                false,
                VoterInterface::ACCESS_DENIED,
            ],
            [
                new Attribute(
                    'content',
                    'read',
                    [
                        'valueObject' => $this->getMockForAbstractClass(ValueObject::class),
                        'targets' => $this->getMockForAbstractClass(ValueObject::class),
                    ]
                ),
                null,
                VoterInterface::ACCESS_ABSTAIN,
            ],
            [
                new Attribute(
                    'content',
                    'read',
                    [
                        'valueObject' => $this->getMockForAbstractClass(ValueObject::class),
                        'targets' => [$this->getMockForAbstractClass(ValueObject::class)],
                    ]
                ),
                null,
                VoterInterface::ACCESS_ABSTAIN,
            ],
            [
                new Attribute(
                    'content',
                    'read',
                    [
                        'valueObject' => $this->getMockForAbstractClass(ValueObject::class),
                        'targets' => $this->getMockForAbstractClass(ValueObject::class),
                    ]
                ),
                null,
                VoterInterface::ACCESS_ABSTAIN,
            ],
            [
                new Attribute(
                    'content',
                    'read',
                    [
                        'valueObject' => $this->getMockForAbstractClass(ValueObject::class),
                        'targets' => [$this->getMockForAbstractClass(ValueObject::class)],
                    ]
                ),
                null,
                VoterInterface::ACCESS_ABSTAIN,
            ],
        ];
    }
}

class_alias(CoreVoterTest::class, 'eZ\Publish\Core\MVC\Symfony\Security\Tests\Voter\CoreVoterTest');
