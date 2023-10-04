<?php

namespace WapplerSystems\Address\ViewHelpers;

/**
 * This file is part of the "address" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;
use WapplerSystems\Address\Domain\Model\Address;

/**
 * ViewHelper to exclude address items in other plugins
 *
 * # Example: Basic example
 *
 * <code>
 * <ad:excludeDisplayedAddress address="{address}" />
 * </code>
 * <output>
 * None
 * </output>
 *
 */
class ExcludeDisplayedAddressViewHelper extends AbstractViewHelper
{
    use CompileWithRenderStatic;

    /**
     * Initialize arguments
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('address', Address::class, 'address record', true);
    }

    /**
     * @param array $arguments
     * @param \Closure $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
     */
    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ) {
        $address = $arguments['address'];
        $uid = $address->getUid();

        if (empty($GLOBALS['EXT']['address']['alreadyDisplayed'])) {
            $GLOBALS['EXT']['address']['alreadyDisplayed'] = [];
        }
        $GLOBALS['EXT']['address']['alreadyDisplayed'][$uid] = $uid;

        // Add localized uid as well
        $originalUid = (int)$address->_getProperty('_localizedUid');
        if ($originalUid > 0) {
            $GLOBALS['EXT']['address']['alreadyDisplayed'][$originalUid] = $originalUid;
        }
    }
}
