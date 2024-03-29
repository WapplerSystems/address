<?php

return [
    'ctrl' => [
        'title' => 'LLL:EXT:address/Resources/Private/Language/locallang_db.xlf:tx_address_domain_model_link',
        'descriptionColumn' => 'description',
        'label' => 'title',
        'label_alt' => 'uri',
        'label_alt_force' => true,
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'languageField' => 'sys_language_uid',
        'transOrigPointerField' => 'l10n_parent',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'versioningWS' => true,
        'origUid' => 't3_origuid',
        'default_sortby' => 'ORDER BY sorting',
        'sortby' => 'sorting',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
        ],
        'typeicon_classes' => [
            'default' => 'ext-address-link'
        ],
        'hideTable' => true,
    ],
    'interface' => [
        'showRecordFieldList' => 'hidden,title,description,uri'
    ],
    'columns' => [
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
        'sys_language_uid' => [
            'exclude' => true,
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.language',
            'config' => ['type' => 'language']
        ],
        'l10n_parent' => [
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'exclude' => true,
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.l18n_parent',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['', 0],
                ],
                'foreign_table' => 'tx_address_domain_model_link',
                'foreign_table_where' => 'AND tx_address_domain_model_link.pid=###CURRENT_PID### AND tx_address_domain_model_link.sys_language_uid IN (-1,0)',
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
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.hidden',
            'config' => [
                'type' => 'check',
                'default' => 0
            ]
        ],
        'title' => [
            'exclude' => false,
            'l10n_mode' => 'mergeIfNotBlank',
            'label' => 'LLL:EXT:address/Resources/Private/Language/locallang_db.xlf:tx_address_domain_model_link.title',
            'config' => [
                'type' => 'input',
                'size' => 30,
            ]
        ],
        'description' => [
            'exclude' => true,
            'l10n_mode' => 'mergeIfNotBlank',
            'label' => 'LLL:EXT:address/Resources/Private/Language/locallang_db.xlf:tx_address_domain_model_link.description',
            'config' => [
                'type' => 'text',
                'cols' => 30,
                'rows' => 5,
            ]
        ],
        'uri' => [
            'exclude' => false,
            'l10n_mode' => 'mergeIfNotBlank',
            'label' => 'LLL:EXT:address/Resources/Private/Language/locallang_db.xlf:tx_address_domain_model_link.uri',
            'config' => [
                'type' => 'input',
                'placeholder' => 'LLL:EXT:address/Resources/Private/Language/locallang_db.xlf:tx_address_domain_model_link.uri.placeholder',
                'size' => 30,
                'eval' => 'trim,required',
                'softref' => 'typolink',
                'wizards' => [
                    'link' => [
                        'type' => 'popup',
                        'title' => 'Link',
                        'icon' => 'actions-wizard-link',
                        'module' => [
                            'name' => 'wizard_link',
                        ],
                        'JSopenParams' => 'height=300,width=500,status=0,menubar=0,scrollbars=1'
                    ]
                ]
            ]
        ],
    ],
    'types' => [
        0 => [
            'showitem' => 'uri, --palette--;;paletteCore,title, --palette--;;paletteDescription'
        ]
    ],
    'palettes' => [
        'paletteCore' => [
            'showitem' => 'hidden,sys_language_uid,l10n_parent, l10n_diffsource,',
        ],
        'paletteDescription' => [
            'showitem' => 'description',
        ]
    ]
];
