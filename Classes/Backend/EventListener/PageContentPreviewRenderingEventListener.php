<?php
declare(strict_types=1);

namespace WapplerSystems\Address\Backend\EventListener;

use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Backend\Utility\BackendUtility as BackendUtilityCore;
use TYPO3\CMS\Backend\View\Event\PageContentPreviewRenderingEvent;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Type\Bitmask\Permission;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;
use WapplerSystems\Address\Utility\TemplateLayout;

final class PageContentPreviewRenderingEventListener
{

    /**
     * Table information
     */
    protected array $tableData = [];


    protected array $addresses = [];

    /**
     * Flexform information
     */
    public array $flexformData = [];


    public function __construct(
        readonly TemplateLayout $templateLayout,
        readonly IconFactory    $iconFactory
    )
    {
    }


    public function __invoke(PageContentPreviewRenderingEvent $event): void
    {
        $this->tableData = [];
        $this->addresses = [];

        if ($event->getTable() !== 'tt_content') {
            return;
        }

        if (str_starts_with($event->getRecord()['CType'], 'address_')) {
            $event->setPreviewContent($this->getContent($event->getRecord()));
        }

    }

    private function getContent(array $record): string
    {

        $flexformData = GeneralUtility::xml2array($record['pi_flexform']);
        if (is_string($flexformData)) {
            return 'ERROR: ' . htmlspecialchars($flexformData);
        }
        $this->flexformData = $flexformData;

        return $this->getExtensionSummary($record);
    }


    /**
     *
     */
    protected function getExtensionSummary(array $record)
    {
        switch ($record['CType']) {

            case 'address_list':
            case 'address_listanddetail':
                $this->getListAddressSettings();
                $this->getStartingPoint();
                $this->getCategorySettings();
                $this->getDetailPidSetting();
                $this->getTemplateLayoutSettings($record['pid']);
                $this->getArchiveSettings();
                $this->getTopAddressRestrictionSetting();
                $this->getOrderSettings();
                $this->getOffsetLimitSettings();
                $this->getListPidSetting();
                $this->getTagRestrictionSetting();
                break;
            case 'address_detail':
                $this->getSingleAddressSettings();
                $this->getDetailPidSetting();
                $this->getTemplateLayoutSettings($record['pid']);
                break;
            case 'category_list':
                $this->getCategorySettings(false);
                $this->getTemplateLayoutSettings($record['pid']);
                break;
            case 'tag_list':
                $this->getStartingPoint();
                $this->getListPidSetting();
                $this->getOrderSettings();
                $this->getTemplateLayoutSettings($record['pid']);
                break;
            default:
                $this->getTemplateLayoutSettings($record['pid']);
        }

        // for all views
        $this->getOverrideDemandSettings();

        return $this->renderSettingsAsTable($record['uid']);
    }


    protected function getListAddressSettings()
    {
        if ($this->getFieldFromFlexform('settings.selectedAddresses') === null) {
            return;
        }
        $addressRecords = GeneralUtility::intExplode(',', $this->getFieldFromFlexform('settings.selectedAddresses'), true);
        if (count($addressRecords) > 0) {

            foreach ($addressRecords as $id) {

                $addressRecord = BackendUtilityCore::getRecord('tx_address_domain_model_address', $id);

                if (is_array($addressRecord)) {
                    $pageRecord = BackendUtilityCore::getRecord('pages', $addressRecord['pid']);

                    if (is_array($pageRecord)) {
                        $content = $this->getRecordData($addressRecord['uid'], 'tx_address_domain_model_address');
                    } else {
                        $text = sprintf($this->getLanguageService()->sL('LLL:EXT:address/Resources/Private/Language/locallang_be.xlf:pagemodule.pageNotAvailable'),
                            $addressRecord['pid']);
                        $content = $this->generateCallout($text);
                    }
                } else {
                    $text = sprintf($this->getLanguageService()->sL('LLL:EXT:address/Resources/Private/Language/locallang_be.xlf:pagemodule.addressNotAvailable'),
                        $addressRecord);
                    $content = $this->generateCallout($text);
                }

                $this->tableData[] = [
                    $this->getLanguageService()->sL('LLL:EXT:address/Resources/Private/Language/locallang_be.xlf:flexforms_general.address'),
                    $content
                ];

            }
        }
    }

    /**
     * Render archive settings
     *
     */
    protected function getArchiveSettings()
    {
        $archive = $this->getFieldFromFlexform('settings.archiveRestriction');

        if (!empty($archive)) {
            $this->tableData[] = [
                $this->getLanguageService()->sL('LLL:EXT:address/Resources/Private/Language/locallang_be.xlf:flexforms_general.archiveRestriction'),
                $this->getLanguageService()->sL('LLL:EXT:address/Resources/Private/Language/locallang_be.xlf:flexforms_general.archiveRestriction.' . $archive)
            ];
        }
    }

    /**
     * Render single address settings
     *
     */
    public function getSingleAddressSettings()
    {
        $singleAddressRecord = (int)$this->getFieldFromFlexform('settings.singleAddress');

        if ($singleAddressRecord > 0) {
            $addressRecord = BackendUtilityCore::getRecord('tx_address_domain_model_address', $singleAddressRecord);

            if (is_array($addressRecord)) {
                $pageRecord = BackendUtilityCore::getRecord('pages', $addressRecord['pid']);

                if (is_array($pageRecord)) {
                    $content = $this->getRecordData($addressRecord['uid'], 'tx_address_domain_model_address');
                } else {
                    $text = sprintf($this->getLanguageService()->sL('LLL:EXT:address/Resources/Private/Language/locallang_be.xlf:pagemodule.pageNotAvailable'),
                        $addressRecord['pid']);
                    $content = $this->generateCallout($text);
                }
            } else {
                $text = sprintf($this->getLanguageService()->sL('LLL:EXT:address/Resources/Private/Language/locallang_be.xlf:pagemodule.addressNotAvailable'),
                    $singleAddressRecord);
                $content = $this->generateCallout($text);
            }

            $this->tableData[] = [
                $this->getLanguageService()->sL('LLL:EXT:address/Resources/Private/Language/locallang_be.xlf:flexforms_general.singleAddress'),
                $content
            ];
        }
    }

    /**
     * Render single address settings
     *
     */
    public function getDetailPidSetting()
    {
        $detailPid = (int)$this->getFieldFromFlexform('settings.detailPid', 'additional');

        if ($detailPid > 0) {
            $content = $this->getRecordData($detailPid);

            $this->tableData[] = [
                $this->getLanguageService()->sL('LLL:EXT:address/Resources/Private/Language/locallang_be.xlf:flexforms_additional.detailPid'),
                $content
            ];
        }
    }

    /**
     * Render listPid address settings
     *
     */
    public function getListPidSetting()
    {
        $listPid = (int)$this->getFieldFromFlexform('settings.listPid', 'additional');

        if ($listPid > 0) {
            $content = $this->getRecordData($listPid);

            $this->tableData[] = [
                $this->getLanguageService()->sL('LLL:EXT:address/Resources/Private/Language/locallang_be.xlf:flexforms_additional.listPid'),
                $content
            ];
        }
    }

    /**
     * Get the rendered page title including onclick menu
     *
     * @param int $detailPid
     * @return string
     * @deprecated use getRecordData() instead
     */
    public function getPageRecordData($detailPid)
    {
        return $this->getRecordData($detailPid, 'pages');
    }

    /**
     * @param int $id
     * @param string $table
     * @return string
     */
    public function getRecordData($id, $table = 'pages')
    {
        $record = BackendUtilityCore::getRecord($table, $id);

        if (is_array($record)) {
            $data = '<span data-toggle="tooltip" data-placement="top" data-title="id=' . $record['uid'] . '">'
                . $this->iconFactory->getIconForRecord($table, $record, Icon::SIZE_SMALL)->render()
                . '</span> ';
            $content = BackendUtilityCore::wrapClickMenuOnIcon($data, $table, $record['uid'], true, '',
                '+info,edit,history');

            $linkTitle = htmlspecialchars(BackendUtilityCore::getRecordTitle($table, $record));

            if ($table === 'pages') {
                $id = $record['uid'];
                $currentPageId = (int)GeneralUtility::_GET('id');
                $link = htmlspecialchars($this->getEditLink($record, $currentPageId));
                $switchLabel = $this->getLanguageService()->sL('LLL:EXT:address/Resources/Private/Language/locallang_be.xlf:pagemodule.switchToPage');
                $content .= ' <a href="#" data-toggle="tooltip" data-placement="top" data-title="' . $switchLabel . '" onclick=\'top.jump("' . $link . '", "web_layout", "web", ' . $id . ');return false\'>' . $linkTitle . '</a>';
            } else {
                $content .= $linkTitle;
            }
        } else {
            $text = sprintf($this->getLanguageService()->sL('LLL:EXT:address/Resources/Private/Language/locallang_be.xlf:pagemodule.recordNotAvailable'),
                $id);
            $content = $this->generateCallout($text);
        }

        return $content;
    }

    /**
     * Get order settings
     *
     */
    public function getOrderSettings()
    {
        $orderField = $this->getFieldFromFlexform('settings.orderBy');
        if (!empty($orderField)) {
            $text = $this->getLanguageService()->sL('LLL:EXT:address/Resources/Private/Language/locallang_be.xlf:flexforms_general.orderBy.' . $orderField);

            // Order direction (asc, desc)
            $orderDirection = $this->getOrderDirectionSetting();
            if ($orderDirection) {
                $text .= ', ' . strtolower($orderDirection);
            }

            // Top address first
            $topAddress = $this->getTopAddressFirstSetting();
            if ($topAddress) {
                $text .= '<br />' . $topAddress;
            }

            $this->tableData[] = [
                $this->getLanguageService()->sL('LLL:EXT:address/Resources/Private/Language/locallang_be.xlf:flexforms_general.orderBy'),
                $text
            ];
        }
    }

    /**
     * Get order direction
     *
     * @return string
     */
    public function getOrderDirectionSetting()
    {
        $text = '';

        $orderDirection = $this->getFieldFromFlexform('settings.orderDirection');
        if (!empty($orderDirection)) {
            $text = $this->getLanguageService()->sL('LLL:EXT:address/Resources/Private/Language/locallang_be.xlf:flexforms_general.orderDirection.' . $orderDirection);
        }

        return $text;
    }

    /**
     * Get topAddressFirst setting
     *
     * @return string
     */
    public function getTopAddressFirstSetting()
    {
        $text = '';
        $topAddressSetting = (int)$this->getFieldFromFlexform('settings.topAddressFirst', 'additional');
        if ($topAddressSetting === 1) {
            $text = $this->getLanguageService()->sL('LLL:EXT:address/Resources/Private/Language/locallang_be.xlf:flexforms_additional.topAddressFirst');
        }

        return $text;
    }

    /**
     * Render category settings
     *
     * @param bool $showCategoryMode show the category conjunction
     */
    public function getCategorySettings($showCategoryMode = true)
    {
        $categories = GeneralUtility::intExplode(',', $this->getFieldFromFlexform('settings.categories'), true);
        if (count($categories) > 0) {
            $categoriesOut = [];
            foreach ($categories as $id) {
                $categoriesOut[] = $this->getRecordData($id, 'sys_category');
            }

            $this->tableData[] = [
                $this->getLanguageService()->sL('LLL:EXT:address/Resources/Private/Language/locallang_be.xlf:flexforms_general.categories'),
                implode(', ', $categoriesOut)
            ];

            // Category mode
            if ($showCategoryMode) {
                $categoryModeSelection = $this->getFieldFromFlexform('settings.categoryConjunction');
                if (empty($categoryModeSelection)) {
                    $categoryMode = $this->getLanguageService()->sL('LLL:EXT:address/Resources/Private/Language/locallang_be.xlf:flexforms_general.categoryConjunction.all');
                } else {
                    $categoryMode = $this->getLanguageService()->sL('LLL:EXT:address/Resources/Private/Language/locallang_be.xlf:flexforms_general.categoryConjunction.' . $categoryModeSelection);
                }

                if (count($categories) > 0 && empty($categoryModeSelection)) {
                    $categoryMode = $this->generateCallout($categoryMode);
                } else {
                    $categoryMode = htmlspecialchars($categoryMode);
                }

                $this->tableData[] = [
                    $this->getLanguageService()->sL('LLL:EXT:address/Resources/Private/Language/locallang_be.xlf:flexforms_general.categoryConjunction'),
                    $categoryMode
                ];
            }

            $includeSubcategories = $this->getFieldFromFlexform('settings.includeSubCategories');
            if ($includeSubcategories) {
                $this->tableData[] = [
                    $this->getLanguageService()->sL('LLL:EXT:address/Resources/Private/Language/locallang_be.xlf:flexforms_general.includeSubCategories'),
                    '<i class="fa fa-check"></i>'
                ];
            }
        }
    }

    /**
     * Get the restriction for tags
     *
     */
    public function getTagRestrictionSetting()
    {
        $tags = GeneralUtility::intExplode(',', $this->getFieldFromFlexform('settings.tags', 'additional'), true);
        if (count($tags) === 0) {
            return;
        }

        $categoryTitles = [];
        foreach ($tags as $id) {
            $categoryTitles[] = $this->getRecordData($id, 'tx_address_domain_model_tag');
        }

        $this->tableData[] = [
            $this->getLanguageService()->sL('LLL:EXT:address/Resources/Private/Language/locallang_be.xlf:flexforms_additional.tags'),
            implode(', ', $categoryTitles)
        ];
    }

    /**
     * Render offset & limit configuration
     *
     */
    public function getOffsetLimitSettings()
    {
        $offset = $this->getFieldFromFlexform('settings.offset', 'additional');
        $limit = $this->getFieldFromFlexform('settings.limit', 'additional');
        $hidePagination = $this->getFieldFromFlexform('settings.hidePagination', 'additional');

        if ($offset) {
            $this->tableData[] = [
                $this->getLanguageService()->sL('LLL:EXT:address/Resources/Private/Language/locallang_be.xlf:flexforms_additional.offset'),
                $offset
            ];
        }
        if ($limit) {
            $this->tableData[] = [
                $this->getLanguageService()->sL('LLL:EXT:address/Resources/Private/Language/locallang_be.xlf:flexforms_additional.limit'),
                $limit
            ];
        }
        if ($hidePagination) {
            $this->tableData[] = [
                $this->getLanguageService()->sL('LLL:EXT:address/Resources/Private/Language/locallang_be.xlf:flexforms_additional.hidePagination'),
                '<i class="fa fa-check"></i>'
            ];
        }
    }

    /**
     * Render date menu configuration
     *
     */
    public function getDateMenuSettings()
    {
        $dateMenuField = $this->getFieldFromFlexform('settings.dateField');

        $this->tableData[] = [
            $this->getLanguageService()->sL('LLL:EXT:address/Resources/Private/Language/locallang_be.xlf:flexforms_general.dateField'),
            $this->getLanguageService()->sL('LLL:EXT:address/Resources/Private/Language/locallang_be.xlf:flexforms_general.dateField.' . $dateMenuField)
        ];
    }

    /**
     * Render time restriction configuration
     *
     */
    public function getTimeRestrictionSetting()
    {
        $timeRestriction = $this->getFieldFromFlexform('settings.timeRestriction');

        if (!empty($timeRestriction)) {
            $this->tableData[] = [
                $this->getLanguageService()->sL('LLL:EXT:address/Resources/Private/Language/locallang_be.xlf:flexforms_general.timeRestriction'),
                htmlspecialchars($timeRestriction)
            ];
        }

        $timeRestrictionHigh = $this->getFieldFromFlexform('settings.timeRestrictionHigh');
        if (!empty($timeRestrictionHigh)) {
            $this->tableData[] = [
                $this->getLanguageService()->sL('LLL:EXT:address/Resources/Private/Language/locallang_be.xlf:flexforms_general.timeRestrictionHigh'),
                htmlspecialchars($timeRestrictionHigh)
            ];
        }
    }

    /**
     * Render top address restriction configuration
     *
     */
    public function getTopAddressRestrictionSetting()
    {
        $topAddressRestriction = (int)$this->getFieldFromFlexform('settings.topAddressRestriction');
        if ($topAddressRestriction > 0) {
            $this->tableData[] = [
                $this->getLanguageService()->sL('LLL:EXT:address/Resources/Private/Language/locallang_be.xlf:flexforms_general.topAddressRestriction'),
                $this->getLanguageService()->sL('LLL:EXT:address/Resources/Private/Language/locallang_be.xlf:flexforms_general.topAddressRestriction.' . $topAddressRestriction)
            ];
        }
    }

    /**
     * Render template layout configuration
     *
     * @param int $pageUid
     */
    public function getTemplateLayoutSettings($pageUid)
    {
        $title = '';
        $field = $this->getFieldFromFlexform('settings.templateLayout', 'template');

        // Find correct title by looping over all options
        if (!empty($field)) {
            $layouts = $this->templateLayout->getAvailableTemplateLayouts($pageUid);
            foreach ($layouts as $layout) {
                if ((string)$layout[1] === (string)$field) {
                    $title = $layout[0];
                }
            }
        }

        if (!empty($title)) {
            $this->tableData[] = [
                $this->getLanguageService()->sL('LLL:EXT:address/Resources/Private/Language/locallang_be.xlf:flexforms_template.templateLayout'),
                $this->getLanguageService()->sL($title)
            ];
        }
    }

    /**
     * Get information if override demand setting is disabled or not
     *
     */
    public function getOverrideDemandSettings()
    {
        $field = $this->getFieldFromFlexform('settings.disableOverrideDemand', 'additional');

        if ($field == 1) {
            $this->tableData[] = [
                $this->getLanguageService()->sL(
                    'LLL:EXT:address/Resources/Private/Language/locallang_be.xlf:flexforms_additional.disableOverrideDemand'),
                '<i class="fa fa-check"></i>'
            ];
        }
    }

    /**
     * Get the startingpoint
     *
     */
    public function getStartingPoint()
    {
        $value = $this->getFieldFromFlexform('settings.startingpoint');

        if (!empty($value)) {
            $pageIds = GeneralUtility::intExplode(',', $value, true);
            $pagesOut = [];

            foreach ($pageIds as $id) {
                $pagesOut[] = $this->getRecordData($id, 'pages');
            }

            $recursiveLevel = (int)$this->getFieldFromFlexform('settings.recursive');
            $recursiveLevelText = '';
            if ($recursiveLevel === 250) {
                $recursiveLevelText = $this->getLanguageService()->sL('LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:recursive.I.5');
            } elseif ($recursiveLevel > 0) {
                $recursiveLevelText = $this->getLanguageService()->sL('LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:recursive.I.' . $recursiveLevel);
            }

            if (!empty($recursiveLevelText)) {
                $recursiveLevelText = '<br />' .
                    htmlspecialchars($this->getLanguageService()->sL('LLL:EXT:lang/locallang_general.xlf:LGL.recursive')) . ' ' .
                    $recursiveLevelText;
            }

            $this->tableData[] = [
                $this->getLanguageService()->sL('LLL:EXT:lang/locallang_general.xlf:LGL.startingpoint'),
                implode(', ', $pagesOut) . $recursiveLevelText
            ];
        }
    }

    /**
     * Render an alert box
     *
     * @param string $text
     * @return string
     */
    protected function generateCallout($text)
    {
        return '<div class="alert alert-warning">
            ' . htmlspecialchars($text) . '
        </div>';
    }

    /**
     * Render the settings as table for Web>Page module
     * System settings are displayed in mono font
     *
     * @param int $recordUid
     * @return string
     */
    protected function renderSettingsAsTable(int $recordUid = 0)
    {
        $pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);
        $pageRenderer->addCssFile('EXT:address/Resources/Public/CSS/Backend/PageLayoutView.css');

        $content = '';

        if ($this->addresses) {
            $view = GeneralUtility::makeInstance(StandaloneView::class);
            $view->setTemplatePathAndFilename(GeneralUtility::getFileAbsFileName('EXT:address/Resources/Private/Templates/Backend/ContentPreview/Addresses.html'));
            $view->assignMultiple([
                'addresses' => $this->addresses,
                'id' => $recordUid
            ]);
            $content .= $view->render();
        }
        if ($this->tableData) {
            $view = GeneralUtility::makeInstance(StandaloneView::class);
            $view->setTemplatePathAndFilename(GeneralUtility::getFileAbsFileName('EXT:address/Resources/Private/Templates/Backend/ContentPreview/Parameters.html'));
            $view->assignMultiple([
                'rows' => $this->tableData,
                'id' => $recordUid
            ]);
            $content .= $view->render();
        }
        return $content;
    }

    /**
     * Get field value from flexform configuration,
     * including checks if flexform configuration is available
     *
     * @param string $key name of the key
     * @param string $sheet name of the sheet
     * @return string|NULL if nothing found, value if found
     */
    public function getFieldFromFlexform($key, $sheet = 'sDEF')
    {
        $flexform = $this->flexformData;
        if (isset($flexform['data'])) {
            $flexform = $flexform['data'];
            if (is_array($flexform) && is_array($flexform[$sheet]) && is_array($flexform[$sheet]['lDEF'])
                && is_array($flexform[$sheet]['lDEF'][$key] ?? null) && isset($flexform[$sheet]['lDEF'][$key]['vDEF'])
            ) {
                return $flexform[$sheet]['lDEF'][$key]['vDEF'];
            }
        }

        return null;
    }

    protected function getEditLink(array $row, int $currentPageUid): string
    {
        $editLink = '';
        $localCalcPerms = $GLOBALS['BE_USER']->calcPerms(BackendUtilityCore::getRecord('pages', $row['uid']));
        $permsEdit = $localCalcPerms & Permission::PAGE_EDIT;
        if ($permsEdit) {
            $uriBuilder = GeneralUtility::makeInstance(UriBuilder::class);
            $returnUrl = $uriBuilder->buildUriFromRoute('web_layout', ['id' => $currentPageUid]);
            $editLink = $uriBuilder->buildUriFromRoute('web_layout', [
                'id' => $row['uid'],
                'returnUrl' => $returnUrl,
            ]);
        }
        return (string)$editLink;
    }

    /**
     * @return \TYPO3\CMS\Core\Localization\LanguageService
     */
    protected function getLanguageService(): LanguageService
    {
        return $GLOBALS['LANG'];
    }


}
