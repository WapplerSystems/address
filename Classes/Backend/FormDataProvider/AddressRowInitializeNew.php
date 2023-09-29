<?php

namespace WapplerSystems\Address\Backend\FormDataProvider;

/**
 * This file is part of the "address" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use WapplerSystems\Address\Utility\EmConfiguration;
use TYPO3\CMS\Backend\Form\FormDataProviderInterface;
use TYPO3\CMS\Core\Utility\VersionNumberUtility;

/**
 * Fill the address records with default values
 */
class AddressRowInitializeNew implements FormDataProviderInterface
{

    /** @var  EmConfiguration */
    protected $emConfiguration;

    public function __construct()
    {
        $this->emConfiguration = EmConfiguration::getSettings();
    }

    /**
     * @param array $result
     * @return array
     */
    public function addData(array $result)
    {
        if ($result['tableName'] !== 'tx_address_domain_model_address') {
            return $result;
        }

        if ($result['command'] === 'new') {
            $result = $this->fillDateField($result);
        }

        return $result;
    }

    /**
     * @param array $result
     * @return array
     */
    protected function fillDateField(array $result)
    {

        if (is_array($result['pageTsConfig']['tx_address.']['predefine.'] ?? null)) {
            if (isset($result['pageTsConfig']['tx_address.']['predefine.']['author']) && (int)$result['pageTsConfig']['tx_address.']['predefine.']['author'] === 1) {
                $result['databaseRow']['author'] = $GLOBALS['BE_USER']->user['realName'];
                $result['databaseRow']['author_email'] = $GLOBALS['BE_USER']->user['email'];
            }

            if (isset($result['pageTsConfig']['tx_address.']['predefine.']['archive'])) {
                $calculatedTime = strtotime($result['pageTsConfig']['tx_address.']['predefine.']['archive']);

                if ($calculatedTime !== false) {
                    $result['databaseRow']['archive'] = $calculatedTime;
                }
            }
        }

        return $result;
    }


}
