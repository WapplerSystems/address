<?php

namespace WapplerSystems\Address\Controller;

/**
 * This file is part of the "address" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use GeorgRinger\NumberedPagination\NumberedPagination;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Pagination\SimplePagination;
use TYPO3\CMS\Core\Pagination\SlidingWindowPagination;
use TYPO3\CMS\Core\TypoScript\TypoScriptService;
use TYPO3\CMS\Core\Utility\DebugUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Http\ForwardResponse;
use TYPO3\CMS\Extbase\Property\TypeConverter\PersistentObjectConverter;
use TYPO3\CMS\Extbase\Reflection\ObjectAccess;
use TYPO3\CMS\Fluid\View\TemplateView;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;
use WapplerSystems\Address\Domain\Model\Address;
use WapplerSystems\Address\Domain\Model\Dto\AddressDemand;
use WapplerSystems\Address\Domain\Model\Dto\Search;
use WapplerSystems\Address\Domain\Repository\AddressRepository;
use WapplerSystems\Address\Domain\Repository\CategoryRepository;
use WapplerSystems\Address\Domain\Repository\TagRepository;
use WapplerSystems\Address\Event\AddressDetailActionEvent;
use WapplerSystems\Address\Event\AddressListActionEvent;
use WapplerSystems\Address\Pagination\QueryResultPaginator;
use WapplerSystems\Address\Seo\AddressTitleProvider;
use WapplerSystems\Address\Utility\Cache;
use WapplerSystems\Address\Utility\ClassCacheManager;
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


    /** @var array */
    protected $ignoredSettingsForOverride = ['demandclass', 'orderbyallowed'];

    /**
     * Original settings without any magic done by stdWrap and skipping empty values
     *
     * @var array
     */
    protected $originalSettings = [];


    public function __construct(readonly AddressRepository $addressRepository,
                                readonly CategoryRepository $categoryRepository,
                                readonly TagRepository $tagRepository)
    {
    }


    /**
     * Initializes the current action
     *
     */
    public function initializeAction(): void
    {
        GeneralUtility::makeInstance(ClassCacheManager::class)->reBuildSimple();
        $this->buildSettings();
        if (isset($this->settings['format'])) {
            $this->request = $this->request->withFormat($this->settings['format']);
        }
        // Only do this in Frontend Context
        if (!empty($GLOBALS['TSFE']) && is_object($GLOBALS['TSFE'])) {
            // We only want to set the tag once in one request, so we have to cache that statically if it has been done
            static $cacheTagsSet = false;

            /** @var $typoScriptFrontendController TypoScriptFrontendController */
            $typoScriptFrontendController = $GLOBALS['TSFE'];
            if (!$cacheTagsSet) {
                $typoScriptFrontendController->addCacheTags(['tx_address']);
                $cacheTagsSet = true;
            }
        }
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
    )
    {
        $class = isset($settings['demandClass']) && !empty($settings['demandClass']) ? $settings['demandClass'] : $class;

        /* @var $demand AddressDemand */
        $demand = GeneralUtility::makeInstance($class, $settings);
        if (!$demand instanceof AddressDemand) {
            throw new \UnexpectedValueException(
                sprintf('The demand object must be an instance of \\WapplerSystems\\Address\\Domain\\Model\\Dto\\AddressDemand, but %s given!',
                    $class),
                1423157953);
        }

        if (($this->settings['selectedAddresses'] ?? '') !== '') {
            $demand->setIds(explode(',', $this->settings['selectedAddresses']));
        }
        $demand->setCategories(GeneralUtility::trimExplode(',', $settings['categories'] ?? '', true));
        $demand->setCategoryConjunction($settings['categoryConjunction'] ?? '');
        $demand->setIncludeSubCategories($settings['includeSubCategories'] ?? '');
        if (($settings['tags'] ?? '') !== '') {
            // TODO
            $demand->setTags($settings['tags']);
        }

        $demand->setTopAddressRestriction($settings['topAddressRestriction'] ?? '');
        $demand->setArchiveRestriction($settings['archiveRestriction'] ?? '');
        $demand->setExcludeAlreadyDisplayedAddress($settings['excludeAlreadyDisplayedAddress'] ?? '');
        $demand->setHideIdList($settings['hideIdList'] ?? '');

        if ($settings['orderBy']) {
            $demand->setOrder($settings['orderBy'] . ' ' . $settings['orderDirection']);
        }
        $demand->setOrderByAllowed($settings['orderByAllowed']);

        $demand->setTopAddressFirst($settings['topAddressFirst']);

        $demand->setLimit((int)($settings['limit'] ?? 0));
        $demand->setOffset((int)($settings['offset'] ?? 0));

        $demand->setSearchFields($settings['search']['fields'] ?? '');

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
    protected function overwriteDemandObject($demand, $overwriteDemand): AddressDemand
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
     * @param array|null $overwriteDemand
     */
    public function listAction(array $overwriteDemand = null): ResponseInterface
    {
        $possibleRedirect = $this->forwardToDetailActionWhenRequested();
        if ($possibleRedirect) {
            return $possibleRedirect;
        }

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
            'categories' => null,
            'tags' => null,
            'settings' => $this->settings,
        ];

        if (count($demand->getCategories()) > 0) {
            $assignedValues['categories'] = $this->categoryRepository->findByIdList($demand->getCategories());
        }

        if ($demand->getTags() !== null && count($demand->getTags()) > 0) {
            $assignedValues['tags'] = $this->tagRepository->findByIdList($demand->getTags());
        }
        $event = $this->eventDispatcher->dispatch(new AddressListActionEvent($this, $assignedValues, $this->request));
        $this->view->assignMultiple($event->getAssignedValues());

        // pagination
        $paginationConfiguration = $this->settings['list']['paginate'] ?? [];
        $itemsPerPage = (int)(($paginationConfiguration['itemsPerPage'] ?? '') ?: 10);
        $maximumNumberOfLinks = (int)($paginationConfiguration['maximumNumberOfLinks'] ?? 0);

        $currentPage = max(1, $this->request->hasArgument('currentPage') ? (int)$this->request->getArgument('currentPage') : 1);
        $paginator = GeneralUtility::makeInstance(QueryResultPaginator::class, $event->getAssignedValues()['addresses'], $currentPage, $itemsPerPage, (int)($this->settings['limit'] ?? 0), (int)($this->settings['offset'] ?? 0));
        $paginationClass = $paginationConfiguration['class'] ?? SimplePagination::class;
        $pagination = $this->getPagination($paginationClass, $maximumNumberOfLinks, $paginator);

        $this->view->assign('pagination', [
            'currentPage' => $currentPage,
            'paginator' => $paginator,
            'pagination' => $pagination,
        ]);

        Cache::addPageCacheTagsByDemandObject($demand);
        return $this->htmlResponse();
    }

    /**
     * Single view of a address record
     *
     * @param Address $address address item
     * @param int $currentPage current page for optional pagination
     * @return void
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\NoSuchArgumentException
     */
    public function detailAction(Address $address = null, $currentPage = 1): ResponseInterface
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

        $event = $this->eventDispatcher->dispatch(new AddressDetailActionEvent($this, $assignedValues, $this->request));
        $assignedValues = $event->getAssignedValues();

        $this->view->assignMultiple($assignedValues);

        if ($address !== null) {
            Page::setRegisterProperties($this->settings['detail']['registerProperties'] ?? false, $address);
            Cache::addCacheTagsByAddressRecords([$address]);
            Cache::addCacheTagsByAddressRecords($address->getRelated()->toArray());

            if ($this->settings['detail']['pageTitle']['_typoScriptNodeValue'] ?? false) {
                $providerConfiguration = $this->settings['detail']['pageTitle'] ?? [];
                $providerClass = $providerConfiguration['provider'] ?? AddressTitleProvider::class;

                /** @var AddressTitleProvider $provider */
                $provider = GeneralUtility::makeInstance($providerClass);
                $provider->setTitleByAddress($address, $providerConfiguration);
            }
        } elseif (isset($this->settings['detail']['errorHandling'])) {
            $errorResponse = $this->handleNoAddressFoundError($this->settings['detail']['errorHandling'] ?? '');
            if ($errorResponse) {
                return $errorResponse;
            }
        }
        return $this->htmlResponse();
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
        array  $overwriteDemand = []
    )
    {
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
        array  $overwriteDemand = []
    )
    {
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


    public function buildSettings(): void
    {
        $tsSettings = $this->configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK,
            'news',
            'news_pi1'
        );
        $originalSettings = $this->configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS
        );

        $propertiesNotAllowedViaFlexForms = ['orderByAllowed'];
        foreach ($propertiesNotAllowedViaFlexForms as $property) {
            $originalSettings[$property] = ($tsSettings['settings'] ?? [])[$property] ?? ($originalSettings[$property] ?? '');
        }
        $this->originalSettings = $originalSettings;

        // Use stdWrap for given defined settings

        if (isset($originalSettings['useStdWrap']) && !empty($originalSettings['useStdWrap'])) {
            $typoScriptService = GeneralUtility::makeInstance(TypoScriptService::class);
            $typoScriptArray = $typoScriptService->convertPlainArrayToTypoScriptArray($originalSettings);
            $stdWrapProperties = GeneralUtility::trimExplode(',', $originalSettings['useStdWrap'], true);
            foreach ($stdWrapProperties as $key) {
                if (is_array($typoScriptArray[$key . '.'] ?? null)) {
                    $originalSettings[$key] = $this->configurationManager->getContentObject()->stdWrap(
                        $typoScriptArray[$key] ?? '',
                        $typoScriptArray[$key . '.']
                    );
                }
            }
        }

        // start override
        if (isset($tsSettings['settings']['overrideFlexformSettingsIfEmpty'])) {
            $typoScriptUtility = GeneralUtility::makeInstance(TypoScript::class);
            $originalSettings = $typoScriptUtility->override($originalSettings, $tsSettings);
        }

        foreach ($hooks = ($GLOBALS['TYPO3_CONF_VARS']['EXT']['address']['Controller/AddressController.php']['overrideSettings'] ?? []) as $_funcRef) {
            $_params = [
                'originalSettings' => $originalSettings,
                'tsSettings' => $tsSettings,
            ];
            $originalSettings = GeneralUtility::callUserFunction($_funcRef, $_params, $this);
        }

        $this->settings = $originalSettings;
    }

    /**
     * When list action is called along with a news argument, we forward to detail action.
     */
    protected function forwardToDetailActionWhenRequested(): ?ForwardResponse
    {
        if (!$this->isActionAllowed('detail')
            || !$this->request->hasArgument('address')
        ) {
            return null;
        }

        $forwardResponse = new ForwardResponse('detail');
        return $forwardResponse->withArguments(['address' => $this->request->getArgument('address')]);
    }

    /**
     * Checks whether an action is enabled in switchableControllerActions configuration
     *
     * @param string $action
     * @return bool
     */
    protected function isActionAllowed(string $action): bool
    {
        $frameworkConfiguration = $this->configurationManager->getConfiguration($this->configurationManager::CONFIGURATION_TYPE_FRAMEWORK);
        // @extensionScannerIgnoreLine
        $allowedActions = $frameworkConfiguration['controllerConfiguration']['Address']['actions'] ?? [];

        return \in_array($action, $allowedActions, true);
    }


    /**
     * @param $paginationClass
     * @param int $maximumNumberOfLinks
     * @param $paginator
     * @return \#o#Э#A#M#C\GeorgRinger\News\Controller\NewsController.getPagination.0|NumberedPagination|mixed|\Psr\Log\LoggerAwareInterface|string|SimplePagination|\TYPO3\CMS\Core\SingletonInterface
     */
    protected function getPagination($paginationClass, int $maximumNumberOfLinks, $paginator)
    {
        if (class_exists(NumberedPagination::class) && $paginationClass === NumberedPagination::class && $maximumNumberOfLinks) {
            $pagination = GeneralUtility::makeInstance(NumberedPagination::class, $paginator, $maximumNumberOfLinks);
        } elseif (class_exists(SlidingWindowPagination::class) && $paginationClass === SlidingWindowPagination::class && $maximumNumberOfLinks) {
            $pagination = GeneralUtility::makeInstance(SlidingWindowPagination::class, $paginator, $maximumNumberOfLinks);
        } elseif (class_exists($paginationClass)) {
            $pagination = GeneralUtility::makeInstance($paginationClass, $paginator);
        } else {
            $pagination = GeneralUtility::makeInstance(SimplePagination::class, $paginator);
        }
        return $pagination;
    }

}
