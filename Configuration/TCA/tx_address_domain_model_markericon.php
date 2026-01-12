<?php

use TYPO3\CMS\Core\Utility\GeneralUtility;
use WapplerSystems\Address\Domain\Model\Dto\EmConfiguration;


/** @var EmConfiguration $configuration */
$configuration = GeneralUtility::makeInstance(EmConfiguration::class);


$tx_address_domain_model_markericon = [
    'ctrl' => [
        'title' => 'LLL:EXT:address/Resources/Private/Language/locallang_db.xlf:tx_address_domain_model_markericon',
        'descriptionColumn' => 'notes',
        'label' => 'icon',
        'prependAtCopy' => $configuration->getPrependAtCopy() ? 'LLL:EXT:lang/locallang_general.xlf:LGL.prependAtCopy' : '',
        'hideAtCopy' => true,
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'versioningWS' => true,
        'origUid' => 't3_origuid',
        'editlock' => 'editlock',
        'useColumnsForDefaultValues' => 'type',
        'languageField' => 'sys_language_uid',
        'transOrigPointerField' => 'l10n_parent',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'default_sortby' => 'sorting',
        'sortby' => 'sorting',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
        ],
        'typeicon_classes' => [
            'default' => 'ext-address-markericon',
        ],
    ],
    'interface' => [
        'showRecordFieldList' => 'cruser_id,pid,sys_language_uid,l10n_parent,l10n_diffsource,hidden,starttime,endtime,icon'
    ],
    'columns' => [
        'sys_language_uid' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.language',
            'config' => ['type' => 'language']
        ],
        'l10n_parent' => [
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.l18n_parent',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['', 0],
                ],
                'foreign_table' => 'tx_address_domain_model_markericon',
                'foreign_table_where' => 'AND tx_address_domain_model_markericon.pid=###CURRENT_PID### AND tx_address_domain_model_markericon.sys_language_uid IN (-1,0)',
                'showIconTable' => false,
                'default' => 0,
            ]
        ],
        'l10n_diffsource' => [
            'config' => [
                'type' => 'passthrough',
                'default' => ''
            ]
        ],
        'hidden' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.visible',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
                'items' => [
                    [
                        0 => '',
                        1 => '',
                        'invertStateDisplay' => true
                    ]
                ],
            ]
        ],
        'pid' => [
            'label' => 'pid',
            'config' => [
                'type' => 'passthrough'
            ]
        ],
        'crdate' => [
            'label' => 'crdate',
            'config' => [
                'type' => 'passthrough',
            ]
        ],
        'tstamp' => [
            'label' => 'tstamp',
            'config' => [
                'type' => 'passthrough',
            ]
        ],
        'sorting' => [
            'label' => 'sorting',
            'config' => [
                'type' => 'passthrough',
            ]
        ],
        'starttime' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.starttime',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'eval' => 'datetime,int',
                'default' => 0
            ],
            'l10n_mode' => 'exclude',
            'l10n_display' => 'defaultAsReadonly'
        ],
        'endtime' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.endtime',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'eval' => 'datetime,int',
                'default' => 0,
                'range' => [
                    'upper' => mktime(0, 0, 0, 1, 1, 2038)
                ]
            ],
            'l10n_mode' => 'exclude',
            'l10n_display' => 'defaultAsReadonly'
        ],
        'icon' => [
            'exclude' => false,
            'label' => 'LLL:EXT:address/Resources/Private/Language/locallang_db.xlf:tx_address_domain_model_markericon.icon',
            'config' => [
                'type' => 'file',
            ]
        ],
        'size' => [
            'exclude' => false,
            'label' => 'LLL:EXT:address/Resources/Private/Language/locallang_db.xlf:tx_address_domain_model_markericon.size',
            'description' => 'LLL:EXT:address/Resources/Private/Language/locallang_db.xlf:tx_address_domain_model_markericon.size.description',
            'config' => [
                'type' => 'input',
                'size' => 7,
                'eval' => 'trim'
            ]
        ],
        'anchor' => [
            'exclude' => false,
            'label' => 'LLL:EXT:address/Resources/Private/Language/locallang_db.xlf:tx_address_domain_model_markericon.anchor',
            'description' => 'LLL:EXT:address/Resources/Private/Language/locallang_db.xlf:tx_address_domain_model_markericon.anchor.description',
            'config' => [
                'type' => 'input',
                'size' => 7,
                'eval' => 'trim'
            ]
        ],
        'notes' => [
            'label' => 'LLL:EXT:address/Resources/Private/Language/locallang_db.xlf:notes',
            'config' => [
                'type' => 'text',
                'rows' => 10,
                'cols' => 48
            ]
        ]
    ],
    'types' => [
        '0' => [
            'showitem' => 'l10n_parent, l10n_diffsource,
                    title,--palette--;;paletteCore,
                --div--;LLL:EXT:address/Resources/Private/Language/locallang_db.xlf:notes,
                    notes,'
        ],
    ],
    'palettes' => [
        'paletteCore' => [
            'showitem' => 'type, icon, --linebreak--, size, --linebreak--, anchor, --linebreak--, sys_language_uid,',
        ],
        'paletteAccess' => [
            'showitem' => 'starttime;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:starttime_formlabel,
                    endtime;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:endtime_formlabel,
                    --linebreak--,editlock,',
        ],
    ]
];

// category restriction based on settings in extension manager
$categoryRestrictionSetting = $configuration->getCategoryRestriction();
if ($categoryRestrictionSetting) {
    $categoryRestriction = '';
    switch ($categoryRestrictionSetting) {
        case 'current_pid':
            $categoryRestriction = ' AND sys_category.pid=###CURRENT_PID### ';
            break;
        case 'siteroot':
            $categoryRestriction = ' AND sys_category.pid IN (###SITEROOT###) ';
            break;
        case 'page_tsconfig':
            $categoryRestriction = ' AND sys_category.pid IN (###PAGE_TSCONFIG_IDLIST###) ';
            break;
        default:
            $categoryRestriction = '';
    }

    // prepend category restriction at the beginning of foreign_table_where
    if (!empty($categoryRestriction)) {
        $tx_address_domain_model_markericon['columns']['categories']['config']['foreign_table_where'] = $categoryRestriction .
            $tx_address_domain_model_markericon['columns']['categories']['config']['foreign_table_where'];
    }
}

if (!\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('news')) {
    unset($tx_address_domain_model_markericon['columns']['related_news']);
}

return $tx_address_domain_model_markericon;
