<?php

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;



// TypoScript
ExtensionManagementUtility::addStaticFile('address', 'Configuration/TypoScript', 'Address');
ExtensionManagementUtility::addStaticFile('address', 'Configuration/TypoScript/Sitemap', 'Address Sitemap');
ExtensionManagementUtility::addStaticFile('address', 'Configuration/TypoScript/Form', 'Address Form');
