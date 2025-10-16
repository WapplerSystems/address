<?php

return [
    'dependencies' => [
        'core',
    ],
    'tags' => [
        'backend.module',
        'backend.form',
    ],
    'imports' => [
        '@wapplersystems/address/' => [
            'path' => 'EXT:address/Resources/Public/JavaScript/Backend/',
        ],
    ],
];
