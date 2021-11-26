<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Core\Persistence\Legacy\UserPreference;

use Ibexa\Contracts\Core\Persistence\UserPreference\UserPreference;
use Ibexa\Core\Persistence\Legacy\UserPreference\Mapper;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ibexa\Core\Persistence\Legacy\UserPreference\Mapper
 */
class MapperTest extends TestCase
{
    /** @var \Ibexa\Core\Persistence\Legacy\UserPreference\Mapper */
    private $mapper;

    protected function setUp(): void
    {
        $this->mapper = new Mapper();
    }

    public function testExtractUserPreferencesFromRows()
    {
        $rows = [
            [
                'id' => 1,
                'user_id' => 5,
                'name' => 'setting_1',
                'value' => 'value_1',
            ],
            [
                'id' => 1,
                'user_id' => 5,
                'name' => 'setting_2',
                'value' => 'value_2',
            ],
        ];

        $objects = [
            new UserPreference([
                'id' => 1,
                'userId' => 5,
                'name' => 'setting_1',
                'value' => 'value_1',
            ]),
            new UserPreference([
                'id' => 1,
                'userId' => 5,
                'name' => 'setting_2',
                'value' => 'value_2',
            ]),
        ];

        $this->assertEquals($objects, $this->mapper->extractUserPreferencesFromRows($rows));
    }
}

class_alias(MapperTest::class, 'eZ\Publish\Core\Persistence\Legacy\Tests\UserPreference\MapperTest');
