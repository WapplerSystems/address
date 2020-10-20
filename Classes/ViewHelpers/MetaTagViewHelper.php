<?php

namespace WapplerSystems\Address\ViewHelpers;

/**
 * This file is part of the "address" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * ViewHelper to render meta tags
 *
 * # Example: Basic Example: Address title as og:title meta tag
 * <code>
 * <ad:metaTag property="og:title" content="{addressItem.title}" />
 * </code>
 * <output>
 * <meta property="og:title" content="TYPO3 is awesome" />
 * </output>
 *
 * # Example: Force the attribute "name"
 * <code>
 * <ad:metaTag name="keywords" content="{addressItem.keywords}" />
 * </code>
 * <output>
 * <meta name="keywords" content="address 1, address 2" />
 * </output>
 */
/**
 * ViewHelper to render meta tags
 *
 * # Example: Basic Example: News title as og:title meta tag
 * <code>
 * <ad:metaTag property="og:title" content="{newsItem.title}" />
 * </code>
 * <output>
 * <meta property="og:title" content="TYPO3 is awesome" />
 * </output>
 *
 * # Example: Force the attribute "name"
 * <code>
 * <ad:metaTag name="keywords" content="{newsItem.keywords}" />
 * </code>
 * <output>
 * <meta name="keywords" content="news 1, news 2" />
 * </output>
 */
class MetaTagViewHelper extends \TYPO3Fluid\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper
{

    /**
     * @var string
     */
    protected $tagName = 'meta';

    /**
     * Arguments initialization
     *
     */
    public function initializeArguments()
    {
        $this->registerTagAttribute('property', 'string', 'Property of meta tag');
        $this->registerTagAttribute('name', 'string', 'Content of meta tag using the name attribute');
        $this->registerTagAttribute('content', 'string', 'Content of meta tag');
        $this->registerArgument('useCurrentDomain', 'boolean', 'Use current domain', false, false);
        $this->registerArgument('forceAbsoluteUrl', 'boolean', 'Force absolut domain', false, false);
    }

    /**
     * Renders a meta tag

     */
    public function render()
    {
        // Skip if current record is part of tt_content CType shortcut
        if (!empty($GLOBALS['TSFE']->recordRegister)
            && is_array($GLOBALS['TSFE']->recordRegister)
            && strpos(array_keys($GLOBALS['TSFE']->recordRegister)[0], 'tt_content:') !== false
            && !empty($GLOBALS['TSFE']->currentRecord)
            && strpos($GLOBALS['TSFE']->currentRecord, 'tx_address_domain_model_address:') !== false
        ) {
            return;
        }

        $useCurrentDomain = $this->arguments['useCurrentDomain'];
        $forceAbsoluteUrl = $this->arguments['forceAbsoluteUrl'];

        // set current domain
        if ($useCurrentDomain) {
            $this->tag->addAttribute('content', GeneralUtility::getIndpEnv('TYPO3_REQUEST_URL'));
        }

        // prepend current domain
        if ($forceAbsoluteUrl) {
            $parsedPath = parse_url($this->arguments['content']);
            if (is_array($parsedPath) && !isset($parsedPath['host'])) {
                $this->tag->addAttribute('content',
                    rtrim(GeneralUtility::getIndpEnv('TYPO3_SITE_URL'), '/')
                    . '/'
                    . ltrim($this->arguments['content'], '/')
                );
            }
        }

        if ($useCurrentDomain || (isset($this->arguments['content']) && !empty($this->arguments['content']))) {
            $pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);
            if ($this->tag->hasAttribute('property')) {
                $pageRenderer->setMetaTag('property', $this->tag->getAttribute('property'), $this->tag->getAttribute('content'));
            } elseif ($this->tag->hasAttribute('name')) {
                $pageRenderer->setMetaTag('property', $this->tag->getAttribute('name'), $this->tag->getAttribute('content'));
            }
        }
    }
}
