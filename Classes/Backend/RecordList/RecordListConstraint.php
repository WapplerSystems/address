<?php
namespace WapplerSystems\Address\Backend\RecordList;

/**
 * This file is part of the "address" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */
use WapplerSystems\Address\Service\CategoryService;
use WapplerSystems\Address\Utility\ConstraintHelper;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Database\DatabaseConnection;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class for the list rendering of administration module
 */
class RecordListConstraint
{
    const TABLE = 'tx_news_domain_model_news';

    /**
     * Check if current module is the address administration module
     *
     * @return bool
     */
    public function isInAdministrationModule()
    {
        $vars = GeneralUtility::_GET('M');
        return $vars === 'web_NewsTxNewsM2';
    }

    public function extendQuery(array &$parameters, array $arguments)
    {
        // search word
        if (isset($arguments['searchWord']) && !empty($arguments['searchWord'])) {
            $words = GeneralUtility::trimExplode(' ', $arguments['searchWord'], true);
            $fields = ['title', 'teaser', 'bodytext'];
            $parameters['where'][] = $this->getDatabaseConnection()->searchQuery($words, $fields, self::TABLE);
        }
        // top address
        $topAddressSetting = (int)$arguments['topAddressRestriction'];
        if ($topAddressSetting > 0) {
            if ($topAddressSetting === 1) {
                $parameters['where'][] = 'istopaddress=1';
            } elseif ($topAddressSetting === 2) {
                $parameters['where'][] = 'istopaddress=0';
            }
        }

        // archived (1==active, 2==archived)
        $archived = (int)$arguments['archived'];
        if ($archived > 0) {
            $currentTime = $GLOBALS['EXEC_TIME'];
            if ($archived === 1) {
                $parameters['where'][] = '(archive > ' . $currentTime . ' OR archive=0)';
            } elseif ($archived === 2) {
                $parameters['where'][] = 'archive > 0 AND archive <' . $currentTime;
            }
        }

        // hidden
        $hidden = (int)$arguments['hidden'];
        if ($hidden > 0) {
            if ($hidden === 1) {
                $parameters['where'][] = 'hidden=1';
            } elseif ($hidden === 2) {
                $parameters['where'][] = 'hidden=0';
            }
        }

        // time constraint low
        if (isset($arguments['timeRestriction']) && !empty($arguments['timeRestriction'])) {
            try {
                $limit = ConstraintHelper::getTimeRestrictionLow($arguments['timeRestriction']);
                $parameters['where'][] = 'datetime >=' . $limit;
            } catch (\Exception $e) {
                // @todo add flash message
            }
        }

        // time constraint high
        if (isset($arguments['timeRestrictionHigh']) && !empty($arguments['timeRestrictionHigh'])) {
            try {
                $limit = ConstraintHelper::getTimeRestrictionHigh($arguments['timeRestrictionHigh']);
                $parameters['where'][] = 'datetime <=' . $limit;
            } catch (\Exception $e) {
                // @todo add flash message
            }
        }

        // categories
        if (isset($arguments['selectedCategories']) && is_array($arguments['selectedCategories'])) {
            $categoryMode = strtolower($arguments['categoryConjunction']);
            foreach ($arguments['selectedCategories'] as $key => $category) {
                if ((int)$category === 0) {
                    unset($arguments['selectedCategories'][$key]);
                }
            }
            if (!empty($arguments['selectedCategories'])) {
                if ((int)$arguments['includeSubCategories'] === 1) {
                    $categoryList = implode(',', $arguments['selectedCategories']);
                    $listWithSubCategories = CategoryService::getChildrenCategories($categoryList);
                    $arguments['selectedCategories'] = explode(',', $listWithSubCategories);
                }
                switch ($categoryMode) {
                    case 'and':
                        foreach ($arguments['selectedCategories'] as $category) {
                            $idList = $this->getAddressIdsOfCategory($category, $parameters['where']['pidSelect']);
                            if (empty($idList)) {
                                $parameters['where'][] = '1=2';
                            } else {
                                $parameters['where'][] = sprintf('uid IN(%s)', implode(',', $idList));
                            }
                        }
                        break;
                    case 'or':
                        $orConstraint = [];
                        foreach ($arguments['selectedCategories'] as $category) {
                            $idList = $this->getAddressIdsOfCategory($category, $parameters['where']['pidSelect']);
                            if (!empty($idList)) {
                                $orConstraint[] = sprintf('uid IN(%s)', implode(',', $idList));
                            }
                        }
                        if (empty($orConstraint)) {
                            $parameters['where'][] = '1=2';
                        } else {
                            $parameters['where'][] = implode(' OR ', $orConstraint);
                        }
                        break;
                    // @todo test that
                    case 'notor':
                        $orConstraint = [];
                        foreach ($arguments['selectedCategories'] as $category) {
                            $idList = $this->getAddressIdsOfCategory($category, $parameters['where']['pidSelect']);
                            if (!empty($idList)) {
                                $orConstraint[] = sprintf('uid IN(%s)', implode(',', $idList));
                            } else {
                                $orConstraint[] = '1=2';
                            }
                        }
                        if (empty($orConstraint)) {
                            $parameters['where'][] = '1=2';
                        } else {
                            $parameters['where'][] = implode(' NOT OR ', $orConstraint);
                        }
                        break;
                    case 'notand':
                        foreach ($arguments['selectedCategories'] as $category) {
                            $idList = $this->getAddressIdsOfCategory($category, $parameters['where']['pidSelect']);
                            if (!empty($idList)) {
                                $parameters['where'][] = sprintf('uid NOT IN(%s)', implode(',', $idList));
                            }
                        }
                        break;
                }
            }
        }

        // order
        if (isset($arguments['sortingField']) && isset($GLOBALS['TCA']['tx_address_domain_model_address']['columns'][$arguments['sortingField']])) {
            $direction = ($arguments['sortingDirection'] === 'asc' || $arguments['sortingDirection'] === 'desc') ? $arguments['sortingDirection'] : '';
            $parameters['orderBy'] = [[$arguments['sortingField'], $direction]];
        }
    }

    /**
     * @param int $categoryId
     * @param string $pidConstraint
     * @return array
     */
    protected function getAddressIdsOfCategory($categoryId, $pidConstraint = '')
    {
        $idList = [];

        if (!empty($pidConstraint)) {
            $pidConstraint = ' AND ' . $pidConstraint;
        }

        $res = $this->getDatabaseConnection()->sql_query(
            'SELECT tx_address_domain_model_address.uid, sys_category.title
            FROM tx_address_domain_model_address
                RIGHT JOIN `sys_category_record_mm` ON tx_address_domain_model_address.uid = sys_category_record_mm.uid_foreign
                RIGHT JOIN sys_category ON sys_category.uid = sys_category_record_mm.uid_local
            WHERE
              tx_address_domain_model_address.uid IS NOT NULL AND sys_category.uid=' . (int)$categoryId . $pidConstraint
            . BackendUtility::deleteClause('sys_category')
            . BackendUtility::deleteClause('tx_address_domain_model_address')
        );
        while ($row = $this->getDatabaseConnection()->sql_fetch_assoc($res)) {
            $idList[] = $row['uid'];
        }

        return $idList;
    }

    /**
     * @return DatabaseConnection
     */
    protected function getDatabaseConnection()
    {
        return $GLOBALS['TYPO3_DB'];
    }
}
