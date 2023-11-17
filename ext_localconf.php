<?php

use TYPO3\CMS\Backend\Form\Container\InlineRecordContainer;
use TYPO3\CMS\Backend\Form\FormDataProvider\DatabaseRowInitializeNew;
use TYPO3\CMS\Core\Cache\Backend\FileBackend;
use TYPO3\CMS\Core\Cache\Backend\Typo3DatabaseBackend;
use TYPO3\CMS\Core\Cache\Frontend\PhpFrontend;
use TYPO3\CMS\Core\Cache\Frontend\VariableFrontend;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;
use WapplerSystems\Address\Backend\Form\Element\MapElement;
use WapplerSystems\Address\Backend\FormDataProvider\AddressRowInitializeNew;
use WapplerSystems\Address\Command\AddressImportCommandController;
use WapplerSystems\Address\Controller\AddressController;
use WapplerSystems\Address\Controller\CategoryController;
use WapplerSystems\Address\Controller\TagController;
use WapplerSystems\Address\Hooks\DataHandler;
use WapplerSystems\Address\Hooks\Form\AddressHook;
use WapplerSystems\Address\Hooks\InlineElementHook;
use WapplerSystems\Address\Utility\ClassCacheManager;
use WapplerSystems\Address\Utility\ClassLoader;
use WapplerSystems\Address\Xclass\InlineRecordContainerForAddress;

$boot = static function (): void {

    ExtensionUtility::configurePlugin(
        'Address',
        'ListAndDetail',
        [
            AddressController::class => 'list,detail',
        ],
        [],
        ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
    );
    ExtensionUtility::configurePlugin(
        'Address',
        'List',
        [
            AddressController::class => 'list',
        ],
        [],
        ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
    );
    ExtensionUtility::configurePlugin(
        'Address',
        'Detail',
        [
            AddressController::class => 'detail',
        ],
        [],
        ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
    );
    ExtensionUtility::configurePlugin(
        'Address',
        'SearchForm',
        [
            AddressController::class => 'searchForm',
        ],
        [
            AddressController::class => 'searchForm',
        ],
        ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
    );
    ExtensionUtility::configurePlugin(
        'Address',
        'SearchResult',
        [
            AddressController::class => 'searchResult',
        ],
        [
            AddressController::class => 'searchResult',
        ],
        ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
    );

    ExtensionUtility::configurePlugin(
        'Address',
        'CategoryList',
        [
            CategoryController::class => 'list',
        ],
        [],
        ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
    );
    ExtensionUtility::configurePlugin(
        'Address',
        'TagList',
        [
            TagController::class => 'list',
        ],
        [],
        ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
    );

    $GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry'][1603209223] = [
        'nodeName' => 'map',
        'priority' => '10',
        'class' => MapElement::class,
    ];

    // Page module hook
    /*$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['cms/layout/class.tx_cms_layout.php']['list_type_Info']['address_pi1']['address'] =
        \WapplerSystems\Address\Hooks\PageLayoutView::class . '->getExtensionSummary';*/

    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['clearCachePostProc']['address_clearcache'] =
        DataHandler::class . '->clearCachePostProc';

    // Edit restriction for address records
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processCmdmapClass']['address'] =
        DataHandler::class;



    // Inline records hook
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tceforms_inline.php']['tceformsInlineHook']['address'] =
        InlineElementHook::class;

    // Xclass InlineRecordContainer
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][InlineRecordContainer::class] = [
        'className' => InlineRecordContainerForAddress::class,
    ];



    ExtensionManagementUtility::addPageTSConfig(
        "@import 'EXT:address/Configuration/TsConfig/ContentElementWizard.tsconfig'"
    );

    ExtensionManagementUtility::addPageTSConfig(
        "@import 'EXT:address/Configuration/TsConfig/Page/config.tsconfig'"
    );

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptSetup(trim('
    config.pageTitleProviders {
        address {
            provider = WapplerSystems\Address\Seo\AddressTitleProvider
            before = altPageTitle,record,seo
        }
    }
'));


    // Register cache frontend for proxy class generation
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['address'] = [
        'frontend' => PhpFrontend::class,
        'backend' => FileBackend::class,
        'groups' => [
            'all',
            'system',
        ],
        'options' => [
            'defaultLifetime' => 0,
        ]
    ];


    if (!is_array($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['address_geocoding'] ?? null)) {
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['address_geocoding'] = [
            'frontend' => VariableFrontend::class,
            'backend'  => Typo3DatabaseBackend::class,
        ];
    }

    if (class_exists(ClassLoader::class)) {
        ClassLoader::registerAutoloader();
    }
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['clearCachePostProc'][] =
        ClassCacheManager::class . '->reBuild';


    $GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['formDataGroup']['tcaDatabaseRecord'][AddressRowInitializeNew::class] = [
        'depends' => [
            DatabaseRowInitializeNew::class,
        ]
    ];
    ClassLoader::registerAutoloader();


    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['extbase']['commandControllers'][] = AddressImportCommandController::class;

    if (ExtensionManagementUtility::isLoaded('form')) {

        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/form']['beforeRendering'][1506563222] = AddressHook::class;

    }

};

$boot();
unset($boot);
