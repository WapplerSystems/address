<?php

use TYPO3\CMS\Core\Utility\VersionNumberUtility;



    $config = $GLOBALS['TCA']['tx_address_domain_model_address']['columns']['media']['config']['foreign_types'];
    $GLOBALS['TCA']['tx_address_domain_model_address']['columns']['media']['config']['overrideChildTca']['types'] = $config;

    unset($GLOBALS['TCA']['tx_address_domain_model_address']['columns']['media']['config']['foreign_types']);

