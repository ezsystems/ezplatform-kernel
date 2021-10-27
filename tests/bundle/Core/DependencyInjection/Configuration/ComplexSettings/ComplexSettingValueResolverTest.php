<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Bundle\Core\DependencyInjection\Configuration\ComplexSettings;

use Ibexa\Bundle\Core\DependencyInjection\Configuration\ComplexSettings\ComplexSettingValueResolver;
use PHPUnit\Framework\TestCase;

class ComplexSettingValueResolverTest extends TestCase
{
    public function testGetArgumentValue()
    {
        $resolver = new ComplexSettingValueResolver();
        self::assertEquals(
            '/mnt/nfs/var/ezdemo_site/storage',
            $resolver->resolveSetting(
                '/mnt/nfs/$var_dir$/$storage_dir$',
                'var_dir',
                'var/ezdemo_site',
                'storage_dir',
                'storage'
            )
        );
    }
}

class_alias(ComplexSettingValueResolverTest::class, 'eZ\Bundle\EzPublishCoreBundle\Tests\DependencyInjection\Configuration\ComplexSettings\ComplexSettingValueResolverTest');
