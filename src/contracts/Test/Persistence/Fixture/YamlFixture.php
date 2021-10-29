<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Test\Persistence\Fixture;

use Symfony\Component\Yaml\Yaml;

/**
 * Data fixture stored in Yaml file.
 *
 * @internal for internal use by Repository test setup
 */
final class YamlFixture extends BaseInMemoryCachedFileFixture
{
    protected function loadFixture(): array
    {
        return Yaml::parseFile($this->getFilePath());
    }
}

class_alias(YamlFixture::class, 'eZ\Publish\SPI\Tests\Persistence\YamlFixture');
