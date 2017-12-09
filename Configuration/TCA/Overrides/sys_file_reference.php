<?php
defined('TYPO3_MODE') or die();


if (!\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('news')) {

    /**
     * Add extra field showinpreview and some special address controls to sys_file_reference record
     */
    $newSysFileReferenceColumns = [
        'showinpreview' => [
            'exclude' => true,
            'label' => 'LLL:EXT:address/Resources/Private/Language/locallang_db.xlf:tx_address_domain_model_media.showinpreview',
            'config' => [
                'type' => 'check',
                'default' => 0
            ]
        ],
    ];

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('sys_file_reference',
        $newSysFileReferenceColumns);

}

// add special address palette
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addFieldsToPalette('sys_file_reference', 'addressPalette',
    'showinpreview');