<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Core\SiteAccess\Config;

use Ibexa\Bundle\Core\SiteAccess\Config\ComplexConfigProcessor;
use Ibexa\Bundle\Core\SiteAccess\Config\IOConfigResolver;
use Ibexa\Core\MVC\ConfigResolverInterface;
use Ibexa\Core\MVC\Symfony\SiteAccess;
use Ibexa\Core\MVC\Symfony\SiteAccess\SiteAccessService;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ibexa\Bundle\Core\SiteAccess\Config\IOConfigResolver
 */
class IOConfigResolverTest extends TestCase
{
    private const DEFAULT_NAMESPACE = 'ezsettings';

    /** @var \Ibexa\Core\MVC\ConfigResolverInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $configResolver;

    /** @var \Ibexa\Core\MVC\Symfony\SiteAccess\SiteAccessService|\PHPUnit\Framework\MockObject\MockObject */
    private $siteAccessService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->configResolver = $this->createMock(ConfigResolverInterface::class);
        $this->siteAccessService = $this->createMock(SiteAccessService::class);
    }

    public function testGetUrlPrefix(): void
    {
        $this->siteAccessService
            ->method('getCurrent')
            ->willReturn(new SiteAccess('ezdemo_site'));

        $this->configResolver
            ->method('hasParameter')
            ->with('io.url_prefix', null, 'ezdemo_site')
            ->willReturn(true);
        $this->configResolver
            ->method('getParameter')
            ->willReturnMap([
                ['io.url_prefix', null, 'ezdemo_site', '$var_dir$/ezdemo_site/$storage_dir$'],
                ['var_dir', self::DEFAULT_NAMESPACE, 'ezdemo_site', 'var'],
                ['storage_dir', self::DEFAULT_NAMESPACE, 'ezdemo_site', 'storage'],
            ]);

        $complexConfigProcessor = new ComplexConfigProcessor(
            $this->configResolver,
            $this->siteAccessService
        );

        $ioConfigResolver = new IOConfigResolver(
            $complexConfigProcessor
        );

        $this->assertEquals('var/ezdemo_site/storage', $ioConfigResolver->getUrlPrefix());
    }

    public function testGetLegacyUrlPrefix(): void
    {
        $this->siteAccessService
            ->method('getCurrent')
            ->willReturn(new SiteAccess('ezdemo_site'));

        $this->configResolver
            ->method('hasParameter')
            ->with('io.legacy_url_prefix', null, 'ezdemo_site')
            ->willReturn(true);
        $this->configResolver
            ->method('getParameter')
            ->willReturnMap([
                ['io.legacy_url_prefix', null, 'ezdemo_site', '$var_dir$/ezdemo_site/$storage_dir$'],
                ['var_dir', self::DEFAULT_NAMESPACE, 'ezdemo_site', 'var'],
                ['storage_dir', self::DEFAULT_NAMESPACE, 'ezdemo_site', 'legacy_storage'],
            ]);

        $complexConfigProcessor = new ComplexConfigProcessor(
            $this->configResolver,
            $this->siteAccessService
        );

        $ioConfigResolver = new IOConfigResolver(
            $complexConfigProcessor
        );

        $this->assertEquals('var/ezdemo_site/legacy_storage', $ioConfigResolver->getLegacyUrlPrefix());
    }

    public function testGetRootDir(): void
    {
        $this->siteAccessService
            ->method('getCurrent')
            ->willReturn(new SiteAccess('ezdemo_site'));

        $this->configResolver
            ->method('hasParameter')
            ->with('io.root_dir', null, 'ezdemo_site')
            ->willReturn(true);
        $this->configResolver
            ->method('getParameter')
            ->willReturnMap([
                ['io.root_dir', null, 'ezdemo_site', '/path/to/ezpublish/web/$var_dir$/ezdemo_site/$storage_dir$'],
                ['var_dir', self::DEFAULT_NAMESPACE, 'ezdemo_site', 'var'],
                ['storage_dir', self::DEFAULT_NAMESPACE, 'ezdemo_site', 'legacy_storage'],
            ]);

        $complexConfigProcessor = new ComplexConfigProcessor(
            $this->configResolver,
            $this->siteAccessService
        );

        $ioConfigResolver = new IOConfigResolver(
            $complexConfigProcessor
        );

        $this->assertEquals('/path/to/ezpublish/web/var/ezdemo_site/legacy_storage', $ioConfigResolver->getRootDir());
    }
}

class_alias(IOConfigResolverTest::class, 'eZ\Bundle\EzPublishCoreBundle\Tests\SiteAccess\Config\IOConfigResolverTest');
