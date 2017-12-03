<?php
defined('TYPO3_MODE') or die();

return [
    'ctrl' => [
        'title' => 'LLL:EXT:address/Resources/Private/Language/locallang_db.xlf:tx_address_domain_model_tag',
        'descriptionColumn' => 'notes',
        'label' => 'title',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'default_sortby' => 'ORDER BY title',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
        ],
        'typeicon_classes' => [
            'default' => 'ext-address-tag'
        ],
        'searchFields' => 'uid,title',
    ],
    'interface' => [
        'showRecordFieldList' => 'hidden,title'
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
            'label' => 'LLL:EXT:address/Resources/Private/Language/locallang_db.xlf:tx_address_domain_model_tag.title',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'required,unique,trim',
            ]
        ],
        'seo_headline' => [
            'exclude' => true,
            'label' => 'LLL:EXT:address/Resources/Private/Language/locallang_db.xlf:tx_address_domain_model_tag.seo.seo_headline',
            'config' => [
                'type' => 'input',
            ],
        ],
        'seo_title' => [
            'exclude' => true,
            'label' => 'LLL:EXT:address/Resources/Private/Language/locallang_db.xlf:tx_address_domain_model_tag.seo.seo_title',
            'config' => [
                'type' => 'input',
            ],
        ],
        'seo_description' => [
            'exclude' => true,
            'label' => 'LLL:EXT:address/Resources/Private/Language/locallang_db.xlf:tx_address_domain_model_tag.seo.seo_description',
            'config' => [
                'type' => 'text',
            ],
        ],
        'seo_text' => [
            'exclude' => true,
            'label' => 'LLL:EXT:address/Resources/Private/Language/locallang_db.xlf:tx_address_domain_model_tag.seo.seo_text',
            'config' => [
                'type' => 'text',
            ],
            'defaultExtras' => 'richtext:rte_transform[mode=ts_css]',
        ],
        'notes' => [
            'label' => 'LLL:EXT:address/Resources/Private/Language/locallang_db.xlf:notes',
            'config' => [
                'type' => 'text',
                'rows' => 10,
                'cols' => 48
            ]
        ],
    ],
    'types' => [
        0 => [
            'showitem' => 'title, --palette--;;paletteCore,
            --div--;' . 'LLL:EXT:address/Resources/Private/Language/locallang_db.xlf:tx_address_domain_model_tag.tabs.seo, seo_title, seo_description, seo_headline, seo_text,
            --div--;' . 'LLL:EXT:address/Resources/Private/Language/locallang_db.xlf:notes,
                    notes,
			--div--;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.tabs.extended,'
        ]
    ],
    'palettes' => [
        'paletteCore' => [
            'showitem' => 'hidden,',
        ],
    ]
];
