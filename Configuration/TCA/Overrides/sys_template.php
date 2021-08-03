<?php
defined('TYPO3_MODE') or die();


// TypoScript
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile('address', 'Configuration/TypoScript', 'Address');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile('address', 'Configuration/TypoScript/Sitemap', 'Address Sitemap');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile('address', 'Configuration/TypoScript/Form', 'Address Form');
