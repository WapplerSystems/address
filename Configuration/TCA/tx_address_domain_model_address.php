<?php
defined('TYPO3_MODE') or die();

// Extension manager configuration
$configuration = \WapplerSystems\Address\Utility\EmConfiguration::getSettings();

$teaserRteConfiguration = $configuration->getRteForTeaser() ? 'richtext:rte_transform[mode=ts_css]' : '';

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToInsertRecords('tx_address_domain_model_address');

$tx_address_domain_model_address = [
    'ctrl' => [
        'title' => 'LLL:EXT:address/Resources/Private/Language/locallang_db.xlf:tx_address_domain_model_address',
        'descriptionColumn' => 'notes',
        'label' => 'title',
        'label_alt' => 'last_name',
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
            'default' => 'ext-address-type-default',
            '1' => 'ext-address-type-person',
            '2' => 'ext-address-type-company',
        ],
        'useColumnsForDefaultValues' => 'type',
        'languageField' => 'sys_language_uid',
        'transOrigPointerField' => 'l10n_parent',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'default_sortby' => 'ORDER BY title,last_name DESC',
        'sortby' => ($configuration->getManualSorting() ? 'sorting' : ''),
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
        ],
        'iconfile' => 'EXT:address/Resources/Public/Icons/address_domain_model_address.svg',
        'searchFields' => 'uid,title,first_name,last_name',
        'thumbnail' => 'media',
    ],
    'interface' => [
        'showRecordFieldList' => 'cruser_id,pid,sys_language_uid,l10n_parent,l10n_diffsource,hidden,starttime,endtime,title,teaser,bodytext,archive,categories,related,type,keywords,media,url,istopaddress,related_files,related_links,content_elements,tags,path_segment,alternative_title,related_files,detail_pid'
    ],
    'columns' => [
        'sys_language_uid' => [
            'exclude' => true,
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.language',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'special' => 'languages',
                'items' => [
                    [
                        'LLL:EXT:lang/locallang_general.xlf:LGL.allLanguages',
                        -1,
                        'flags-multiple'
                    ],
                ],
                'default' => 0,
            ]
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
                'foreign_table' => 'tx_address_domain_model_address',
                'foreign_table_where' => 'AND tx_address_domain_model_address.pid=###CURRENT_PID### AND tx_address_domain_model_address.sys_language_uid IN (-1,0)',
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
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.hidden',
            'config' => [
                'type' => 'check',
                'default' => 0
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
            'l10n_mode' => 'mergeIfNotBlank',
            'label' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:starttime_formlabel',
            'config' => [
                'type' => 'input',
                'size' => 16,
                'eval' => 'datetime',
                'default' => 0,
            ]
        ],
        'endtime' => [
            'exclude' => true,
            'l10n_mode' => 'mergeIfNotBlank',
            'label' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:endtime_formlabel',
            'config' => [
                'type' => 'input',
                'size' => 16,
                'eval' => 'datetime',
                'default' => 0,
            ]
        ],
        'title' => [
            'exclude' => false,
            'l10n_display' => 'defaultAsReadonly',
            'l10n_mode' => 'exclude',
            'label' => 'LLL:EXT:address/Resources/Private/Language/locallang_db.xlf:tx_address_domain_model_address.title',
            'config' => [
                'type' => 'input',
                'size' => 60,
                'eval' => '',
            ]
        ],
        'first_name' => [
            'exclude' => false,
            'l10n_display' => 'defaultAsReadonly',
            'l10n_mode' => 'exclude',
            'label' => 'LLL:EXT:address/Resources/Private/Language/locallang_db.xlf:tx_address_domain_model_address.first_name',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => '',
            ]
        ],
        'middle_name' => [
            'exclude' => false,
            'l10n_display' => 'defaultAsReadonly',
            'l10n_mode' => 'exclude',
            'label' => 'LLL:EXT:address/Resources/Private/Language/locallang_db.xlf:tx_address_domain_model_address.middle_name',
            'config' => [
                'type' => 'input',
                'size' => 10,
                'eval' => '',
            ]
        ],
        'last_name' => [
            'exclude' => false,
            'l10n_display' => 'defaultAsReadonly',
            'l10n_mode' => 'exclude',
            'label' => 'LLL:EXT:address/Resources/Private/Language/locallang_db.xlf:tx_address_domain_model_address.last_name',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => '',
            ]
        ],
        'birthday' => [
            'exclude' => false,
            'l10n_display' => 'defaultAsReadonly',
            'l10n_mode' => 'exclude',
            'label' => 'LLL:EXT:address/Resources/Private/Language/locallang_db.xlf:tx_address_domain_model_address.birthday',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'dbType' => 'date',
                'eval' => 'date',
                'size' => 10,
            ]
        ],
        'academic_title' => [
            'exclude' => false,
            'l10n_mode' => 'mergeIfNotBlank',
            'label' => 'LLL:EXT:address/Resources/Private/Language/locallang_db.xlf:tx_address_domain_model_address.academic_title',
            'config' => [
                'type' => 'input',
                'size' => 10,
                'eval' => '',
            ]
        ],
        'append_academic_title' => [
            'exclude' => true,
            'l10n_mode' => 'exclude',
            'l10n_display' => 'defaultAsReadonly',
            'label' => 'LLL:EXT:address/Resources/Private/Language/locallang_db.xlf:tx_address_domain_model_address.append_academic_title',
            'config' => [
                'type' => 'check',
                'default' => 0
            ]
        ],
        'position' => [
            'exclude' => false,
            'l10n_mode' => 'mergeIfNotBlank',
            'label' => 'LLL:EXT:address/Resources/Private/Language/locallang_db.xlf:tx_address_domain_model_address.position',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => '',
            ]
        ],
        'phone' => [
            'exclude' => false,
            'l10n_display' => 'defaultAsReadonly',
            'l10n_mode' => 'exclude',
            'label' => 'LLL:EXT:address/Resources/Private/Language/locallang_db.xlf:tx_address_domain_model_address.phone',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => '',
            ]
        ],
        'fax' => [
            'exclude' => false,
            'l10n_display' => 'defaultAsReadonly',
            'l10n_mode' => 'exclude',
            'label' => 'LLL:EXT:address/Resources/Private/Language/locallang_db.xlf:tx_address_domain_model_address.fax',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => '',
            ]
        ],
        'email' => [
            'exclude' => false,
            'l10n_display' => 'defaultAsReadonly',
            'l10n_mode' => 'exclude',
            'label' => 'LLL:EXT:address/Resources/Private/Language/locallang_db.xlf:tx_address_domain_model_address.email',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => '',
            ]
        ],
        'teaser' => [
            'exclude' => true,
            'l10n_mode' => 'mergeIfNotBlank',
            'label' => 'LLL:EXT:address/Resources/Private/Language/locallang_db.xlf:tx_address_domain_model_address.teaser',
            'config' => [
                'type' => 'text',
                'cols' => 60,
                'rows' => 5,
            ]
        ],
        'latitude' => [
            'exclude' => false,
            'l10n_display' => 'defaultAsReadonly',
            'l10n_mode' => 'exclude',
            'label' => 'LLL:EXT:address/Resources/Private/Language/locallang_db.xlf:tx_address_domain_model_address.latitude',
            'config' => [
                'type' => 'input',
                'eval' => 'null,trim,WapplerSystems\\Address\\Evaluation\\Double6Evaluator',
                'default' => null,
            ]
        ],
        'longitude' => [
            'exclude' => false,
            'l10n_display' => 'defaultAsReadonly',
            'l10n_mode' => 'exclude',
            'label' => 'LLL:EXT:address/Resources/Private/Language/locallang_db.xlf:tx_address_domain_model_address.longitude',
            'config' => [
                'type' => 'input',
                'eval' => 'null,trim,WapplerSystems\\Address\\Evaluation\\Double6Evaluator',
                'default' => null,
            ]
        ],
        'address' => [
            'exclude' => true,
            'l10n_mode' => 'mergeIfNotBlank',
            'label' => 'LLL:EXT:address/Resources/Private/Language/locallang_db.xlf:tx_address_domain_model_address.address',
            'config' => [
                'type' => 'text',
                'cols' => 30,
                'rows' => 3,
            ]
        ],
        'city' => [
            'exclude' => true,
            'l10n_mode' => 'mergeIfNotBlank',
            'label' => 'LLL:EXT:address/Resources/Private/Language/locallang_db.xlf:tx_address_domain_model_address.city',
            'config' => [
                'type' => 'input',
            ]
        ],
        'zip' => [
            'exclude' => true,
            'l10n_mode' => 'mergeIfNotBlank',
            'label' => 'LLL:EXT:address/Resources/Private/Language/locallang_db.xlf:tx_address_domain_model_address.zip',
            'config' => [
                'type' => 'input',
            ]
        ],
        'region' => [
            'exclude' => true,
            'l10n_mode' => 'mergeIfNotBlank',
            'label' => 'LLL:EXT:address/Resources/Private/Language/locallang_db.xlf:tx_address_domain_model_address.region',
            'config' => [
                'type' => 'input',
            ]
        ],
        'country' => [
            'exclude' => true,
            'l10n_mode' => 'mergeIfNotBlank',
            'label' => 'LLL:EXT:address/Resources/Private/Language/locallang_db.xlf:tx_address_domain_model_address.country',
            'config' => [
                'type' => 'input',
            ]
        ],
        'building' => [
            'exclude' => true,
            'l10n_display' => 'defaultAsReadonly',
            'l10n_mode' => 'exclude',
            'label' => 'LLL:EXT:address/Resources/Private/Language/locallang_db.xlf:tx_address_domain_model_address.building',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => '',
            ]
        ],
        'inline_map' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:address/Resources/Private/Language/locallang_db.xlf:tx_address_domain_model_address.inline_map',
            'config' => [
                'type' => 'user',
                'userFunc' => 'WapplerSystems\\Address\\Utility\\LocationUtility->render',
                'parameters' => [
                    'longitude' => 'longitude',
                    'latitude' => 'latitude',
                    'city' => 'city',
                    'zip' => 'zip',
                    'country' => 'country',
                    'address' => 'address',
                ],
            ],
        ],
        'bodytext' => [
            'exclude' => false,
            'l10n_mode' => 'mergeIfNotBlank',
            'label' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:bodytext_formlabel',
            'config' => [
                'type' => 'text',
                'cols' => 30,
                'rows' => 5,
                'softref' => 'rtehtmlarea_images,typolink_tag,images,email[subst],url',
                'wizards' => [
                    'RTE' => [
                        'notNewRecords' => 1,
                        'RTEonly' => 1,
                        'type' => 'script',
                        'title' => 'Full screen Rich Text Editing',
                        'icon' => 'actions-wizard-rte',
                        'module' => [
                            'name' => 'wizard_rte',
                        ],
                    ],
                ],
            ]
        ],
        'archive' => [
            'exclude' => true,
            'l10n_display' => 'defaultAsReadonly',
            'l10n_mode' => 'exclude',
            'label' => 'LLL:EXT:address/Resources/Private/Language/locallang_db.xlf:tx_address_domain_model_address.archive',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => $configuration->getArchiveDate(),
                'default' => 0
            ]
        ],
        'categories' => [
            'exclude' => true,
            'l10n_mode' => 'exclude',
            'label' => 'LLL:EXT:address/Resources/Private/Language/locallang_db.xlf:tx_address_domain_model_address.categories',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectTree',
                'treeConfig' => [
                    'dataProvider' => \WapplerSystems\Address\TreeProvider\DatabaseTreeDataProvider::class,
                    'parentField' => 'parent',
                    'appearance' => [
                        'showHeader' => true,
                        'expandAll' => true,
                        'maxLevels' => 99,
                    ],
                ],
                'MM' => 'sys_category_record_mm',
                'MM_match_fields' => [
                    'fieldname' => 'categories',
                    'tablenames' => 'tx_address_domain_model_address',
                ],
                'MM_opposite_field' => 'items',
                'foreign_table' => 'sys_category',
                'foreign_table_where' => ' AND (sys_category.sys_language_uid = 0 OR sys_category.l10n_parent = 0) ORDER BY sys_category.sorting',
                'size' => 10,
                'minitems' => 0,
                'maxitems' => 99,
            ]
        ],
        'related' => [
            'exclude' => true,
            'l10n_mode' => 'exclude',
            'label' => 'LLL:EXT:address/Resources/Private/Language/locallang_db.xlf:tx_address_domain_model_address.related',
            'config' => [
                'type' => 'group',
                'internal_type' => 'db',
                'allowed' => 'tx_address_domain_model_address',
                'foreign_table' => 'tx_address_domain_model_address',
                'MM_opposite_field' => 'related_from',
                'size' => 5,
                'minitems' => 0,
                'maxitems' => 100,
                'MM' => 'tx_address_domain_model_address_related_mm',
                'wizards' => [
                    'suggest' => [
                        'type' => 'suggest',
                        'default' => [
                            'searchWholePhrase' => true,
                            'addWhere' => ' AND tx_address_domain_model_address.uid != ###THIS_UID###'
                        ]
                    ],
                ],
            ]
        ],
        'related_from' => [
            'exclude' => true,
            'label' => 'LLL:EXT:address/Resources/Private/Language/locallang_db.xlf:tx_address_domain_model_address.related_from',
            'config' => [
                'type' => 'group',
                'internal_type' => 'db',
                'foreign_table' => 'tx_address_domain_model_address',
                'allowed' => 'tx_address_domain_model_address',
                'size' => 5,
                'maxitems' => 100,
                'MM' => 'tx_address_domain_model_address_related_mm',
                'readOnly' => 1,
            ]
        ],

        'related_links' => [
            'exclude' => true,
            'l10n_mode' => 'mergeIfNotBlank',
            'label' => 'LLL:EXT:address/Resources/Private/Language/locallang_db.xlf:tx_address_domain_model_address.related_links',
            'config' => [
                'type' => 'inline',
                'allowed' => 'tx_address_domain_model_link',
                'foreign_table' => 'tx_address_domain_model_link',
                'foreign_sortby' => 'sorting',
                'foreign_field' => 'parent',
                'size' => 5,
                'minitems' => 0,
                'maxitems' => 100,
                'appearance' => [
                    'collapseAll' => true,
                    'expandSingle' => true,
                    'levelLinksPosition' => 'bottom',
                    'useSortable' => true,
                    'showPossibleLocalizationRecords' => true,
                    'showRemovedLocalizationRecords' => true,
                    'showAllLocalizationLink' => true,
                    'showSynchronizationLink' => true,
                    'enabledControls' => [
                        'info' => false,
                    ]
                ]
            ]
        ],
        'type' => [
            'exclude' => false,
            'l10n_display' => 'defaultAsReadonly',
            'l10n_mode' => 'exclude',
            'label' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.doktype_formlabel',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['LLL:EXT:address/Resources/Private/Language/locallang_db.xlf:tx_address_domain_model_address.type.I.0', 0, 'ext-address-type-default'],
                    ['LLL:EXT:address/Resources/Private/Language/locallang_db.xlf:tx_address_domain_model_address.type.I.1', 1, 'ext-address-type-person'],
                    ['LLL:EXT:address/Resources/Private/Language/locallang_db.xlf:tx_address_domain_model_address.type.I.2', 2, 'ext-address-type-company'],
                ],
                'showIconTable' => true,
                'size' => 1,
                'maxitems' => 1,
            ]
        ],
        'keywords' => [
            'exclude' => true,
            'l10n_mode' => 'mergeIfNotBlank',
            'label' => $GLOBALS['TCA']['pages']['columns']['keywords']['label'],
            'config' => [
                'type' => 'text',
                'placeholder' => 'LLL:EXT:address/Resources/Private/Language/locallang_db.xlf:tx_address_domain_model_address.keywords.placeholder',
                'cols' => 30,
                'rows' => 5,
            ]
        ],
        'description' => [
            'exclude' => true,
            'l10n_mode' => 'mergeIfNotBlank',
            'label' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.description_formlabel',
            'config' => [
                'type' => 'text',
                'cols' => 30,
                'rows' => 5,
            ]
        ],
        'url' => [
            'exclude' => false,
            'label' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.doktype.I.8',
            'config' => [
                'type' => 'input',
                'size' => 50,
                'eval' => 'required',
                'softref' => 'typolink'
            ]
        ],
        'istopaddress' => [
            'exclude' => true,
            'l10n_mode' => 'exclude',
            'l10n_display' => 'defaultAsReadonly',
            'label' => 'LLL:EXT:address/Resources/Private/Language/locallang_db.xlf:tx_address_domain_model_address.istopaddress',
            'config' => [
                'type' => 'check',
                'default' => 0
            ]
        ],
        'direct_contact' => [
            'exclude' => true,
            'l10n_mode' => 'exclude',
            'l10n_display' => 'defaultAsReadonly',
            'label' => 'LLL:EXT:address/Resources/Private/Language/locallang_db.xlf:tx_address_domain_model_address.direct_contact',
            'config' => [
                'type' => 'check',
                'default' => 0
            ]
        ],
        'editlock' => [
            'exclude' => true,
            'l10n_mode' => 'mergeIfNotBlank',
            'label' => 'LLL:EXT:lang/locallang_tca.xlf:editlock',
            'config' => [
                'type' => 'check'
            ]
        ],
        'content_elements' => [
            'exclude' => true,
            'l10n_mode' => 'mergeIfNotBlank',
            'label' => 'LLL:EXT:address/Resources/Private/Language/locallang_db.xlf:tx_address_domain_model_address.content_elements',
            'config' => [
                'type' => 'inline',
                'allowed' => 'tt_content',
                'foreign_table' => 'tt_content',
                'foreign_sortby' => 'sorting',
                'foreign_field' => 'tx_address_related_address',
                'minitems' => 0,
                'maxitems' => 99,
                'appearance' => [
                    'useXclassedVersion' => $configuration->getContentElementPreview(),
                    'collapseAll' => true,
                    'expandSingle' => true,
                    'levelLinksPosition' => 'bottom',
                    'useSortable' => true,
                    'showPossibleLocalizationRecords' => true,
                    'showRemovedLocalizationRecords' => true,
                    'showAllLocalizationLink' => true,
                    'showSynchronizationLink' => true,
                    'enabledControls' => [
                        'info' => false,
                    ]
                ]
            ]
        ],
        'related_news' => [
            'exclude' => true,
            'l10n_mode' => 'mergeIfNotBlank',
            'label' => 'LLL:EXT:address/Resources/Private/Language/locallang_db.xlf:tx_address_domain_model_address.related_news',
            'config' => [
                'type' => 'group',
                'internal_type' => 'db',
                'allowed' => 'tx_news_domain_model_news',
                'foreign_table' => 'tx_news_domain_model_news',
                'foreign_sortby' => 'sorting',
                'minitems' => 0,
                'maxitems' => 99,
                'wizards' => [
                    'suggest' => [
                        'type' => 'suggest',
                        'default' => [
                            'searchWholePhrase' => true,
                        ],
                    ],
                ],
            ]
        ],
        'tags' => [
            'exclude' => true,
            'l10n_mode' => 'mergeIfNotBlank',
            'label' => 'LLL:EXT:address/Resources/Private/Language/locallang_db.xlf:tx_address_domain_model_address.tags',
            'config' => [
                'type' => 'group',
                'internal_type' => 'db',
                'allowed' => 'tx_address_domain_model_tag',
                'MM' => 'tx_address_domain_model_address_tag_mm',
                'foreign_table' => 'tx_address_domain_model_tag',
                'foreign_table_where' => 'ORDER BY tx_address_domain_model_tag.title',
                'size' => 10,
                'minitems' => 0,
                'maxitems' => 99,
                'wizards' => [
                    'suggest' => [
                        'type' => 'suggest',
                        'default' => [
                            'searchWholePhrase' => true,
                            'receiverClass' => \WapplerSystems\Address\Hooks\SuggestReceiver::class
                        ],
                    ],
                    'list' => [
                        'type' => 'script',
                        'title' => 'LLL:EXT:address/Resources/Private/Language/locallang_db.xlf:tx_address_domain_model_address.tags.list',
                        'icon' => 'actions-system-list-open',
                        'params' => [
                            'table' => 'tx_address_domain_model_tag',
                            'pid' => $configuration->getTagPid(),
                        ],
                        'module' => [
                            'name' => 'wizard_list',
                        ],
                    ],
                    'edit' => [
                        'type' => 'popup',
                        'title' => 'LLL:EXT:address/Resources/Private/Language/locallang_db.xlf:tx_address_domain_model_address.tags.edit',
                        'module' => [
                            'name' => 'wizard_edit',
                        ],
                        'popup_onlyOpenIfSelected' => true,
                        'icon' => 'actions-open',
                        'JSopenParams' => 'height=350,width=580,status=0,menubar=0,scrollbars=1',
                    ],
                ],
            ],
        ],
        'path_segment' => [
            'exclude' => true,
            'label' => 'LLL:EXT:address/Resources/Private/Language/locallang_db.xlf:tx_address_domain_model_address.path_segment',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'nospace,alphanum_x,lower,unique',
            ]
        ],
        'import_id' => [
            'label' => 'LLL:EXT:address/Resources/Private/Language/locallang_db.xlf:tx_address_domain_model_address.import_id',
            'config' => [
                'type' => 'passthrough'
            ]
        ],
        'import_source' => [
            'label' => 'LLL:EXT:address/Resources/Private/Language/locallang_db.xlf:tx_address_domain_model_address.import_source',
            'config' => [
                'type' => 'passthrough'
            ]
        ],
        'media' => [
            'exclude' => true,
            'l10n_mode' => 'mergeIfNotBlank',
            'label' => 'LLL:EXT:address/Resources/Private/Language/locallang_db.xlf:tx_address_domain_model_address.media',
            'config' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig(
                'media',
                [
                    'appearance' => [
                        'createNewRelationLinkTitle' => 'LLL:EXT:address/Resources/Private/Language/locallang_db.xlf:tx_address_domain_model_address.media.add',
                        'showPossibleLocalizationRecords' => true,
                        'showRemovedLocalizationRecords' => true,
                        'showAllLocalizationLink' => true,
                        'showSynchronizationLink' => true
                    ],
                    'foreign_match_fields' => [
                        'fieldname' => 'media',
                        'tablenames' => 'tx_address_domain_model_address',
                        'table_local' => 'sys_file',
                    ],
                    // custom configuration for displaying fields in the overlay/reference table
                    // to use the addressPalette and imageoverlayPalette instead of the basicoverlayPalette
                    'foreign_types' => [
                        '0' => [
                            'showitem' => '
						--palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;addressPalette,
						--palette--;;imageoverlayPalette,
						--palette--;;filePalette'
                        ],
                        \TYPO3\CMS\Core\Resource\File::FILETYPE_TEXT => [
                            'showitem' => '
						--palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;addressPalette,
						--palette--;;imageoverlayPalette,
						--palette--;;filePalette'
                        ],
                        \TYPO3\CMS\Core\Resource\File::FILETYPE_IMAGE => [
                            'showitem' => '
						--palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;addressPalette,
						--palette--;;imageoverlayPalette,
						--palette--;;filePalette'
                        ],
                        \TYPO3\CMS\Core\Resource\File::FILETYPE_AUDIO => [
                            'showitem' => '
						--palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;addressPalette,
						--palette--;;imageoverlayPalette,
						--palette--;;filePalette'
                        ],
                        \TYPO3\CMS\Core\Resource\File::FILETYPE_VIDEO => [
                            'showitem' => '
						--palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;addressPalette,
						--palette--;;imageoverlayPalette,
						--palette--;;filePalette'
                        ],
                        \TYPO3\CMS\Core\Resource\File::FILETYPE_APPLICATION => [
                            'showitem' => '
						--palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;addressPalette,
						--palette--;;imageoverlayPalette,
						--palette--;;filePalette'
                        ]
                    ]
                ],
                $GLOBALS['TYPO3_CONF_VARS']['SYS']['mediafile_ext']
            )
        ],
        'related_files' => [
            'exclude' => true,
            'l10n_mode' => 'mergeIfNotBlank',
            'label' => 'LLL:EXT:address/Resources/Private/Language/locallang_db.xlf:tx_address_domain_model_address.related_files',
            'config' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig(
                'related_files',
                [
                    'appearance' => [
                        'createNewRelationLinkTitle' => 'LLL:EXT:address/Resources/Private/Language/locallang_db.xlf:tx_address_domain_model_address.related_files.add',
                        'showPossibleLocalizationRecords' => true,
                        'showRemovedLocalizationRecords' => true,
                        'showAllLocalizationLink' => true,
                        'showSynchronizationLink' => true
                    ],
                    'inline' => [
                        'inlineOnlineMediaAddButtonStyle' => 'display:none'
                    ],
                    'foreign_match_fields' => [
                        'fieldname' => 'related_files',
                        'tablenames' => 'tx_address_domain_model_address',
                        'table_local' => 'sys_file',
                    ],
                ]
            )
        ],
        'notes' => [
            'label' => 'LLL:EXT:address/Resources/Private/Language/locallang_db.xlf:notes',
            'config' => [
                'type' => 'text',
                'rows' => 10,
                'cols' => 48
            ]
        ],
        'detail_pid' => [
            'exclude' => true,
            'l10n_mode' => 'mergeIfNotBlank',
            'label' => 'LLL:EXT:address/Resources/Private/Language/locallang_db.xlf:tx_address_domain_model_address.detail_pid',
            'config' => [
                'type' => 'group',
                'internal_type' => 'db',
                'allowed' => 'pages',
                'size' => 1,
                'maxitems' => 1,
                'show_thumbs' => 1,
                'default' => 0,
                'wizards' => [
                    'suggest' => [
                        'type' => 'suggest',
                        'default' => [
                            'searchWholePhrase' => true
                        ]
                    ],
                ],
            ]
        ],
    ],
    'types' => [
        // default address
        '0' => [
            'columnsOverrides' => [
                'bodytext' => [
                    'defaultExtras' => 'richtext:rte_transform[mode=ts_css]'
                ],
                'teaser' => [
                    'defaultExtras' => $teaserRteConfiguration
                ],
            ],
            'showitem' => 'l10n_parent, l10n_diffsource,
					title,--palette--;;paletteCore,
					
					--palette--;;palettePerson,
					--palette--;;paletteContact,
					teaser,
					bodytext;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:rte_enabled_formlabel,
					--palette--;;paletteArchive,
				--div--;LLL:EXT:address/Resources/Private/Language/locallang_db.xlf:location,
				    --palette--;;paletteLocation,
                --div--;LLL:EXT:address/Resources/Private/Language/locallang_db.xlf:tx_address_domain_model_address.content_elements,content_elements,

				--div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.access,
					--palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.access;paletteAccess,

				--div--;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.tabs.options,categories,tags,
				--div--;LLL:EXT:address/Resources/Private/Language/locallang_db.xlf:tx_address_domain_model_address.tabs.relations,media,related_files,related_links,related,related_from,detail_pid,
				--div--;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.tabs.metadata,
					--palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.palettes.metatags;metatags,
					--palette--;LLL:EXT:address/Resources/Private/Language/locallang_db.xlf:tx_address_domain_model_address.palettes.alternativeTitles;alternativeTitles,
				--div--;LLL:EXT:address/Resources/Private/Language/locallang_db.xlf:notes,
                    notes,
				--div--;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.tabs.extended,'
        ],
        // person
        '1' => [
            'columnsOverrides' => [
                'bodytext' => [
                    'defaultExtras' => 'richtext:rte_transform[mode=ts_css]'
                ],
                'teaser' => [
                    'defaultExtras' => $teaserRteConfiguration
                ],
            ],
            'showitem' => 'l10n_parent, l10n_diffsource,
					--palette--;;paletteCore,
					
					--palette--;;palettePerson,
					--palette--;;paletteContact,
					
					teaser,
					--palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.palettes.editorial;paletteAuthor,
					--palette--;;paletteArchive,
					bodytext;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:rte_enabled_formlabel,
					
				--div--;LLL:EXT:address/Resources/Private/Language/locallang_db.xlf:location,
				    --palette--;;paletteLocation,
				    related,
					
                --div--;LLL:EXT:address/Resources/Private/Language/locallang_db.xlf:tx_address_domain_model_address.content_elements,content_elements,

				--div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.access,
					--palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.access;paletteAccess,

				--div--;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.tabs.options,categories,tags,
				--div--;LLL:EXT:address/Resources/Private/Language/locallang_db.xlf:tx_address_domain_model_address.tabs.relations,media,related_files,related_links,related_from,detail_pid,
				--div--;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.tabs.metadata,
					--palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.palettes.metatags;metatags,
					--palette--;LLL:EXT:address/Resources/Private/Language/locallang_db.xlf:tx_address_domain_model_address.palettes.alternativeTitles;alternativeTitles,
				--div--;LLL:EXT:address/Resources/Private/Language/locallang_db.xlf:notes,
                    notes,
				--div--;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.tabs.extended,'
        ],
        // company
        '2' => [
            'columnsOverrides' => [
                'bodytext' => [
                    'defaultExtras' => 'richtext:rte_transform[mode=ts_css]'
                ],
                'teaser' => [
                    'defaultExtras' => $teaserRteConfiguration
                ],
            ],
            'showitem' => 'l10n_parent, l10n_diffsource,
					title,
					--palette--;;paletteCore,
					--palette--;;paletteContact,
					--palette--;;paletteDate,teaser,
					bodytext;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:rte_enabled_formlabel,
                
                --div--;LLL:EXT:address/Resources/Private/Language/locallang_db.xlf:location,
                --palette--;;paletteLocation,
                
                
                --div--;LLL:EXT:address/Resources/Private/Language/locallang_db.xlf:tx_address_domain_model_address.content_elements,content_elements,


				--div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.access,
					--palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.access;paletteAccess,

				--div--;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.tabs.options,categories,tags,
				--div--;LLL:EXT:address/Resources/Private/Language/locallang_db.xlf:tx_address_domain_model_address.tabs.relations,media,related_files,related_links,related,related_from,
				--div--;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.tabs.metadata,
					--palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.palettes.metatags;metatags,
					--palette--;LLL:EXT:address/Resources/Private/Language/locallang_db.xlf:tx_address_domain_model_address.palettes.alternativeTitles;alternativeTitles,
				--div--;LLL:EXT:address/Resources/Private/Language/locallang_db.xlf:notes,
                    notes,
				--div--;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.tabs.extended,'
        ],
    ],
    'palettes' => [
        'paletteArchive' => [
            'showitem' => 'archive,',
        ],
        'paletteCore' => [
            'showitem' => 'type, sys_language_uid, hidden, istopaddress,',
        ],
        'paletteContact' => [
            'showitem' => 'phone, fax, --linebreak--, email, direct_contact,',
        ],
        'paletteLocation' => [
            'showitem' => 'address, building, --linebreak--, zip, city, country, --linebreak--, inline_map, --linebreak--, longitude, latitude,',
        ],
        'palettePerson' => [
            'showitem' => 'first_name, middle_name, last_name,--linebreak--,academic_title, append_academic_title, position,birthday,',
        ],
        'paletteNavtitle' => [
            'showitem' => 'alternative_title,path_segment',
        ],
        'paletteAccess' => [
            'showitem' => 'starttime;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:starttime_formlabel,
					endtime;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:endtime_formlabel,
					--linebreak--,editlock,',
        ],
        'paletteMetatags' => [
            'showitem' => 'keywords,--linebreak--,description,',
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
        $tx_address_domain_model_address['columns']['categories']['config']['foreign_table_where'] = $categoryRestriction .
            $tx_address_domain_model_address['columns']['categories']['config']['foreign_table_where'];
    }
}

if (!$configuration->getContentElementRelation()) {
    unset($tx_address_domain_model_address['columns']['content_elements']);
}

if (!\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('news')) {
    unset($tx_address_domain_model_address['columns']['related_news']);
}

return $tx_address_domain_model_address;
