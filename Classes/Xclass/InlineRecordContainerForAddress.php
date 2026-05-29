<?php
declare(strict_types=1);

namespace WapplerSystems\Address\Xclass;

use TYPO3\CMS\Backend\Form\Container\InlineRecordContainer;

/**
 * Xclass InlineRecordContainer — previously provided enhanced tt_content preview.
 * In TYPO3 v14, the internal APIs used (PageLayoutView, getCurrentStructureDomObjectIdPrefix)
 * have been removed. This class now delegates entirely to the parent.
 */
class InlineRecordContainerForAddress extends InlineRecordContainer
{
}