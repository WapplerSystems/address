<?php
declare(strict_types=1);

namespace WapplerSystems\Address\Utility;

/**
 * This file is part of the "address" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * MapRenderer utility class
 */
class MapRenderer implements SingletonInterface
{

    /**
     * Get available map renderers for a certain page
     *
     * @param int $pageUid
     * @return array
     */
    public function getAvailableMapRenderers($pageUid)
    {
        $mapRenderers = [];

        // Check if the layouts are extended by ext_tables
        if (isset($GLOBALS['TYPO3_CONF_VARS']['EXT']['address']['mapRenderers'])
            && is_array($GLOBALS['TYPO3_CONF_VARS']['EXT']['address']['mapRenderers'])
        ) {
            $mapRenderers = $GLOBALS['TYPO3_CONF_VARS']['EXT']['address']['mapRenderers'];
        }

        // Add TsConfig values
        foreach ($this->getMapRenderersFromTsConfig($pageUid) as $templateKey => $title) {
            if (str_starts_with($title, '--div--')) {
                $optGroupParts = GeneralUtility::trimExplode(',', $title, true, 2);
                $title = $optGroupParts[1];
                $templateKey = $optGroupParts[0];
            }
            $mapRenderers[] = [$title, $templateKey];
        }

        return $mapRenderers;
    }

    /**
     * Get map renderers defined in TsConfig
     *
     * @param $pageUid
     * @return array
     */
    protected function getMapRenderersFromTsConfig($pageUid)
    {
        $mapRenderers = [];
        $pagesTsConfig = BackendUtility::getPagesTSconfig($pageUid);
        if (isset($pagesTsConfig['tx_address.']['mapRenderers.']) && is_array($pagesTsConfig['tx_address.']['mapRenderers.'])) {
            $mapRenderers = $pagesTsConfig['tx_address.']['mapRenderers.'];
        }
        return $mapRenderers;
    }
}
