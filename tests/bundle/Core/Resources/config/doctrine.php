<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Ibexa\DoctrineSchema\Database\DbPlatform\PostgreSqlDbPlatform;
use Ibexa\DoctrineSchema\Database\DbPlatform\SqliteDbPlatform;
use RuntimeException;

return static function (ContainerConfigurator $container): void {
    if (!isset($_ENV['DATABASE_URL'])) {
        $_ENV['DATABASE_URL'] = 'sqlite://:memory:';
    }

    $platformsMap = [
        'sqlite' => SqliteDbPlatform::class,
        'postgres' => PostgreSqlDbPlatform::class,
        'postgresql' => PostgreSqlDbPlatform::class,
        'pgsql' => PostgreSqlDbPlatform::class,
    ];

    $scheme = parse_url($_ENV['DATABASE_URL'], PHP_URL_SCHEME);
    if (!is_string($scheme)) {
        throw new RuntimeException(sprintf(
            'Failed parsing "%s". Unable to determine scheme.',
            $_ENV['DATABASE_URL'],
        ));
    }

    $platform = $platformsMap[$scheme] ?? null;

    $container->extension('doctrine', [
        'dbal' => [
            'url' => '%env(DATABASE_URL)%',
            'logging' => false,
            'platform_service' => $platform,
        ],
    ]);
};
