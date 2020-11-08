<?php

namespace WapplerSystems\Address\Utility;

/**
 * This file is part of the "address" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */
use TYPO3\CMS\Backend\Tree\View\PageTreeView;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

class SortedArray extends \ArrayObject {

    public function __construct(array $items, array $sortedIds)
    {

        $sortedItems = new \ArrayObject($items);
        $sortedItems->uasort(function ($first, $second) use ($sortedIds) {
            foreach ($sortedIds as $myCustomId) {
                if ($first->getUid() === (int)$myCustomId) {
                    return -1;
                }
                if ($second->getUid() === (int)$myCustomId) {
                    return 1;
                }
            }
            return 0;
        });

        parent::__construct($sortedItems->getArrayCopy());
    }

}