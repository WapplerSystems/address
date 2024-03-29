<?php

namespace WapplerSystems\Address\Xclass;

use TYPO3\CMS\Backend\Form\Container\InlineRecordContainer;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * This file is part of the "address" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

/**
 * Xclass InlineRecordContainer to show better preview of tt_content elements
 */
class InlineRecordContainerForAddress extends InlineRecordContainer
{

    /**
     * @param array $data
     * @param string $ariaAttributesString HTML aria attributes for the collapse button
     * @return string
     */
    protected function renderForeignRecordHeader(array $data, string $ariaAttributesString): string
    {
        $languageService = $this->getLanguageService();
        $inlineConfig = $data['inlineParentConfig'];
        $foreignTable = $inlineConfig['foreign_table'];
        if (!isset($inlineConfig['appearance']['useXclassedVersion']) || $inlineConfig['appearance']['useXclassedVersion'] !== true || $foreignTable !== 'tt_content') {
            return parent::renderForeignRecordHeader($data, $ariaAttributesString);
        }

        $rec = $data['databaseRow'];
        // Init:
        $domObjectId = $this->inlineStackProcessor->getCurrentStructureDomObjectIdPrefix($data['inlineFirstPid']);
        $objectId = $domObjectId . '-' . $foreignTable . '-' . $rec['uid'];

        $altText = BackendUtility::getRecordIconAltText($rec, $foreignTable);
        $iconImg = '<span title="' . $altText . '" id="' . htmlspecialchars($objectId) . '_icon' . '">' . $this->iconFactory->getIconForRecord($foreignTable, $rec, Icon::SIZE_SMALL)->render() . '</span>';

        $raw = BackendUtility::getRecord('tt_content', $rec['uid']);
        if (is_array($raw) && !empty($raw) && $raw['CType'] !== 'gridelements_pi1') {
            $pageLayoutView = GeneralUtility::makeInstance(PageLayoutView::class);
            $pageLayoutView->doEdit = false;

            $label = $pageLayoutView->tt_content_drawItem($raw);
        } else {
            $label = $data['recordTitle'];
            if (!empty($recordTitle)) {
                // The user function may return HTML, therefore we can't escape it
                if (empty($data['processedTca']['ctrl']['formattedLabel_userFunc'])) {
                    $label = BackendUtility::getRecordTitlePrep($recordTitle);
                }
            } else {
                $label = '<em>[' . htmlspecialchars($languageService->sL('LLL:EXT:lang/Resources/Private/Language/locallang_core.xlf:labels.no_title')) . ']</em>';
            }
        }

        $label = '<span id="' . $objectId . '_label">' . $label . '</span>';
        $header = '
                <div class="form-irre-header-cell form-irre-header-icon" id="' . $objectId . '_iconcontainer" style="vertical-align:top;padding-top:8px;">' . $iconImg . '</div>
				<div class="form-irre-header-cell form-irre-header-body">' . $label . '</div>
				<div class="form-irre-header-cell form-irre-header-control t3js-formengine-irre-control">' . $this->renderForeignRecordHeaderControl($data) . '</div>';

        return $header;
    }
}
