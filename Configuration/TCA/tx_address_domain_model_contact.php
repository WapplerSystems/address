<?php

use TYPO3\CMS\Core\Utility\GeneralUtility;
use WapplerSystems\Address\Domain\Model\Dto\EmConfiguration;


/** @var EmConfiguration $configuration */
$configuration = GeneralUtility::makeInstance(EmConfiguration::class);


$tx_address_domain_model_contact = [
    'ctrl' => [
        'title' => 'LLL:EXT:address/Resources/Private/Language/locallang_db.xlf:tx_address_domain_model_contact',
        'descriptionColumn' => 'notes',
        'label' => 'content',
        'label_alt' => 'type,content',
        'prependAtCopy' => $configuration->getPrependAtCopy() ? 'LLL:EXT:lang/locallang_general.xlf:LGL.prependAtCopy' : '',
        'hideAtCopy' => true,
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'versioningWS' => true,
        'origUid' => 't3_origuid',
        'editlock' => 'editlock',
        'type' => 'type',
        'typeicon_column' => 'type',
        'typeicon_classes' => [
            'email' => 'ext-address-contact-type-email',
            'telephone' => 'ext-address-contact-type-telephone',
            'mobilephone' => 'ext-address-contact-type-mobilephone',
            'website' => 'ext-address-contact-type-website',
            'blog' => 'ext-address-contact-type-blog',
            'xing' => 'ext-address-contact-type-xing',
            'linkedin' => 'ext-address-contact-type-linkedin',
            'twitter' => 'ext-address-contact-type-twitter',
            'facebook' => 'ext-address-contact-type-facebook',
            'instagram' => 'ext-address-contact-type-instagram',
            'mastodon' => 'ext-address-contact-type-mastodon',
            'threema' => 'ext-address-contact-type-threema',
        ],
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
        'searchFields' => 'content',
        'hideTable' => true,
    ],
    'interface' => [
        'showRecordFieldList' => 'cruser_id,pid,sys_language_uid,l10n_parent,l10n_diffsource,hidden,starttime,endtime,content'
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
                'foreign_table' => 'tx_address_domain_model_contact',
                'foreign_table_where' => 'AND tx_address_domain_model_contact.pid=###CURRENT_PID### AND tx_address_domain_model_contact.sys_language_uid IN (-1,0)',
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
        'content' => [
            'exclude' => false,
            'label' => 'LLL:EXT:address/Resources/Private/Language/locallang_db.xlf:tx_address_domain_model_contact.content',
            'config' => [
                'type' => 'input',
                'size' => 60,
                'eval' => 'trim',
            ]
        ],
        'type' => [
            'exclude' => false,
            'l10n_display' => 'defaultAsReadonly',
            'l10n_mode' => 'exclude',
            'label' => 'LLL:EXT:address/Resources/Private/Language/locallang_db.xlf:tx_address_domain_model_contact.type',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['LLL:EXT:address/Resources/Private/Language/locallang_db.xlf:tx_address_domain_model_contact.type.email', 'email', 'ext-address-contact-type-email'],
                    ['LLL:EXT:address/Resources/Private/Language/locallang_db.xlf:tx_address_domain_model_contact.type.telephone', 'telephone', 'ext-address-contact-type-telephone'],
                    ['LLL:EXT:address/Resources/Private/Language/locallang_db.xlf:tx_address_domain_model_contact.type.mobilePhone', 'mobilephone', 'ext-address-contact-type-mobilephone'],
                    ['LLL:EXT:address/Resources/Private/Language/locallang_db.xlf:tx_address_domain_model_contact.type.website', 'website', 'ext-address-contact-type-website'],
                    ['LLL:EXT:address/Resources/Private/Language/locallang_db.xlf:tx_address_domain_model_contact.type.blog', 'blog', 'ext-address-contact-type-blog'],
                    ['LLL:EXT:address/Resources/Private/Language/locallang_db.xlf:tx_address_domain_model_contact.type.xing', 'xing', 'ext-address-contact-type-xing'],
                    ['LLL:EXT:address/Resources/Private/Language/locallang_db.xlf:tx_address_domain_model_contact.type.linkedin', 'linkedin', 'ext-address-contact-type-linkedin'],
                    ['LLL:EXT:address/Resources/Private/Language/locallang_db.xlf:tx_address_domain_model_contact.type.twitter', 'twitter', 'ext-address-contact-type-twitter'],
                    ['LLL:EXT:address/Resources/Private/Language/locallang_db.xlf:tx_address_domain_model_contact.type.facebook', 'facebook', 'ext-address-contact-type-facebook'],
                    ['LLL:EXT:address/Resources/Private/Language/locallang_db.xlf:tx_address_domain_model_contact.type.instagram', 'instagram', 'ext-address-contact-type-instagram'],
                    ['LLL:EXT:address/Resources/Private/Language/locallang_db.xlf:tx_address_domain_model_contact.type.mastodon', 'mastodon', 'ext-address-contact-type-mastodon'],
                    ['LLL:EXT:address/Resources/Private/Language/locallang_db.xlf:tx_address_domain_model_contact.type.threema', 'threema', 'ext-address-contact-type-threema'],
                    ['LLL:EXT:address/Resources/Private/Language/locallang_db.xlf:tx_address_domain_model_contact.type.fax', 'fax', 'ext-address-contact-type-fax'],
                ],
                'showIconTable' => true,
                'size' => 1,
                'maxitems' => 1,
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
            'showitem' => 'type, content, sys_language_uid, hidden,',
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
        $tx_address_domain_model_contact['columns']['categories']['config']['foreign_table_where'] = $categoryRestriction .
            $tx_address_domain_model_contact['columns']['categories']['config']['foreign_table_where'];
    }
}

if (!$configuration->getContentElementRelation()) {
    unset($tx_address_domain_model_contact['columns']['content_elements']);
}

if (!\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('news')) {
    unset($tx_address_domain_model_contact['columns']['related_news']);
}

return $tx_address_domain_model_contact;
