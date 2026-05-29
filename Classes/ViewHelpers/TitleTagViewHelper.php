<?php
declare(strict_types=1);

namespace WapplerSystems\Address\ViewHelpers;

/**
 * This file is part of the "address" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use WapplerSystems\Address\Seo\AddressTitleProvider;

/**
 * ViewHelper to render the page title
 *
 * # Example: Basic Example
 * # Description: Render the content of the VH as page title
 * <code>
 *    <ad:titleTag>{address.title}</n:titleTag>
 * </code>
 * <output>
 *    <title>TYPO3 is awesome</title>
 * </output>
 *
 */
class TitleTagViewHelper extends AbstractViewHelper
{

    public function render(): void
    {
        // recordRegister / currentRecord no longer available (TSFE removed in v14)

        $content = trim($this->renderChildren());
        if (!empty($content)) {
            GeneralUtility::makeInstance(AddressTitleProvider::class)->setTitle($content);
        }
    }
}
