<?php

// Override address icon
$GLOBALS['TCA']['pages']['columns']['module']['config']['items'][] = [
    0 => 'LLL:EXT:address/Resources/Private/Language/locallang_be.xlf:address-folder',
    1 => 'address',
    2 => 'apps-pagetree-folder-contains-address'
];

