<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Test\Persistence\Fixture;

use Ibexa\Contracts\Core\Test\Persistence\Fixture;
use RuntimeException;
use SplFileInfo;

/**
 * Factory building an instance of Fixture depending on a file type.
 *
 * @see \Ibexa\Contracts\Core\Test\Persistence\Fixture
 */
final class FileFixtureFactory
{
    public function buildFixture(string $filePath): Fixture
    {
        $fileInfo = new SplFileInfo($filePath);
        $extension = $fileInfo->getExtension();

        switch ($extension) {
            case 'yml':
            case 'yaml':
                return new YamlFixture($filePath);
            case 'php':
                return new PhpArrayFileFixture($filePath);
            default:
                throw new RuntimeException("Unsupported fixture file type: {$extension}");
        }
    }
}

class_alias(FileFixtureFactory::class, 'eZ\Publish\SPI\Tests\Persistence\FileFixtureFactory');
