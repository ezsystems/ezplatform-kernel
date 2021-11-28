<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Bundle\Core\Imagine;

use Ibexa\Bundle\Core\Imagine\AliasCleaner;
use Liip\ImagineBundle\Imagine\Cache\Resolver\ResolverInterface;
use PHPUnit\Framework\TestCase;

class AliasCleanerTest extends TestCase
{
    /** @var \Ibexa\Bundle\Core\Imagine\AliasCleaner */
    private $aliasCleaner;

    /** @var \PHPUnit\Framework\MockObject\MockObject */
    private $resolver;

    protected function setUp(): void
    {
        parent::setUp();
        $this->resolver = $this->createMock(ResolverInterface::class);
        $this->aliasCleaner = new AliasCleaner($this->resolver);
    }

    public function testRemoveAliases()
    {
        $originalPath = 'foo/bar/test.jpg';
        $this->resolver
            ->expects($this->once())
            ->method('remove')
            ->with([$originalPath], []);

        $this->aliasCleaner->removeAliases($originalPath);
    }
}

class_alias(AliasCleanerTest::class, 'eZ\Bundle\EzPublishCoreBundle\Tests\Imagine\AliasCleanerTest');
