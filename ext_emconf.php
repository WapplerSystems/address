<?php


$EM_CONF['address'] = [
    'title' => 'Address list',
    'description' => 'Address extension with multiple contact types per address. Code is based on news extension.',
    'category' => 'fe',
    'author' => 'Sven Wappler',
    'author_email' => 'typo3YYYY@wappler.systems',
    'state' => 'stable',
    'clearCacheOnLoad' => 1,
    'author_company' => 'WapplerSystems',
    'version' => '12.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '12.4.0-12.4.99',
        ],
    ],
];
