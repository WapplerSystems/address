<?php


$EM_CONF[$_EXTKEY] = [
    'title' => 'Address list',
    'description' => 'Address extension with multiple contact types per address. Code is based on news extension.',
    'category' => 'fe',
    'author' => 'Sven Wappler',
    'author_email' => 'typo3YYYY@wappler.systems',
    'state' => 'stable',
    'clearCacheOnLoad' => 1,
    'author_company' => 'WapplerSystems',
    'version' => '14.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '14.0.0-14.4.99',
        ],
    ],
];
