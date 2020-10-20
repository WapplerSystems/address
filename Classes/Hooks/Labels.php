<?php

namespace WapplerSystems\Address\Hooks;

/**
 * This file is part of the "address" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */
use WapplerSystems\Address\Service\CategoryService;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Userfunc to get alternative label
 *
 */
class Labels
{

    /**
     * Generate additional label for category records
     * including the title of the parent category
     *
     * @param array $params
     */
    public function getUserLabelCategory(array &$params)
    {
        $showTranslationInformation = false;

        $getVars = GeneralUtility::_GET();
        if (isset($getVars['route']) && $getVars['route'] === '/record/edit'
            && isset($getVars['edit']) && is_array($getVars['edit'])
            && (isset($getVars['edit']['tt_content']) || isset($getVars['edit']['tx_address_domain_model_address']) || isset($getVars['edit']['sys_category']))
        ) {
            $showTranslationInformation = true;
        }

        if ($showTranslationInformation && is_array($params['row'])) {
            $params['title'] = CategoryService::translateCategoryRecord($params['row']['title'], $params['row']);
        } else {
            $params['title'] = $params['row']['title'];
        }
    }

    /**
     * Get address categories based on the address id
     *
     * @param int $addressUid
     * @param int $catMm
     * @return string list of categories
     */
    protected function getCategories($addressUid, $catMm)
    {
        if ($catMm == 0) {
            return '';
        }

        $catTitles = [];
        $res = $GLOBALS['TYPO3_DB']->exec_SELECT_mm_query(
            'sys_category.title as title',
            'tx_address_domain_model_address',
            'sys_category_mm',
            'sys_category',
            ' AND tx_address_domain_model_address.uid=' . (int)$addressUid
        );
        while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
            $catTitles[] = $row['title'];
        }
        $GLOBALS['TYPO3_DB']->sql_free_result($res);

        return implode(', ', $catTitles);
    }

    /**
     * Get the first filled field of a record
     *
     * @param string $fieldList comma separated list of fields
     * @param array $record record
     * @return string 1st used field
     */
    protected function getTitleFromFields($fieldList, $record = [])
    {
        $title = '';
        $fields = GeneralUtility::trimExplode(',', $fieldList, true);

        if (!is_array($record) || empty($record)) {
            return $title;
        }

        foreach ($fields as $fieldName) {
            if (empty($title) && isset($record[$fieldName]) && !empty($record[$fieldName])) {
                $title = $record[$fieldName];
            }
        }

        $title = $this->splitFileName($title);

        return $title;
    }

    /**
     * Split the filename
     *
     * @param string $title
     * @return string
     */
    protected function splitFileName($title)
    {
        $split = explode('|', $title);
        if (count($split) === 2 && $split[0] === $split[1]) {
            $title = $split[0];
        }

        return $title;
    }
}
