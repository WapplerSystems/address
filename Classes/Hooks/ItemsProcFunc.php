<?php

namespace WapplerSystems\Address\Hooks;

/**
 * This file is part of the "address" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */
use WapplerSystems\Address\Utility\TemplateLayout;
use TYPO3\CMS\Backend\Utility\BackendUtility as BackendUtilityCore;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Userfunc to render alternative label for media elements
 */
class ItemsProcFunc
{

    /** @var TemplateLayout $templateLayoutsUtility */
    protected $templateLayoutsUtility;

    /**
     * ItemsProcFunc constructor.
     * @param TemplateLayout $templateLayout
     */
    public function __construct(
        TemplateLayout $templateLayout
    ) {
        $this->templateLayoutsUtility = $templateLayout;
    }

    /**
     * Itemsproc function to extend the selection of templateLayouts in the plugin
     *
     * @param array &$config configuration array
     */
    public function user_templateLayout(array &$config): void
    {
        $currentColPos = $config['flexParentDatabaseRow']['colPos'];
        $pageId = $this->getPageId($config['flexParentDatabaseRow']['pid']);

        if ($pageId > 0) {
            $templateLayouts = $this->templateLayoutsUtility->getAvailableTemplateLayouts($pageId);

            $templateLayouts = $this->reduceTemplateLayouts($templateLayouts, $currentColPos);
            foreach ($templateLayouts as $layout) {
                $additionalLayout = [
                    htmlspecialchars($this->getLanguageService()->sL($layout[0])),
                    $layout[1],
                ];
                array_push($config['items'], $additionalLayout);
            }
        }
    }

    /**
     * Reduce the template layouts by the ones that are not allowed in given colPos
     *
     * @param array $templateLayouts
     * @param int $currentColPos
     * @return array
     */
    protected function reduceTemplateLayouts($templateLayouts, $currentColPos): array
    {
        $currentColPos = (int)$currentColPos;
        $restrictions = [];
        $allLayouts = [];
        foreach ($templateLayouts as $key => $layout) {
            if (is_array($layout[0])) {
                if (isset($layout[0]['allowedColPos']) && str_ends_with((string)$layout[1], '.')) {
                    $layoutKey = substr($layout[1], 0, -1);
                    $restrictions[$layoutKey] = GeneralUtility::intExplode(',', $layout[0]['allowedColPos'], true);
                }
            } else {
                $allLayouts[$key] = $layout;
            }
        }
        if (!empty($restrictions)) {
            foreach ($restrictions as $restrictedIdentifier => $restrictedColPosList) {
                if (!in_array($currentColPos, $restrictedColPosList, true)) {
                    unset($allLayouts[$restrictedIdentifier]);
                }
            }
        }

        return $allLayouts;
    }

    /**
     * Modifies the select box of orderBy-options as a category menu
     * needs different ones then a address action
     *
     * @param array &$config configuration array
     */
    public function user_orderBy(array &$config)
    {
        $row = $this->getContentElementRow($config['row']['uid']);

        // check if the record has been saved once
        if (is_array($row) && !empty($row['pi_flexform'])) {
            $flexformConfig = GeneralUtility::xml2array($row['pi_flexform']);

            // check if there is a flexform configuration
            if (isset($flexformConfig['data']['sDEF']['lDEF'])) {
                $selectedPlugin = strtolower($row['CType']) ?? '';
                // check for selected plugin
                if ($selectedPlugin === 'address_categorylist') {
                    $newItems = $GLOBALS['TYPO3_CONF_VARS']['EXT']['address']['orderByCategory'];
                } elseif ($selectedPlugin === 'address_taglist') {
                    $this->removeNonValidOrderFields($config, 'tx_address_domain_model_tag');
                    $newItems = $GLOBALS['TYPO3_CONF_VARS']['EXT']['address']['orderByTag'];
                } else {
                    $newItems = $GLOBALS['TYPO3_CONF_VARS']['EXT']['address']['orderByNews'];
                }
            }
        }

        // if a override configuration is found
        if (!empty($newItems)) {
            // remove default configuration
            $config['items'] = [];
            // empty default line
            array_push($config['items'], ['', '']);

            $newItemArray = GeneralUtility::trimExplode(',', $newItems, true);
            $languageKey = 'LLL:EXT:address/Resources/Private/Language/locallang_be.xlf:flexforms_general.orderBy.';
            foreach ($newItemArray as $item) {
                // label: if empty, key (=field) is used
                $label = $this->getLanguageService()->sL($languageKey . $item);
                if (empty($label)) {
                    $label = $item;
                }
                array_push($config['items'], [htmlspecialchars($label), $item]);
            }
        }
    }

    /**
     * Remove not valid fields from ordering
     *
     * @param array $config tca items
     * @param string $tableName table name
     */
    protected function removeNonValidOrderFields(array &$config, $tableName)
    {
        $allowedFields = array_keys($GLOBALS['TCA'][$tableName]['columns']);

        foreach ($config['items'] as $key => $item) {
            if ($item[1] != '' && !in_array($item[1], $allowedFields)) {
                unset($config['items'][$key]);
            }
        }
    }

    /**
     * Modifies the selectbox of available actions
     *
     * @param array &$config
     */
    public function user_switchableControllerActions(array &$config)
    {
        if (!empty($GLOBALS['TYPO3_CONF_VARS']['EXT']['address']['switchableControllerActions']['list'])) {
            $configuration = (int)$GLOBALS['TYPO3_CONF_VARS']['EXT']['address']['switchableControllerActions']['list'];
            switch ($configuration) {
                case 1:
                    $this->removeActionFromList($config, 'Address->list');
                    break;
                case 2:
                    $this->removeActionFromList($config, 'Address->list;Address->detail');
                    break;
                default:
            }
        }

        // Add additional actions
        if (isset($GLOBALS['TYPO3_CONF_VARS']['EXT']['address']['switchableControllerActions']['newItems'])
            && is_array($GLOBALS['TYPO3_CONF_VARS']['EXT']['address']['switchableControllerActions']['newItems'])
        ) {
            foreach ($GLOBALS['TYPO3_CONF_VARS']['EXT']['address']['switchableControllerActions']['newItems'] as $key => $label) {
                array_push($config['items'], [$this->getLanguageService()->sL($label), $key, '']);
            }
        }
    }

    /**
     * Remove given action from switchableControllerActions
     *
     * @param array $config available items
     * @param string $action action to be removed
     */
    private function removeActionFromList(array &$config, $action)
    {
        foreach ($config['items'] as $key => $item) {
            if ($item[1] === $action) {
                unset($config['items'][$key]);
                continue;
            }
        }
    }

    /**
     * Generate a select box of languages to choose an overlay
     *
     * @return string select box
     */
    public function user_categoryOverlay()
    {
        $html = '';

        $orderBy = $GLOBALS['TCA']['sys_language']['ctrl']['sortby'] ?
            $GLOBALS['TCA']['sys_language']['ctrl']['sortby'] :
            $GLOBALS['TYPO3_DB']->stripOrderBy($GLOBALS['TCA']['sys_language']['ctrl']['default_sortby']);

        $languages = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
            '*',
            'sys_language',
            '1=1 ' . BackendUtilityCore::deleteClause('sys_language'),
            '',
            $orderBy
        );

        // if any language is available
        if (count($languages) > 0) {
            $html = '<select name="data[addressoverlay]" id="field_addressoverlay" class="form-control">
						<option value="0">' . htmlspecialchars($this->getLanguageService()->sL('LLL:EXT:lang/locallang_general.xlf:LGL.default_value')) . '</option>';

            foreach ($languages as $language) {
                $selected = ((int)$GLOBALS['BE_USER']->uc['addressoverlay'] === (int)$language['uid']) ? ' selected="selected" ' : '';
                $html .= '<option ' . $selected . 'value="' . $language['uid'] . '">' . htmlspecialchars($language['title']) . '</option>';
            }

            $html .= '</select>';
        } else {
            $html .= htmlspecialchars($this->getLanguageService()->sL(
                'LLL:EXT:address/Resources/Private/Language/locallang_be.xlf:usersettings.no-languages-available')
            );
        }

        return $html;
    }

    /**
     * Get tt_content record
     *
     * @param int $uid
     * @return array
     */
    protected function getContentElementRow($uid)
    {
        return BackendUtilityCore::getRecord('tt_content', $uid);
    }

    /**
     * Get page id, if negative, then it is a "after record"
     *
     * @param int $pid
     * @return int
     */
    protected function getPageId($pid)
    {
        $pid = (int)$pid;

        if ($pid > 0) {
            return $pid;
        } else {
            $row = BackendUtilityCore::getRecord('tt_content', abs($pid), 'uid,pid');
            return $row['pid'];
        }
    }

    /**
     * Returns LanguageService
     *
     * @return \TYPO3\CMS\Lang\LanguageService
     */
    protected function getLanguageService()
    {
        return $GLOBALS['LANG'];
    }
}
