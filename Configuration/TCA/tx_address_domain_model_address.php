<?php

use TYPO3\CMS\Core\Utility\GeneralUtility;
use WapplerSystems\Address\Domain\Model\Dto\EmConfiguration;
use WapplerSystems\Address\Validation\Double6Validator;


/** @var EmConfiguration $configuration */
$configuration = GeneralUtility::makeInstance(EmConfiguration::class);


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
        'showRecordFieldList' => 'cruser_id,pid,sys_language_uid,l10n_parent,l10n_diffsource,hidden,starttime,endtime,title,teaser,bodytext,archive,categories,related,type,keywords,media,istopaddress,related_files,related_links,tags,path_segment,alternative_title,related_files,detail_pid'
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
        'abbreviation' => [
            'exclude' => false,
            'l10n_display' => 'defaultAsReadonly',
            'l10n_mode' => 'exclude',
            'label' => 'LLL:EXT:address/Resources/Private/Language/locallang_db.xlf:tx_address_domain_model_address.abbreviation',
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
        'teaser' => [
            'exclude' => true,
            'l10n_mode' => 'mergeIfNotBlank',
            'label' => 'LLL:EXT:address/Resources/Private/Language/locallang_db.xlf:tx_address_domain_model_address.teaser',
            'config' => [
                'type' => 'text',
                'cols' => 60,
                'rows' => 5,
                'enableRichtext' => $configuration->getRteForTeaser(),
            ]
        ],
        'latitude' => [
            'exclude' => false,
            'l10n_display' => 'defaultAsReadonly',
            'l10n_mode' => 'exclude',
            'label' => 'LLL:EXT:address/Resources/Private/Language/locallang_db.xlf:tx_address_domain_model_address.latitude',
            'config' => [
                'type' => 'input',
                'format' => 'float',
                'eval' => 'trim,'. Double6Validator::class,
                'precision' => 6,
                'nullable' => true,
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
                'format' => 'float',
                'precision' => 6,
                'eval' => 'trim,'. Double6Validator::class,
                'nullable' => true,
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
        'contact_person' => [
            'exclude' => true,
            'l10n_mode' => 'mergeIfNotBlank',
            'label' => 'LLL:EXT:address/Resources/Private/Language/locallang_db.xlf:tx_address_domain_model_address.contact_person',
            'config' => [
                'type' => 'input',
            ]
        ],
        'building' => [
            'exclude' => true,
            'l10n_mode' => 'mergeIfNotBlank',
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
                'renderType' => 'map',
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
            'l10n_mode' => 'prefixLangTitle',
            'l10n_cat' => 'text',
            'exclude' => 0,
            'label' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:bodytext_formlabel',
            'config' => [
                'type' => 'text',
                'cols' => 40,
                'rows' => 6,
                'enableRichtext' => true,
            ],
        ],
        'archive' => [
            'exclude' => true,
            'l10n_display' => 'defaultAsReadonly',
            'l10n_mode' => 'exclude',
            'label' => 'LLL:EXT:address/Resources/Private/Language/locallang_db.xlf:tx_address_domain_model_address.archive',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
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
        'istopaddress' => [
            'exclude' => true,
            'l10n_mode' => 'exclude',
            'l10n_display' => 'defaultAsReadonly',
            'label' => 'LLL:EXT:address/Resources/Private/Language/locallang_db.xlf:tx_address_domain_model_address.istopaddress',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
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
                'renderType' => 'checkboxToggle',
                'default' => 0
            ]
        ],
        'editlock' => [
            'exclude' => true,
            'displayCond' => 'HIDE_FOR_NON_ADMINS',
            'l10n_mode' => 'exclude',
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_tca.xlf:editlock',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
                'items' => [
                    [
                        0 => '',
                        1 => '',
                    ]
                ],
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
            'config' => [
                'type' => 'tag',
            ],
        ],
        'path_segment' => [
            'exclude' => true,
            'label' => 'LLL:EXT:address/Resources/Private/Language/locallang_db.xlf:tx_address_domain_model_address.path_segment',
            'displayCond' => 'VERSION:IS:false',
            'config' => [
                'type' => 'slug',
                'size' => 50,
                'generatorOptions' => [
                    'fields' => ['title','first_name','last_name'],
                    'fieldSeparator' => '-',
                    'replacements' => [
                        '/' => '-'
                    ],
                ],
                'fallbackCharacter' => '-',
                'eval' => $configuration->getSlugBehaviour(),
                'default' => ''
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
            'config' => [
                'type' => 'file',
                'allowed' => 'common-image-types',
                'appearance' => [
                    'createNewRelationLinkTitle' => 'LLL:EXT:address/Resources/Private/Language/locallang_db.xlf:tx_address_domain_model_address.media.add',
                    'showPossibleLocalizationRecords' => true,
                    'showRemovedLocalizationRecords' => true,
                    'showAllLocalizationLink' => true,
                    'showSynchronizationLink' => true
                ],
                'overrideChildTca' => [
                    'types' => [
                        \TYPO3\CMS\Core\Resource\FileType::UNKNOWN->value => [
                            'showitem' => '
                                    --palette--;LLL:EXT:core/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;addressPalette,
                                    --palette--;;imageoverlayPalette,
                                    --palette--;;filePalette',
                        ],
                        \TYPO3\CMS\Core\Resource\FileType::TEXT->value => [
                            'showitem' => '
                                    --palette--;LLL:EXT:core/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;addressPalette,
                                    --palette--;;imageoverlayPalette,
                                    --palette--;;filePalette',
                        ],
                        \TYPO3\CMS\Core\Resource\FileType::IMAGE->value => [
                            'showitem' => '
                                    --palette--;LLL:EXT:core/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;addressPalette,
                                    --palette--;;imageoverlayPalette,
                                    --palette--;;filePalette',
                        ],
                        \TYPO3\CMS\Core\Resource\FileType::AUDIO->value => [
                            'showitem' => '
                                    --palette--;LLL:EXT:core/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;addressPalette,
                                    --palette--;;audioOverlayPalette,
                                    --palette--;;filePalette',
                        ],
                        \TYPO3\CMS\Core\Resource\FileType::VIDEO->value => [
                            'showitem' => '
                                    --palette--;LLL:EXT:core/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;addressPalette,
                                    --palette--;;videoOverlayPalette,
                                    --palette--;;filePalette',
                        ],
                        \TYPO3\CMS\Core\Resource\FileType::APPLICATION->value => [
                            'showitem' => '
                                    --palette--;LLL:EXT:core/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;addressPalette,
                                    --palette--;;imageoverlayPalette,
                                    --palette--;;filePalette',
                        ],
                    ],
                ],
            ],
        ],
        'related_files' => [
            'exclude' => true,
            'l10n_mode' => 'mergeIfNotBlank',
            'label' => 'LLL:EXT:address/Resources/Private/Language/locallang_db.xlf:tx_address_domain_model_address.related_files',
            'config' => [
                'type' => 'file',
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
            ]
        ],
        'custom_marker_icon' => [
            'label' => 'LLL:EXT:address/Resources/Private/Language/locallang_db.xlf:tx_address_domain_model_address.custom_marker_icon',
            'config' => [
                'type' => 'group',
                'allowed' => 'tx_address_domain_model_markericon',
                'multiple' => false,
                'size' => 1,
                'maxitems' => 1,
                'appearance' => [
                    'showSynchronizationLink' => true,
                    'showAllLocalizationLink' => true,
                    'showPossibleLocalizationRecords' => true,
                ],
            ]
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
        'contacts' => [
            'label' => 'LLL:EXT:address/Resources/Private/Language/locallang_db.xlf:tx_address_domain_model_address.contacts',
            'config' => [
                'type' => 'inline',
                'foreign_table' => 'tx_address_domain_model_contact',
                'foreign_field' => 'address',
                'foreign_sortby' => 'sorting',
                'appearance' => [
                    'showSynchronizationLink' => true,
                    'showAllLocalizationLink' => true,
                    'showPossibleLocalizationRecords' => true,
                ],
            ],
        ],
    ],
    'types' => [
        // default address
        '0' => [
            'showitem' => 'l10n_parent, l10n_diffsource,
                    title,--palette--;;paletteCore,
                    --palette--;;palettePerson,
                    --palette--;;paletteContact,
                    --palette--;;paletteSlug,
                    teaser,
                    bodytext;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:bodytext_formlabel,
                    --palette--;;paletteArchive,
                --div--;LLL:EXT:address/Resources/Private/Language/locallang_db.xlf:location,
                    --palette--;;paletteLocation,
                    --palette--;LLL:EXT:address/Resources/Private/Language/locallang_db.xlf:tx_address_domain_model_address.marker;paletteMarker,

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
            'showitem' => 'l10n_parent, l10n_diffsource,
                    --palette--;;paletteCore,

                    --palette--;;palettePerson,
                    --palette--;;paletteContact,
                    --palette--;;paletteSlug,

                    teaser,
                    --palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.palettes.editorial;paletteAuthor,
                    --palette--;;paletteArchive,
                    bodytext;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:bodytext_formlabel,

                --div--;LLL:EXT:address/Resources/Private/Language/locallang_db.xlf:location,
                    --palette--;;paletteLocation,
                    --palette--;;paletteMarker,
                    related,

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
            'showitem' => 'l10n_parent, l10n_diffsource,
                    title,
                    --palette--;;paletteCore,
                    --palette--;;paletteContactCompany,
                    --palette--;;paletteDate,
                    --palette--;;paletteSlug,
                    teaser,
                    bodytext;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:bodytext_formlabel,

                --div--;LLL:EXT:address/Resources/Private/Language/locallang_db.xlf:location,
                --palette--;;paletteLocation,
                --palette--;;paletteMarker,

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
            'showitem' => 'contacts, --linebreak--, direct_contact,',
        ],
        'paletteContactCompany' => [
            'showitem' => 'contacts, --linebreak--, direct_contact, --linebreak--, contact_person,',
        ],
        'paletteLocation' => [
            'showitem' => 'address, building, --linebreak--, zip, city, region, country, --linebreak--, inline_map, --linebreak--, longitude, latitude,',
        ],
        'paletteMarker' => [
            'showitem' => 'custom_marker_icon',
        ],
        'palettePerson' => [
            'showitem' => 'first_name, middle_name, last_name, abbreviation, --linebreak--,academic_title, append_academic_title, position,birthday,',
        ],
        'paletteNavtitle' => [
            'showitem' => 'alternative_title',
        ],
        'paletteAccess' => [
            'showitem' => 'starttime;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:starttime_formlabel,
                    endtime;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:endtime_formlabel,
                    --linebreak--,editlock,',
        ],
        'paletteMetatags' => [
            'showitem' => 'keywords,--linebreak--,description,',
        ],
        'paletteSlug' => [
            'showitem' => '
                path_segment
            ',
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

if (!\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('news')) {
    unset($tx_address_domain_model_address['columns']['related_news']);
}

return $tx_address_domain_model_address;
