<?php

namespace WapplerSystems\Address\Utility;

/**
 * This file is part of the "address" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use WapplerSystems\Address\Domain\Model\Dto\AddressDemand;

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
    protected static array $processedContentRecords = [];

    /**
     * Marks as cObj as processed.
     *
     * @param ContentObjectRenderer $cObj
     */
    public function markContentRecordAsProcessed(ContentObjectRenderer $cObj): void
    {
        $key = 'tt_content_' . $cObj->data['uid'];
        self::$processedContentRecords[$key] = true;
    }

    /**
     * Checks if a cObj has already added cache tags.
     *
     * @param ContentObjectRenderer $cObj
     * @return bool
     */
    public function isContentRecordAlreadyProcessed(ContentObjectRenderer $cObj): bool
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
    public static function addCacheTagsByAddressRecords(array $addressRecords): void
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
     * @param AddressDemand $demand
     */
    public static function addPageCacheTagsByDemandObject(AddressDemand $demand)
    {
        $cacheTags = [];
        if ($demand->getStoragePage()) {
            // Add cache tags for each storage page
            foreach ($demand->getStoragePage() as $pageId) {
                $cacheTags[] = 'tx_address_pid_' . $pageId;
            }
        }
        if (count($cacheTags) > 0) {
            $GLOBALS['TSFE']->addCacheTags($cacheTags);
        }
    }
}
