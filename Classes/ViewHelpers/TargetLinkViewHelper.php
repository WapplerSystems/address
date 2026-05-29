<?php
declare(strict_types=1);

namespace WapplerSystems\Address\ViewHelpers;

/**
 * This file is part of the "address" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * ViewHelper to get the target out of the typolink
 *
 * # Example: Basic Example
 * # Description: {relatedLink.uri} is defined as "123 _blank"
 * <code>
 * <f:link.page pageUid="{relatedLink.uri}" target="{n:targetLink(link:relatedLink.uri)}">Link</Link>
 * </code>
 * <output>
 * A link to the page with uid 123 and target set to "_blank"
 * </output>
 */
class TargetLinkViewHelper extends AbstractViewHelper
{

    /**
     * Initialize arguments.
     */
    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('link', 'string', 'Link', true);
    }

    /**
     * Returns the correct target of a typolink
     *
     * @return mixed
     */
    public function render(): mixed
    {
        $params = explode(' ', $this->arguments['link']);

        // The target is on the 2nd place and must start with a '_'
        if (count($params) >= 2 && substr($params[1], 0, 1) === '_') {
            return $params[1];
        }

        return '';
    }
}
