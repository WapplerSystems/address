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
    'version' => '13.0.5',
    'constraints' => [
        'depends' => [
            'typo3' => '13.4.0-13.4.99',
            'tagging' => '13.0.0'
        ],
    ],
];
