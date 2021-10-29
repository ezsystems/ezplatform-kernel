<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\FieldType\BinaryBase;

use Ibexa\Contracts\Core\Persistence\Content\Field;
use Ibexa\Contracts\Core\Persistence\Content\VersionInfo;

/**
 * A variant of PathGenerator that uses Symfony routes for generating URIs.
 */
interface RouteAwarePathGenerator extends PathGeneratorInterface
{
    public function getRoute(Field $field, VersionInfo $versionInfo): string;

    public function getParameters(Field $field, VersionInfo $versionInfo): array;

    public function generate(string $route, array $parameters = []): string;
}

class_alias(RouteAwarePathGenerator::class, 'eZ\Publish\SPI\FieldType\BinaryBase\RouteAwarePathGenerator');
