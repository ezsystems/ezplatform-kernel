<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
use Ibexa\Contracts\Core\Repository\Values\Content\Section;

return [
    [
        1 => new Section(
            [
                'id' => 1,
                'name' => 'Standard',
                'identifier' => 'standard',
            ]
        ),
        2 => new Section(
            [
                'id' => 2,
                'name' => 'Users',
                'identifier' => 'users',
            ]
        ),
        3 => new Section(
            [
                'id' => 3,
                'name' => 'Media',
                'identifier' => 'media',
            ]
        ),
        4 => new Section(
            [
                'id' => 4,
                'name' => 'Setup',
                'identifier' => 'setup',
            ]
        ),
        5 => new Section(
            [
                'id' => 5,
                'name' => 'Design',
                'identifier' => 'design',
            ]
        ),
        6 => new Section(
            [
                'id' => 6,
                'name' => 'Restricted',
                'identifier' => '',
            ]
        ),
    ],
    [
        'standard' => 1,
        'users' => 2,
        'media' => 3,
        'setup' => 4,
        'design' => 5,
    ],
    [
        '2' => [
                    4 => true,
                    10 => true,
                    11 => true,
                    12 => true,
                    13 => true,
                    14 => true,
                    42 => true,
                    59 => true,
                ],
        '3' => [
                    41 => true,
                    49 => true,
                    50 => true,
                    51 => true,
                ],
        '4' => [
                    45 => true,
                    52 => true,
                ],
        '5' => [
                    54 => true,
                    56 => true,
                ],
        '1' => [
                    57 => true,
                    58 => true,
                ],
    ],
    6,
];
