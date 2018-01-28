<?php
defined('TYPO3_MODE') or die();

$boot = function () {
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        'WapplerSystems.address',
        'Pi1',
        [
            'Address' => 'list,detail,searchForm,searchResult',
            'Category' => 'list',
            'Tag' => 'list',
        ],
        [
            'Address' => 'searchForm,searchResult',
        ]
    );

    // Page module hook
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['cms/layout/class.tx_cms_layout.php']['list_type_Info']['address_pi1']['address'] =
        \WapplerSystems\Address\Hooks\PageLayoutView::class . '->getExtensionSummary';

    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['clearCachePostProc']['address_clearcache'] =
        \WapplerSystems\Address\Hooks\DataHandler::class . '->clearCachePostProc';

    // Edit restriction for address records
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processCmdmapClass']['address'] =
        \WapplerSystems\Address\Hooks\DataHandler::class;

    // Hide content elements in page module for 7
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['typo3/class.db_list.inc']['makeQueryArray']['address'] =
        \WapplerSystems\Address\Hooks\Backend\RecordListQueryHook::class;
    // Hide content elements in page module for 8
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['TYPO3\\CMS\\Recordlist\\RecordList\\DatabaseRecordList']['buildQueryParameters'][]
        = \WapplerSystems\Address\Hooks\Backend\RecordListQueryHook8::class;

    // Inline records hook
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tceforms_inline.php']['tceformsInlineHook']['address'] =
        \WapplerSystems\Address\Hooks\InlineElementHook::class;

    // Xclass InlineRecordContainer
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][\TYPO3\CMS\Backend\Form\Container\InlineRecordContainer::class] = [
        'className' => \WapplerSystems\Address\Xclass\InlineRecordContainerForAddress::class,
    ];

    /* ===========================================================================
        Custom cache, done with the caching framework
    =========================================================================== */
    if (!is_array($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['cache_address_category'])) {
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['cache_address_category'] = [];
    }
    // Define string frontend as default frontend, this must be set with TYPO3 4.5 and below
    // and overrides the default variable frontend of 4.6
    if (!isset($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['cache_address_category']['frontend'])) {
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['cache_address_category']['frontend'] = \TYPO3\CMS\Core\Cache\Frontend\StringFrontend::class;
    }

    /* ===========================================================================
        Add TSconfig
    =========================================================================== */
    // For linkvalidator
    /*
    if (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('linkvalidator')) {
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig('<INCLUDE_TYPOSCRIPT: source="FILE:EXT:address/Configuration/TSconfig/Page/mod.linkvalidator.txt">');
    }
    if (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('guide')) {
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig('  <INCLUDE_TYPOSCRIPT: source="DIR:EXT:address/Configuration/TSconfig/Tours" extensions="ts">');
    }*/

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
        '<INCLUDE_TYPOSCRIPT: source="FILE:EXT:address/Configuration/TsConfig/ContentElementWizard.txt">
    ');

    /* ===========================================================================
        Hooks
    =========================================================================== */
    if (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('realurl')) {
        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/realurl/class.tx_realurl_autoconfgen.php']['extensionConfiguration']['address'] =
            \WapplerSystems\Address\Hooks\RealUrlAutoConfiguration::class . '->addAddressConfig';
    }

    // Register cache frontend for proxy class generation
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['address'] = [
        'frontend' => \TYPO3\CMS\Core\Cache\Frontend\PhpFrontend::class,
        'backend' => \TYPO3\CMS\Core\Cache\Backend\FileBackend::class,
        'groups' => [
            'all',
            'system',
        ],
        'options' => [
            'defaultLifetime' => 0,
        ]
    ];

    $GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['formDataGroup']['tcaDatabaseRecord'][\WapplerSystems\Address\Backend\FormDataProvider\AddressRowInitializeNew::class] = [
        'depends' => [
            \TYPO3\CMS\Backend\Form\FormDataProvider\DatabaseRowInitializeNew::class,
        ]
    ];
    \WapplerSystems\Address\Utility\ClassLoader::registerAutoloader();

    if (TYPO3_MODE === 'BE') {
        $icons = [
            'apps-pagetree-folder-contains-address' => 'ext-address-folder-tree.svg',
            'ext-address-wizard-icon' => 'plugin_wizard.svg',
            'ext-address-type-default' => 'address_domain_model_address.svg',
            'ext-address-type-person' => 'address_domain_model_address_person.svg',
            'ext-address-type-company' => 'address_domain_model_address_company.svg',
            'ext-address-tag' => 'address_domain_model_tag.svg',
            'ext-address-link' => 'address_domain_model_link.svg'
        ];
        $iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);
        foreach ($icons as $identifier => $path) {
            $iconRegistry->registerIcon(
                $identifier,
                \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
                ['source' => 'EXT:address/Resources/Public/Icons/' . $path]
            );
        }
    }

    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['extbase']['commandControllers'][] = \WapplerSystems\Address\Command\AddressImportCommandController::class;

    if (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('form')) {

        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/form']['beforeRendering'][1506563222] = \WapplerSystems\Address\Hooks\Form\AddressHook::class;

    }
};

$boot();
unset($boot);
