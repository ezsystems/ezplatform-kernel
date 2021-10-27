<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Core\Persistence\Variation;

use Ibexa\Contracts\Core\Repository\Values\Content\Field;
use Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo;
use Ibexa\Contracts\Core\Variation\Values\Variation;
use Ibexa\Contracts\Core\Variation\VariationHandler;

class InMemoryVariationHandler implements VariationHandler
{
    public function getVariation(
        Field $field,
        VersionInfo $versionInfo,
        $variationName,
        array $parameters = []
    ) {
        return new Variation([
            'uri' => $field->value . '-in-memory-test',
        ]);
    }
}

class_alias(InMemoryVariationHandler::class, 'eZ\Publish\SPI\Tests\Variation\InMemoryVariationHandler');
