<?php

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

if (ExtensionManagementUtility::isLoaded('tagging')) {

    ExtensionManagementUtility::addTCAcolumns('tx_address_domain_model_address', [
        'tags' => [
            'config' => [
                'type' => 'tag',
            ],
        ],
    ]);

    ExtensionManagementUtility::addToAllTCAtypes('tx_address_domain_model_address', 'tags', '', 'after:categories');


}

