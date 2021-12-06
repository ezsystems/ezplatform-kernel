<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Test\Persistence\Fixture;

/**
 * Data fixture stored in PHP file which returns it as an array.
 *
 * @internal for internal use by Repository test setup
 */
final class PhpArrayFileFixture extends BaseInMemoryCachedFileFixture
{
    protected function loadFixture(): array
    {
        return require $this->getFilePath();
    }
}

class_alias(PhpArrayFileFixture::class, 'eZ\Publish\SPI\Tests\Persistence\PhpArrayFileFixture');
