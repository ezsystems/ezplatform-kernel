<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use EzSystems\DoctrineSchema\Database\DbPlatform\SqliteDbPlatform;

return static function (ContainerConfigurator $container): void {
    $platform = null;
    if (substr(getenv('DATABASE_URL'), 0, strlen('sqlite://')) === 'sqlite://') {
        $platform = SqliteDbPlatform::class;
    }

    $container->extension('doctrine', [
        'dbal' => [
            'url' => '%env(DATABASE_URL)%',
            'logging' => false,
            'platform_service' => $platform,
        ],
    ]);
};
