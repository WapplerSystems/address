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
 * Url Utility class
 *
 */
class Url
{

    /**
     * Prepend current url if url is relative
     *
     * @param string $url given url
     * @return string
     */
    public static function prependDomain($url)
    {
        if (!str_starts_with($url, GeneralUtility::getIndpEnv('TYPO3_SITE_URL'))) {
            $url = GeneralUtility::getIndpEnv('TYPO3_SITE_URL') . $url;
        }

        return $url;
    }
}
