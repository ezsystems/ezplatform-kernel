<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Test\Persistence;

/**
 * Represents database fixture.
 *
 * @internal for internal use by Repository test setup
 */
interface Fixture
{
    /**
     * Load database fixture into a map of table names to table rows data.
     *
     * @return array
     */
    public function load(): array;
}

class_alias(Fixture::class, 'eZ\Publish\SPI\Tests\Persistence\Fixture');
