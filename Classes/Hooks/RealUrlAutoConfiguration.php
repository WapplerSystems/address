<?php

namespace WapplerSystems\Address\Hooks;

/**
 * This file is part of the "address" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

/**
 * AutoConfiguration-Hook for RealURL
 *
 */
class RealUrlAutoConfiguration
{

    /**
     * Generates additional RealURL configuration and merges it with provided configuration
     *
     * @param       array $params Default configuration
     * @return      array Updated configuration
     */
    public function addAddressConfig($params)
    {

        // Check for proper unique key
        $postVar = (ExtensionManagementUtility::isLoaded('tt_address') ? 'tx_address' : 'address');

        return array_merge_recursive($params['config'], [
                'postVarSets' => [
                    '_DEFAULT' => [
                        $postVar => [
                            [
                                'GETvar' => 'tx_address_pi1[address]',
                                'lookUpTable' => [
                                    'table' => 'tx_address_domain_model_address',
                                    'id_field' => 'uid',
                                    'alias_field' => 'title',
                                    'useUniqueCache' => 1,
                                    'useUniqueCache_conf' => [
                                        'strtolower' => 1,
                                        'spaceCharacter' => '-',
                                    ],
                                ],
                            ],
                        ],
                    ]
                ]
            ]
        );
    }
}
