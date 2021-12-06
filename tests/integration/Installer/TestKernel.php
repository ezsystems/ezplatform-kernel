<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Installer;

use EzSystems\DoctrineSchemaBundle\DoctrineSchemaBundle;
use EzSystems\PlatformInstallerBundle\EzSystemsPlatformInstallerBundle;
use EzSystems\PlatformInstallerBundle\Installer\CoreInstaller;
use Ibexa\Contracts\Core\Test\IbexaTestKernel;

final class TestKernel extends IbexaTestKernel
{
    public function registerBundles(): iterable
    {
        yield from parent::registerBundles();

        yield new DoctrineSchemaBundle();
        yield new EzSystemsPlatformInstallerBundle();
    }

    protected static function getExposedServicesByClass(): iterable
    {
        yield from parent::getExposedServicesByClass();

        yield CoreInstaller::class;
    }
}
