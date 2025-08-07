<?php

namespace WapplerSystems\Address\EventListener;

use TYPO3\CMS\Core\Configuration\Event\AfterFlexFormDataStructureParsedEvent;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ModifyFlexformEvent
{
    public function __invoke(AfterFlexFormDataStructureParsedEvent $event): void
    {

        if (!ExtensionManagementUtility::isLoaded('tagging')) {
            return;
        }

        $dataStructure = $event->getDataStructure();
        $identifier = $event->getIdentifier();

        if ($identifier['type'] === 'tca' && $identifier['tableName'] === 'tt_content' && $identifier['dataStructureKey'] === '*,address_list') {
            $file = GeneralUtility::getFileAbsFileName('EXT:address/Configuration/FlexForms/Patches/Tagging.xml');
            $content = file_get_contents($file);
            if ($content) {
                ArrayUtility::mergeRecursiveWithOverrule($dataStructure['sheets'], GeneralUtility::xml2array($content));
            }
        }

        $event->setDataStructure($dataStructure);
    }
}
