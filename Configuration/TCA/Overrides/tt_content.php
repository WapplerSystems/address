<?php


use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;



$pluginConfig = ['list_and_detail', 'list', 'detail', 'search_form', 'search_result', 'category_list', 'tag_list', 'map'];
foreach ($pluginConfig as $pluginName) {
    $pluginNameForLabel = $pluginName;
    ExtensionUtility::registerPlugin(
        'address',
        GeneralUtility::underscoredToUpperCamelCase($pluginName),
        'LLL:EXT:address/Resources/Private/Language/locallang_be.xlf:plugin.' . $pluginNameForLabel . '.title',
        null,
        'address'
    );

    $contentTypeName = 'address_' . str_replace('_', '', $pluginName);
    $flexformFileName = in_array($pluginNameForLabel, ['search_result', 'list'], true) ? 'list' : $pluginNameForLabel;

    ExtensionManagementUtility::addPiFlexFormValue(
        '*',
        'FILE:EXT:address/Configuration/FlexForms/flexform_' . $flexformFileName . '.xml',
        $contentTypeName
    );
    $GLOBALS['TCA']['tt_content']['ctrl']['typeicon_classes'][$contentTypeName] = 'ext-address-plugin-' . str_replace('_', '-', $pluginNameForLabel);

    //$GLOBALS['TCA']['tt_content']['types'][$contentTypeName]['previewRenderer'] = \WapplerSystems\Address\Hooks\PluginPreviewRenderer::class;
    $GLOBALS['TCA']['tt_content']['types'][$contentTypeName]['showitem'] = '
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
            --palette--;;general,
            --palette--;;headers,
        --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.plugin,
            pi_flexform,
        --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.appearance,
            --palette--;;frames,
            --palette--;;appearanceLinks,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language,
            --palette--;;language,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
            --palette--;;hidden,
            --palette--;;access,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:categories,
            categories,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:notes,
            rowDescription,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:extended,
    ';
}

ExtensionManagementUtility::addToInsertRecords('tx_address_domain_model_address');


