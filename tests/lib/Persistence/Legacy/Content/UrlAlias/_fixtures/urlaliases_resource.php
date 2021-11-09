<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
return [
    'ezurlalias_ml' => [
        0 => [
            'action' => 'eznode:2',
            'action_type' => 'eznode',
            'alias_redirects' => '1',
            'id' => '1',
            'is_alias' => '0',
            'is_original' => '1',
            'lang_mask' => '4',
            'link' => '1',
            'parent' => '0',
            'text' => '',
            'text_md5' => 'd41d8cd98f00b204e9800998ecf8427e',
        ],
        1 => [
            'action' => 'module:ezinfo/isalive',
            'action_type' => 'module',
            'alias_redirects' => '1',
            'id' => '2',
            'is_alias' => '1',
            'is_original' => '1',
            'lang_mask' => '5',
            'link' => '2',
            'parent' => '0',
            'text' => 'is-alive',
            'text_md5' => 'd003895fa282a14c8ec3eddf23ca4ca2',
        ],
        2 => [
            'action' => 'nop:',
            'action_type' => 'nop',
            'alias_redirects' => '1',
            'id' => '3',
            'is_alias' => '0',
            'is_original' => '0',
            'lang_mask' => '1',
            'link' => '3',
            'parent' => '2',
            'text' => 'then',
            'text_md5' => '0e5243d9965540f62aac19a985f3f33e',
        ],
        3 => [
            'action' => 'module:content/search',
            'action_type' => 'module',
            'alias_redirects' => '0',
            'id' => '4',
            'is_alias' => '1',
            'is_original' => '1',
            'lang_mask' => '2',
            'link' => '4',
            'parent' => '3',
            'text' => 'search',
            'text_md5' => '06a943c59f33a34bb5924aaf72cd2995',
        ],
        4 => [
            'action' => 'nop:',
            'action_type' => 'nop',
            'alias_redirects' => '1',
            'id' => '5',
            'is_alias' => '0',
            'is_original' => '0',
            'lang_mask' => '1',
            'link' => '5',
            'parent' => '0',
            'text' => 'nop-element',
            'text_md5' => 'de55c2fff721217cc4cb67b58dc35f85',
        ],
        5 => [
            'action' => 'module:content/search',
            'action_type' => 'module',
            'alias_redirects' => '0',
            'id' => '6',
            'is_alias' => '1',
            'is_original' => '1',
            'lang_mask' => '4',
            'link' => '6',
            'parent' => '5',
            'text' => 'search',
            'text_md5' => '06a943c59f33a34bb5924aaf72cd2995',
        ],
    ],
    'ezcontentobject_tree' => [
        0 => [
            'node_id' => 1,
            'parent_node_id' => 1,
            'path_string' => '',
            'remote_id' => '',
        ],
        1 => [
            'node_id' => 2,
            'parent_node_id' => 1,
            'path_string' => '',
            'remote_id' => '',
        ],
        2 => [
            'node_id' => 314,
            'parent_node_id' => 2,
            'path_string' => '',
            'remote_id' => '',
        ],
    ],
    'ezcontent_language' => [
        0 => [
            'disabled' => 0,
            'id' => 2,
            'locale' => 'cro-HR',
            'name' => 'Croatian (Hrvatski)',
        ],
        1 => [
            'disabled' => 0,
            'id' => 4,
            'locale' => 'eng-GB',
            'name' => 'English (United Kingdom)',
        ],
    ],
    'ezurlalias_ml_incr' => [
        0 => [
            'id' => '1',
        ],
        1 => [
            'id' => '2',
        ],
        2 => [
            'id' => '3',
        ],
        3 => [
            'id' => '4',
        ],
        4 => [
            'id' => '5',
        ],
        5 => [
            'id' => '6',
        ],
    ],
];
