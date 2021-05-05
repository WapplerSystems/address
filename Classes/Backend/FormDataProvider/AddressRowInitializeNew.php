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

        $result = $this->setTagListingId($result);

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
        if ($this->emConfiguration->getDateTimeRequired()) {
            $result['databaseRow']['datetime'] = $GLOBALS['EXEC_TIME'];
        }

        if (is_array($result['pageTsConfig']['tx_address.'])
            && is_array($result['pageTsConfig']['tx_address.']['predefine.'])
        ) {
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

    /**
     * @param array $result
     * @return array
     */
    protected function setTagListingId(array $result)
    {
        if (!is_array($result['pageTsConfig']['tx_address.']) || !isset($result['pageTsConfig']['tx_address.']['tagPid'])) {
            return $result;
        }
        $tagPid = (int)$result['pageTsConfig']['tx_address.']['tagPid'];
        if ($tagPid <= 0) {
            return $result;
        }

        if (VersionNumberUtility::convertVersionNumberToInteger(TYPO3_version) >= 8006000) {
            $result['processedTca']['columns']['tags']['config']['fieldControl']['listModule']['options']['pid'] = $tagPid;
        } else {
            $result['processedTca']['columns']['tags']['config']['wizards']['list']['params']['pid'] = $tagPid;
        }
        return $result;
    }
}
