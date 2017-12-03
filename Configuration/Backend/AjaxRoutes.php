<?php

/**
 * Definitions for routes provided by EXT:address
 */
return [
    'address_tag' => [
        'path' => '/address/tag',
        'target' => \WapplerSystems\Address\Backend\TagEndPoint::class . '::create'
    ]
];
