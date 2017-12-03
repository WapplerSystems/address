<?php

namespace WapplerSystems\Address\Utility;

/**
 * This file is part of the "address" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Cache Utility class
 *
 */
class Cache
{

    /**
     * Stack for processed cObjs which has added address relevant cache tags.
     * @var array
     */
    protected static $processedContentRecords = [];

    /**
     * Marks as cObj as processed.
     *
     * @param \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer $cObj
     */
    public function markContentRecordAsProcessed(\TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer $cObj)
    {
        $key = 'tt_content_' . $cObj->data['uid'];
        self::$processedContentRecords[$key] = true;
    }

    /**
     * Checks if a cObj has already added cache tags.
     *
     * @param \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer $cObj
     * @return bool
     */
    public function isContentRecordAlreadyProcessed(\TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer $cObj)
    {
        $key = 'tt_content_' . $cObj->data['uid'];
        return array_key_exists($key, self::$processedContentRecords);
    }

    /**
     * Adds cache tags to page cache by address-records.
     *
     * Following cache tags will be added to tsfe:
     * "tx_address_uid_[address:uid]"
     *
     * @param array $addressRecords array with address records
     */
    public static function addCacheTagsByAddressRecords(array $addressRecords)
    {
        $cacheTags = [];
        foreach ($addressRecords as $address) {
            // cache tag for each address record
            $cacheTags[] = 'tx_address_uid_' . $address->getUid();
        }
        if (count($cacheTags) > 0) {
            $GLOBALS['TSFE']->addCacheTags($cacheTags);
        }
    }

    /**
     * Adds page cache tags by used storagePages.
     * This adds tags with the scheme tx_address_pid_[address:pid]
     *
     * @param \WapplerSystems\Address\Domain\Model\Dto\AddressDemand $demand
     */
    public static function addPageCacheTagsByDemandObject(\WapplerSystems\Address\Domain\Model\Dto\AddressDemand $demand)
    {
        $cacheTags = [];
        if ($demand->getStoragePage()) {
            // Add cache tags for each storage page
            foreach (GeneralUtility::trimExplode(',', $demand->getStoragePage()) as $pageId) {
                $cacheTags[] = 'tx_address_pid_' . $pageId;
            }
        }
        if (count($cacheTags) > 0) {
            $GLOBALS['TSFE']->addCacheTags($cacheTags);
        }
    }
}
