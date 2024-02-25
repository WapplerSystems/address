<?php

namespace WapplerSystems\Address\ViewHelpers;

/**
 * This file is part of the "address" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;
use WapplerSystems\Address\Domain\Model\Address;

/**
 * ViewHelper to render links from address records to detail view or page
 *
 * # Example: Basic link
 * <code>
 * <ad:link address="{address}" settings="{settings}">
 *    {address.title}
 * </n:link>
 * </code>
 * <output>
 * A link to the given address record using the address title as link text
 * </output>
 *
 * # Example: Set an additional attribute
 * # Description: Available: class, dir, id, lang, style, title, accesskey, tabindex, onclick
 * <code>
 * <ad:link address="{address}" settings="{settings}" class="a-link-class">fo</n:link>
 * </code>
 * <output>
 * <a href="link" class="a-link-class">fo</n:link>
 * </output>
 *
 * # Example: Return the link only
 * <code>
 * <ad:link address="{address}" settings="{settings}" uriOnly="1" />
 * </code>
 * <output>
 * The uri is returned
 * </output>
 *
 */
class LinkViewHelper extends AbstractTagBasedViewHelper
{

    /**
     * @var string
     */
    protected $tagName = 'a';

    /**
     * @var \WapplerSystems\Address\Service\SettingsService
     */
    protected $pluginSettingsService;

    /**
     * @var array
     */
    protected $detailPidDeterminationCallbacks = [
        'flexform' => 'getDetailPidFromFlexform',
        'categories' => 'getDetailPidFromCategories',
        'default' => 'getDetailPidFromDefaultDetailPid',
    ];

    /** @var $cObj ContentObjectRenderer */
    protected $cObj;

    /**
     * @param \WapplerSystems\Address\Service\SettingsService $pluginSettingsService
     */
    public function injectSettingsService(\WapplerSystems\Address\Service\SettingsService $pluginSettingsService)
    {
        $this->pluginSettingsService = $pluginSettingsService;
    }


    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerUniversalTagAttributes();
        $this->registerArgument('address', Address::class, 'address item', true);
        $this->registerArgument('settings', 'array', 'Settings', false, []);
        $this->registerArgument('uriOnly', 'bool', 'url only', false, false);
        $this->registerArgument('configuration', 'array', 'configuration', false, []);
        $this->registerArgument('content', 'string', 'content', false, '');
        $this->registerTagAttribute('section', 'string', 'Anchor for links', false);
    }

    /**
     * Render link to address item or internal/external pages
     *
     * @param string $content optional content which is linked
     * @return string link
     */
    public function render()
    {
        /** @var Address $address */
        $address = $this->arguments['address'];
        $settings = $this->arguments['settings'];
        $uriOnly = $this->arguments['uriOnly'];
        $configuration = $this->arguments['configuration'];
        $content = $this->arguments['content'];

        $tsSettings = $this->pluginSettingsService->getSettings();
        ArrayUtility::mergeRecursiveWithOverrule($tsSettings, (array)$settings);

        $this->init();

        if (is_null($address)) {
            return $this->renderChildren();
        }


        $configuration = $this->getLinkToAddress($address, $tsSettings, $configuration);

        $url = $this->cObj->typoLink_URL($configuration);
        if ($uriOnly) {
            return $url;
        }

        if (!$this->tag->hasAttribute('target')) {
            $target = $this->getTargetConfiguration($configuration);
            if (!empty($target)) {
                $this->tag->addAttribute('target', $target);
            }
        }

        if ($this->hasArgument('section')) {
            $url .= '#' . $this->arguments['section'];
        }

        $this->tag->addAttribute('href', $url);

        if (empty($content)) {
            $content = $this->renderChildren();
        }
        $this->tag->setContent($content);

        return $this->tag->render();
    }

    /**
     * Generate the link configuration for the link to the address item
     *
     * @param Address $address
     * @param array $tsSettings
     * @param array $configuration
     * @return array
     */
    protected function getLinkToAddress(
        Address $address,
        $tsSettings,
        array $configuration = []
    ) {
        if (!isset($configuration['parameter'])) {
            $detailPid = 0;
            $detailPidDeterminationMethods = GeneralUtility::trimExplode(',', $tsSettings['detailPidDetermination'],
                true);

            if ($detailPidDeterminationMethods[0] === 'record') {
                $detailPid = $this->getDetailPidFromRecord($tsSettings, $address);
                if ($detailPid !== 0) {
                    $configuration['parameter'] = $detailPid;
                    return $configuration;
                }

                unset($detailPidDeterminationMethods[0]);
            }

            // if TS is not set, prefer flexform setting
            if (!isset($tsSettings['detailPidDetermination'])) {
                $detailPidDeterminationMethods[] = 'flexform';
            }

            foreach ($detailPidDeterminationMethods as $determinationMethod) {
                if ($callback = $this->detailPidDeterminationCallbacks[$determinationMethod]) {
                    if ($detailPid = call_user_func([$this, $callback], $tsSettings, $address)) {
                        break;
                    }
                }
            }

            if (!$detailPid) {
                $detailPid = $GLOBALS['TSFE']->id;
            }
            $configuration['parameter'] = $detailPid;
        }

        $configuration['additionalParams'] = (isset($configuration['additionalParams']) ? $configuration['additionalParams'] : '') . '&tx_address_pi1[address]=' . $this->getAddressId($address);
        $configuration['additionalParams'] .= '&tx_address_pi1[address]=' . $this->getAddressId($address);

        if ((int)$tsSettings['link']['skipControllerAndAction'] !== 1) {
            $configuration['additionalParams'] .= '&tx_address_pi1[controller]=Address' .
                '&tx_address_pi1[action]=detail';
        }

        return $configuration;
    }

    /**
     * @param Address $address
     * @return int
     */
    protected function getAddressId(Address $address)
    {
        $uid = $address->getUid();
        // If a user is logged in and not in live workspace
        if ($GLOBALS['BE_USER'] && $GLOBALS['BE_USER']->workspace > 0) {
            $record = \TYPO3\CMS\Backend\Utility\BackendUtility::getLiveVersionOfRecord('tx_address_domain_model_address',
                $address->getUid());
            if ($record['uid']) {
                $uid = $record['uid'];
            }
        }

        return $uid;
    }

    /**
     * @param array $configuration
     * @return string
     */
    protected function getTargetConfiguration(array $configuration)
    {
        $configuration['returnLast'] = 'target';

        return $this->cObj->typoLink('dummy', $configuration);
    }

    /**
     * Gets detailPid from categories of the given address item. First will be return.
     *
     * @param  array $settings
     * @param  Address $address
     * @return int
     */
    protected function getDetailPidFromCategories(array $settings, Address $address)
    {
        $detailPid = 0;
        if ($address->getCategories()) {
            foreach ($address->getCategories() as $category) {
                if ($detailPid = (int)$category->getSinglePid()) {
                    break;
                }
            }
        }
        return $detailPid;
    }

    /**
     * Gets detailPid from defaultDetailPid setting
     *
     * @param  array $settings
     * @param  Address $address
     * @return int
     */
    protected function getDetailPidFromDefaultDetailPid(array $settings, Address $address)
    {
        return (int)$settings['defaultDetailPid'];
    }

    protected function getDetailPidFromRecord(array $settings, Address $address) : int
    {
        return $address->getDetailPid();
    }

    /**
     * Gets detailPid from flexform of current plugin.
     *
     * @param  array $settings
     * @param  Address $address
     * @return int
     */
    protected function getDetailPidFromFlexform(array $settings, Address $address)
    {
        return (int)$settings['detailPid'];
    }

    /**
     * Initialize properties
     *
     */
    protected function init()
    {
        $this->cObj = GeneralUtility::makeInstance(ContentObjectRenderer::class);
    }
}
