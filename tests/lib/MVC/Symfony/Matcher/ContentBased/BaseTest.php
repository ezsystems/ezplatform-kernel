<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Core\MVC\Symfony\Matcher\ContentBased;

use Ibexa\Contracts\Core\Persistence\User\Handler as SPIUserHandler;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Core\MVC\ConfigResolverInterface;
use Ibexa\Core\MVC\Symfony\View\Provider\Configured;
use Ibexa\Core\Repository\Mapper\RoleDomainMapper;
use Ibexa\Core\Repository\Permission\LimitationService;
use Ibexa\Core\Repository\Permission\PermissionResolver;
use Ibexa\Core\Repository\Repository;
use PHPUnit\Framework\TestCase;

abstract class BaseTest extends TestCase
{
    /** @var \PHPUnit\Framework\MockObject\MockObject */
    protected $repositoryMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repositoryMock = $this->getRepositoryMock();
    }

    /**
     * @param array $matchingConfig
     *
     * @return \PHPUnit\Framework\MockObject\MockObject
     */
    protected function getPartiallyMockedViewProvider(array $matchingConfig = [])
    {
        return $this
            ->getMockBuilder(Configured::class)
            ->setConstructorArgs(
                [
                    $this->repositoryMock,
                    $matchingConfig,
                ]
            )
            ->setMethods(['getMatcher'])
            ->getMock();
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject
     */
    protected function getRepositoryMock()
    {
        $repositoryClass = Repository::class;

        return $this
            ->getMockBuilder($repositoryClass)
            ->disableOriginalConstructor()
            ->setMethods(
                array_diff(
                    get_class_methods($repositoryClass),
                    ['sudo']
                )
            )
            ->getMock();
    }

    /**
     * @param array $properties
     *
     * @return \PHPUnit\Framework\MockObject\MockObject
     */
    protected function getLocationMock(array $properties = [])
    {
        return $this
            ->getMockBuilder(Location::class)
            ->setConstructorArgs([$properties])
            ->getMockForAbstractClass();
    }

    /**
     * @param array $properties
     *
     * @return \PHPUnit\Framework\MockObject\MockObject
     */
    protected function getContentInfoMock(array $properties = [])
    {
        return $this->
            getMockBuilder(ContentInfo::class)
            ->setConstructorArgs([$properties])
            ->getMockForAbstractClass();
    }

    protected function getPermissionResolverMock()
    {
        $configResolverMock = $this->createMock(ConfigResolverInterface::class);
        $configResolverMock
            ->method('getParameter')
            ->with('anonymous_user_id')
            ->willReturn(10);

        return $this
            ->getMockBuilder(PermissionResolver::class)
            ->setMethods(null)
            ->setConstructorArgs(
                [
                    $this->createMock(RoleDomainMapper::class),
                    $this->createMock(LimitationService::class),
                    $this->createMock(SPIUserHandler::class),
                    $configResolverMock,
                    [],
                ]
            )
            ->getMock();
    }
}

class_alias(BaseTest::class, 'eZ\Publish\Core\MVC\Symfony\Matcher\Tests\ContentBased\BaseTest');
