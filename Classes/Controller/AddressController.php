<?php
namespace WapplerSystems\Address\Controller;

/**
 * This file is part of the "address" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Property\TypeConverter\PersistentObjectConverter;
use TYPO3\CMS\Extbase\Reflection\ObjectAccess;
use TYPO3\CMS\Fluid\View\TemplateView;
use WapplerSystems\Address\Domain\Model\Address;
use WapplerSystems\Address\Domain\Model\Dto\AddressDemand;
use WapplerSystems\Address\Domain\Model\Dto\Search;
use WapplerSystems\Address\Utility\Cache;
use WapplerSystems\Address\Utility\Page;
use WapplerSystems\Address\Utility\TypoScript;

/**
 * Controller of address records
 *
 */
class AddressController extends AddressBaseController
{
    const SIGNAL_ADDRESS_LIST_ACTION = 'listAction';
    const SIGNAL_ADDRESS_DETAIL_ACTION = 'detailAction';
    const SIGNAL_ADDRESS_SEARCHFORM_ACTION = 'searchFormAction';
    const SIGNAL_ADDRESS_SEARCHRESULT_ACTION = 'searchResultAction';

    /**
     * @var \WapplerSystems\Address\Domain\Repository\AddressRepository
     */
    protected $addressRepository;

    /**
     * @var \WapplerSystems\Address\Domain\Repository\CategoryRepository
     */
    protected $categoryRepository;

    /**
     * @var \WapplerSystems\Address\Domain\Repository\TagRepository
     */
    protected $tagRepository;


    /** @var array */
    protected $ignoredSettingsForOverride = ['demandclass', 'orderbyallowed'];


    /**
     * @param \WapplerSystems\Address\Domain\Repository\AddressRepository $addressRepository
     */
    public function injectAddressRepository(\WapplerSystems\Address\Domain\Repository\AddressRepository $addressRepository)
    {
        $this->addressRepository = $addressRepository;
    }

    /**
     * @param \WapplerSystems\Address\Domain\Repository\CategoryRepository $categoryRepository
     */
    public function injectCategoryRepository(\WapplerSystems\Address\Domain\Repository\CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @param \WapplerSystems\Address\Domain\Repository\TagRepository $tagRepository
     */
    public function injectTagRepository(\WapplerSystems\Address\Domain\Repository\TagRepository $tagRepository)
    {
        $this->tagRepository = $tagRepository;
    }

    /**
     * Initializes the current action
     *
     */
    public function initializeAction()
    {
        if (isset($this->settings['format'])) {
            $this->request->setFormat($this->settings['format']);
        }
        // Only do this in Frontend Context
        if (!empty($GLOBALS['TSFE']) && is_object($GLOBALS['TSFE'])) {
            // We only want to set the tag once in one request, so we have to cache that statically if it has been done
            static $cacheTagsSet = false;

            /** @var $typoScriptFrontendController \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController */
            $typoScriptFrontendController = $GLOBALS['TSFE'];
            if (!$cacheTagsSet) {
                $typoScriptFrontendController->addCacheTags(['tx_address']);
                $cacheTagsSet = true;
            }
        }

        $this->categoryRepository->setRespectSysLanguageInFindInList((bool)$this->settings['respectSysLanguageInFindInList']);
    }

    /**
     * Create the demand object which define which records will get shown
     *
     * @param array $settings
     * @param string $class optional class which must be an instance of \WapplerSystems\Address\Domain\Model\Dto\AddressDemand
     * @return AddressDemand
     * @throws \UnexpectedValueException
     */
    protected function createDemandObjectFromSettings(
        $settings,
        $class = AddressDemand::class
    ) {
        $class = isset($settings['demandClass']) && !empty($settings['demandClass']) ? $settings['demandClass'] : $class;

        /* @var $demand AddressDemand */
        $demand = $this->objectManager->get($class, $settings);
        if (!$demand instanceof AddressDemand) {
            throw new \UnexpectedValueException(
                sprintf('The demand object must be an instance of \WapplerSystems\\Address\\Domain\\Model\\Dto\\AddressDemand, but %s given!',
                    $class),
                1423157953);
        }

        $demand->setCategories(GeneralUtility::trimExplode(',', $settings['categories'], true));
        $demand->setCategoryConjunction($settings['categoryConjunction']);
        $demand->setIncludeSubCategories($settings['includeSubCategories']);
        $demand->setTags($settings['tags']);

        $demand->setTopAddressRestriction($settings['topAddressRestriction']);
        $demand->setTimeRestriction($settings['timeRestriction']);
        $demand->setTimeRestrictionHigh($settings['timeRestrictionHigh']);
        $demand->setArchiveRestriction($settings['archiveRestriction']);
        $demand->setExcludeAlreadyDisplayedAddress($settings['excludeAlreadyDisplayedAddress']);
        $demand->setHideIdList($settings['hideIdList']);

        if ($settings['orderBy']) {
            $demand->setOrder($settings['orderBy'] . ' ' . $settings['orderDirection']);
        }
        $demand->setOrderByAllowed($settings['orderByAllowed']);

        $demand->setTopAddressFirst($settings['topAddressFirst']);

        $demand->setLimit($settings['limit']);
        $demand->setOffset($settings['offset']);

        $demand->setSearchFields($settings['search']['fields']);

        $demand->setStoragePage(Page::extendPidListByChildren($settings['startingpoint'],
            $settings['recursive']));
        return $demand;
    }

    /**
     * Overwrites a given demand object by an propertyName =>  $propertyValue array
     *
     * @param AddressDemand $demand
     * @param array $overwriteDemand
     * @return AddressDemand
     * @throws \InvalidArgumentException
     */
    protected function overwriteDemandObject($demand, $overwriteDemand)
    {
        foreach ($this->ignoredSettingsForOverride as $property) {
            unset($overwriteDemand[$property]);
        }

        foreach ($overwriteDemand as $propertyName => $propertyValue) {
            if (\in_array(strtolower($propertyName), $this->ignoredSettingsForOverride, true)) {
                continue;
            }
            if ($propertyValue !== '' || $this->settings['allowEmptyStringsForOverwriteDemand']) {
                ObjectAccess::setProperty($demand, $propertyName, $propertyValue);
            }
        }
        return $demand;
    }

    /**
     * Output a list view of address
     *
     * @param array $overwriteDemand
     * @throws \InvalidArgumentException
     * @throws \UnexpectedValueException
     */
    public function listAction(array $overwriteDemand = null)
    {
        $demand = $this->createDemandObjectFromSettings($this->settings);
        $demand->setActionAndClass(__METHOD__, __CLASS__);

        if ($overwriteDemand !== null && (int)$this->settings['disableOverrideDemand'] !== 1) {
            $demand = $this->overwriteDemandObject($demand, $overwriteDemand);
        }
        $addressRecords = $this->addressRepository->findDemanded($demand);

        $assignedValues = [
            'addresses' => $addressRecords,
            'overwriteDemand' => $overwriteDemand,
            'demand' => $demand,
        ];

        if ($demand->getCategories() !== '') {
            $categoriesList = $demand->getCategories();
            if (!\is_array($categoriesList)) {
                $categoriesList = GeneralUtility::trimExplode(',', $categoriesList);
            }
            if (!empty($categoriesList)) {
                $assignedValues['categories'] = $this->categoryRepository->findByIdList($categoriesList);
            }
        }

        if ($demand->getTags() !== '') {
            $tagList = $demand->getTags();
            if (!\is_array($tagList)) {
                $tagList = GeneralUtility::trimExplode(',', $tagList);
            }
            if (null !== $tagList) {
                $assignedValues['tags'] = $this->tagRepository->findByIdList($tagList);
            }
        }
        $assignedValues = $this->emitActionSignal('AddressController', self::SIGNAL_ADDRESS_LIST_ACTION, $assignedValues);
        $this->view->assignMultiple($assignedValues);

        Cache::addPageCacheTagsByDemandObject($demand);
    }

    /**
     * Single view of a address record
     *
     * @param Address $address address item
     * @param int $currentPage current page for optional pagination
     * @return void
     * @throws \UnexpectedValueException
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\NoSuchArgumentException
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\UnsupportedRequestTypeException
     * @throws \TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotException
     * @throws \TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotReturnException
     */
    public function detailAction(Address $address = null, $currentPage = 1)
    {
        if ($address === null) {
            $previewAddressId = ((int)$this->settings['singleAddress'] > 0) ? $this->settings['singleAddress'] : 0;
            if ($this->request->hasArgument('address_preview')) {
                $previewAddressId = (int)$this->request->getArgument('address_preview');
            }

            if ($previewAddressId > 0) {
                if ($this->isPreviewOfHiddenRecordsEnabled()) {
                    $GLOBALS['TSFE']->showHiddenRecords = true;
                    $address = $this->addressRepository->findByUid($previewAddressId, false);
                } else {
                    $address = $this->addressRepository->findByUid($previewAddressId);
                }
            }
        }

        if (is_a($address,
                Address::class) && $this->settings['detail']['checkPidOfAddressRecord']
        ) {
            $address = $this->checkPidOfAddressRecord($address);
        }

        if ($address === null && isset($this->settings['detail']['errorHandling'])) {
            $errorContent = $this->handleNoAddressFoundError($this->settings['detail']['errorHandling']);
            if ($errorContent) {
                return $errorContent;
            }
        }

        $demand = $this->createDemandObjectFromSettings($this->settings);
        $demand->setActionAndClass(__METHOD__, __CLASS__);

        $assignedValues = [
            'addressItem' => $address,
            'currentPage' => (int)$currentPage,
            'demand' => $demand,
        ];

        $assignedValues = $this->emitActionSignal('AddressController', self::SIGNAL_ADDRESS_DETAIL_ACTION, $assignedValues);
        $this->view->assignMultiple($assignedValues);

        Page::setRegisterProperties($this->settings['detail']['registerProperties'], $address);
        if ($address !== null && is_a($address, Address::class)) {
            Cache::addCacheTagsByAddressRecords([$address]);
        }
    }


    /**
     * Checks if the address pid could be found in the startingpoint settings of the detail plugin and
     * if the pid could not be found it return NULL instead of the address object.
     *
     * @param Address $address
     * @return null|Address
     * @throws \TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotException
     * @throws \TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotReturnException
     */
    protected function checkPidOfAddressRecord(Address $address)
    {
        $allowedStoragePages = GeneralUtility::trimExplode(
            ',',
            Page::extendPidListByChildren(
                $this->settings['startingpoint'],
                $this->settings['recursive']
            ),
            true
        );
        if (\count($allowedStoragePages) > 0 && !in_array($address->getPid(), $allowedStoragePages)) {
            $this->signalSlotDispatcher->dispatch(
                __CLASS__,
                'checkPidOfAddressRecordFailedInDetailAction',
                [
                    'address' => $address,
                    'addressController' => $this
                ]
            );
            $address = null;
        }
        return $address;
    }

    /**
     * Checks if preview is enabled either in TS or FlexForm
     *
     * @return bool
     */
    protected function isPreviewOfHiddenRecordsEnabled()
    {
        if (!empty($this->settings['previewHiddenRecords']) && $this->settings['previewHiddenRecords'] == 2) {
            $previewEnabled = !empty($this->settings['enablePreviewOfHiddenRecords']);
        } else {
            $previewEnabled = !empty($this->settings['previewHiddenRecords']);
        }
        return $previewEnabled;
    }


    /**
     * Display the search form
     *
     * @param Search $search
     * @param array $overwriteDemand
     * @throws \InvalidArgumentException
     * @throws \UnexpectedValueException
     */
    public function searchFormAction(
        Search $search = null,
        array $overwriteDemand = []
    ) {
        $demand = $this->createDemandObjectFromSettings($this->settings);
        $demand->setActionAndClass(__METHOD__, __CLASS__);

        if ((int)$this->settings['disableOverrideDemand'] !== 1 && $overwriteDemand !== null) {
            $demand = $this->overwriteDemandObject($demand, $overwriteDemand);
        }

        if (null === $search) {
            $search = $this->objectManager->get(Search::class);
        }
        $search->setSettings($this->settings);
        $demand->setSearch($search);

        $assignedValues = [
            'search' => $search,
            'overwriteDemand' => $overwriteDemand,
            'demand' => $demand,
        ];

        $assignedValues = $this->emitActionSignal('AddressController', self::SIGNAL_ADDRESS_SEARCHFORM_ACTION,
            $assignedValues);
        $this->view->assignMultiple($assignedValues);
    }

    /**
     * Displays the search result
     *
     * @param Search $search
     * @param array $overwriteDemand
     * @throws \InvalidArgumentException
     * @throws \UnexpectedValueException
     */
    public function searchResultAction(
        Search $search = null,
        array $overwriteDemand = []
    ) {
        $demand = $this->createDemandObjectFromSettings($this->settings);
        $demand->setActionAndClass(__METHOD__, __CLASS__);

        if ($overwriteDemand !== null && (int)$this->settings['disableOverrideDemand'] !== 1) {
            $demand = $this->overwriteDemandObject($demand, $overwriteDemand);
        }

        if ($search !== null) {
            $search->setFields($this->settings['search']['fields']);
            $search->setSettings($this->settings);
            $demand->setSearch($search);
        }

        $assignedValues = [
            'addresses' => $this->addressRepository->findDemanded($demand),
            'overwriteDemand' => $overwriteDemand,
            'search' => $search,
            'demand' => $demand,
        ];

        $assignedValues = $this->emitActionSignal('AddressController', self::SIGNAL_ADDRESS_SEARCHRESULT_ACTION,
            $assignedValues);
        $this->view->assignMultiple($assignedValues);
    }

    /**
     * initialize search result action
     */
    public function initializeSearchResultAction()
    {
        $this->initializeSearchActions();
    }

    /**
     * Initialize search form action
     */
    public function initializeSearchFormAction()
    {
        $this->initializeSearchActions();
    }

    /**
     * Initialize searchForm and searchResult actions
     */
    protected function initializeSearchActions()
    {
        if ($this->arguments->hasArgument('search')) {
            $propertyMappingConfiguration = $this->arguments['search']->getPropertyMappingConfiguration();
            $propertyMappingConfiguration->allowAllProperties();
            $propertyMappingConfiguration->setTypeConverterOption(PersistentObjectConverter::class, \TYPO3\CMS\Extbase\Property\TypeConverter\PersistentObjectConverter::CONFIGURATION_CREATION_ALLOWED, true);
        }
    }

    /***************************************************************************
     * helper
     **********************/

    /**
     * Injects the Configuration Manager and is initializing the framework settings
     *
     * @param ConfigurationManagerInterface $configurationManager Instance of the Configuration Manager
     * @throws \InvalidArgumentException
     */
    public function injectConfigurationManager(
        ConfigurationManagerInterface $configurationManager
    ) {
        $this->configurationManager = $configurationManager;

        $tsSettings = $this->configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK,
            'address',
            'address_pi1'
        );
        $originalSettings = $this->configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS
        );

        $propertiesNotAllowedViaFlexForms = ['orderByAllowed'];
        foreach ($propertiesNotAllowedViaFlexForms as $property) {
            $originalSettings[$property] = $tsSettings['settings'][$property];
        }

        // Use stdWrap for given defined settings
        /*
        if (isset($originalSettings['useStdWrap']) && !empty($originalSettings['useStdWrap'])) {
            $typoScriptService = GeneralUtility::makeInstance(TypoScriptService::class);
            $typoScriptArray = $typoScriptService->convertPlainArrayToTypoScriptArray($originalSettings);
            $stdWrapProperties = GeneralUtility::trimExplode(',', $originalSettings['useStdWrap'], true);
            foreach ($stdWrapProperties as $key) {
                if (\is_array($typoScriptArray[$key . '.'])) {
                    $originalSettings[$key] = $this->configurationManager->getContentObject()->stdWrap(
                        $originalSettings[$key],
                        $typoScriptArray[$key . '.']
                    );
                }
            }
        }*/

        // start override
        if (isset($tsSettings['settings']['overrideFlexformSettingsIfEmpty'])) {
            $typoScriptUtility = GeneralUtility::makeInstance(TypoScript::class);
            $originalSettings = $typoScriptUtility->override($originalSettings, $tsSettings);
        }

        if (\is_array($GLOBALS['TYPO3_CONF_VARS']['EXT']['address']['Controller/AddressController.php']['overrideSettings'])) {
            foreach ($GLOBALS['TYPO3_CONF_VARS']['EXT']['address']['Controller/AddressController.php']['overrideSettings'] as $_funcRef) {
                $_params = [
                    'originalSettings' => $originalSettings,
                    'tsSettings' => $tsSettings,
                ];
                $originalSettings = GeneralUtility::callUserFunction($_funcRef, $_params, $this);
            }
        }

        $this->settings = $originalSettings;
    }

    /**
     * Injects a view.
     * This function is for testing purposes only.
     *
     * @param TemplateView $view the view to inject
     */
    public function setView(TemplateView $view)
    {
        $this->view = $view;
    }
}
