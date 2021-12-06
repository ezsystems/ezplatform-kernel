<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
return [
    'ezurlwildcard' => [
        0 => [
            'id' => '1',
            'source_url' => 'developer/*',
            'destination_url' => 'dev/{1}',
            'type' => '2',
        ],
        1 => [
            'id' => '2',
            'source_url' => 'repository/*',
            'destination_url' => 'repo/{1}',
            'type' => '2',
        ],
        2 => [
            'id' => '3',
            'source_url' => 'information/*',
            'destination_url' => 'info/{1}',
            'type' => '2',
        ],
    ],
];
