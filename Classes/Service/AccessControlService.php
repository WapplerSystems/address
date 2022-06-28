<?php

namespace WapplerSystems\Address\Service;

/**
 * This file is part of the "address" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */
use WapplerSystems\Address\Utility\EmConfiguration;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Service for access control related stuff
 *
 */
class AccessControlService
{

    /**
     * Check if a user has access to all categories of a address record
     *
     * @param array $addressRecord
     * @return bool
     */
    public static function userHasCategoryPermissionsForRecord(array $addressRecord)
    {
        if (!EmConfiguration::getSettings()->getCategoryBeGroupTceFormsRestriction()) {
            return true;
        }

        if (self::getBackendUser()->isAdmin()) {
            // an admin may edit all address
            return true;
        }

        // If there are any categories with denied access, the user has no permission
        if (count(self::getAccessDeniedCategories($addressRecord))) {
            return false;
        }
        return true;
    }

    /**
     * Get an array with the uid and title of all categories the user doesn't have access to
     *
     * @param array $addressRecord
     * @return array
     */
    public static function getAccessDeniedCategories(array $addressRecord)
    {
        if (self::getBackendUser()->isAdmin()) {
            // an admin may edit all address so no categories without access
            return [];
        }

        // no category mounts set means access to all
        $backendUserCategories = self::getBackendUser()->getCategoryMountPoints();
        if ($backendUserCategories === []) {
            return [];
        }

        $catService = GeneralUtility::makeInstance(CategoryService::class);
        $subCategories = $catService->getChildrenCategories(implode(',', $backendUserCategories));
        if (!empty($subCategories)) {
            $backendUserCategories = explode(',', $subCategories);
        }

        $addressRecordCategories = self::getCategoriesForAddressRecord($addressRecord);

        // Remove categories the user has access to
        foreach ($addressRecordCategories as $key => $addressRecordCategory) {
            if (in_array($addressRecordCategory['uid'], $backendUserCategories)) {
                unset($addressRecordCategories[$key]);
            }
        }

        return $addressRecordCategories;
    }

    /**
     * Get all categories for a address record respecting l10n_mode
     *
     * @param array $addressRecord
     * @return array
     */
    public static function getCategoriesForAddressRecord($addressRecord)
    {
        // determine localization overlay mode to select categories either from parent or localized record
        if ($addressRecord['sys_language_uid'] > 0 && $addressRecord['l10n_parent'] > 0) {
            // localized version of a address record
            $categoryL10nMode = $GLOBALS['TCA']['tx_address_domain_model_address']['columns']['categories']['l10n_mode'];
            if ($categoryL10nMode === 'mergeIfNotBlank') {
                // mergeIfNotBlank: If there are categories in the localized version, take these, if not, inherit from parent
                $whereClause = 'tablenames=\'tx_address_domain_model_address\' AND uid_foreign=' . $addressRecord['uid'];
                $addressRecordCategoriesCount = self::getDatabaseConnection()->exec_SELECTcountRows('*',
                    'sys_category_record_mm', $whereClause, '', '', '', 'uid_local');
                if ($addressRecordCategoriesCount > 0) {
                    // take categories from localized version
                    $addressRecordUid = $addressRecord['uid'];
                } else {
                    // inherit categories from parent
                    $addressRecordUid = $addressRecord['l10n_parent'];
                }
            } elseif ($categoryL10nMode === 'exclude') {
                // exclude: The localized version inherits the categories of the parent
                $addressRecordUid = $addressRecord['l10n_parent'];
            } else {
                // noCopy/prefixLangTitle: no inheritance
                $addressRecordUid = $addressRecord['uid'];
            }
        } else {
            $addressRecordUid = $addressRecord['uid'];
        }

        $whereClause = 'AND sys_category_record_mm.tablenames="tx_address_domain_model_address" AND sys_category_record_mm.fieldname="categories" AND sys_category_record_mm.uid_foreign=' . $addressRecordUid .
            BackendUtility::deleteClause('sys_category') . BackendUtility::BEenableFields('sys_category');

        $res = self::getDatabaseConnection()->exec_SELECT_mm_query(
            'sys_category_record_mm.uid_local, sys_category.title',
            'sys_category',
            'sys_category_record_mm',
            'tx_address_domain_model_address',
            $whereClause
        );

        $categories = [];
        while (($row = self::getDatabaseConnection()->sql_fetch_assoc($res))) {
            $categories[] = [
                'uid' => $row['uid_local'],
                'title' => $row['title']
            ];
        }
        self::getDatabaseConnection()->sql_free_result($res);
        return $categories;
    }

    /**
     * @return \TYPO3\Cms\Core\Database\DatabaseConnection
     */
    protected static function getDatabaseConnection()
    {
        return $GLOBALS['TYPO3_DB'];
    }

    /**
     * Returns the current BE user.
     *
     * @return \TYPO3\CMS\Core\Authentication\BackendUserAuthentication
     */
    protected static function getBackendUser()
    {
        return $GLOBALS['BE_USER'];
    }
}
