<?php

use TYPO3\CMS\Backend\Form\Container\InlineRecordContainer;
use TYPO3\CMS\Backend\Form\FormDataProvider\DatabaseRowInitializeNew;
use TYPO3\CMS\Core\Cache\Backend\FileBackend;
use TYPO3\CMS\Core\Cache\Backend\Typo3DatabaseBackend;
use TYPO3\CMS\Core\Cache\Frontend\PhpFrontend;
use TYPO3\CMS\Core\Cache\Frontend\VariableFrontend;
use TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider;
use TYPO3\CMS\Core\Imaging\IconRegistry;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
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
use WapplerSystems\Address\Updates\AddressSlugUpdater;
use WapplerSystems\Address\Utility\ClassCacheManager;
use WapplerSystems\Address\Utility\ClassLoader;
use WapplerSystems\Address\Xclass\InlineRecordContainerForAddress;

defined('TYPO3_MODE') or die();

$boot = function () {
    ExtensionUtility::configurePlugin(
        'WapplerSystems.address',
        'Pi1',
        [
            AddressController::class => 'list,detail,searchForm,searchResult',
            CategoryController::class => 'list',
            TagController::class => 'list',
        ],
        [
            AddressController::class => 'searchForm,searchResult',
        ]
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

    if (TYPO3_MODE === 'BE') {
        $icons = [
            'apps-pagetree-folder-contains-address' => 'ext-address-folder-tree.svg',
            'ext-address-wizard-icon' => 'plugin_wizard.svg',
            'ext-address-type-default' => 'address_domain_model_address.svg',
            'ext-address-type-person' => 'address_domain_model_address_person.svg',
            'ext-address-type-company' => 'address_domain_model_address_company.svg',
            'ext-address-tag' => 'address_domain_model_tag.svg',
            'ext-address-link' => 'address_domain_model_link.svg',
            'ext-address-contact-type-email' => 'address_domain_model_concact_email.svg',
            'ext-address-contact-type-telephone' => 'address_domain_model_concact_telephone.svg',
            'ext-address-contact-type-mobilephone' => 'address_domain_model_concact_mobilephone.svg',
            'ext-address-contact-type-website' => 'address_domain_model_concact_website.svg',
            'ext-address-contact-type-blog' => 'address_domain_model_concact_blog.svg',
            'ext-address-contact-type-xing' => 'address_domain_model_concact_xing.svg',
        ];
        $iconRegistry = GeneralUtility::makeInstance(IconRegistry::class);
        foreach ($icons as $identifier => $path) {
            $iconRegistry->registerIcon(
                $identifier,
                SvgIconProvider::class,
                ['source' => 'EXT:address/Resources/Public/Icons/' . $path]
            );
        }
    }

    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['extbase']['commandControllers'][] = AddressImportCommandController::class;

    if (ExtensionManagementUtility::isLoaded('form')) {

        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/form']['beforeRendering'][1506563222] = AddressHook::class;

    }

    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/install']['update']['addressSlug']
        = AddressSlugUpdater::class;
};

$boot();
unset($boot);
