<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

return [
    'ibexa_setting' => [
        [
            'id' => 1,
            'group' => 'test_group',
            'identifier' => 'test_identifier',
            'value' => json_encode(['array' => 'value']),
        ],
        [
            'id' => 2,
            'group' => 'test_group',
            'identifier' => 'another_identifier',
            'value' => json_encode('string_value'),
        ],
        [
            'id' => 3,
            'group' => 'another_group',
            'identifier' => 'another_identifier',
            'value' => json_encode(1234),
        ],
        [
            'id' => 4,
            'group' => 'another_group',
            'identifier' => 'some_identifier',
            'value' => json_encode(true),
        ],
        [
            'id' => 5,
            'group' => 'another_group',
            'identifier' => 'other_identifier',
            'value' => json_encode([1, 2, 3]),
        ],
    ],
];
