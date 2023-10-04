<?php

declare(strict_types=1);

/*
 * This file is part of the "address" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace WapplerSystems\Address\Seo;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\DataProcessing\LanguageMenuProcessor;
use TYPO3\CMS\Frontend\Event\ModifyHrefLangTagsEvent;

/**
 * Remove the hreflang for address in strict mode with no translations
 */
class HrefLangEvent
{
    /** @var ContentObjectRenderer */
    public $cObj;

    /** @var LanguageMenuProcessor */
    protected $languageMenuProcessor;

    public function __construct(ContentObjectRenderer $cObj, LanguageMenuProcessor $languageMenuProcessor)
    {
        $this->cObj = $cObj;
        $this->languageMenuProcessor = $languageMenuProcessor;
    }

    public function __invoke(ModifyHrefLangTagsEvent $event): void
    {
        $addressAvailabilityChecker = GeneralUtility::makeInstance(AddressAvailability::class);
        if ($addressAvailabilityChecker->getAddressIdFromRequest() > 0) {
            $allHrefLangs = $event->getHrefLangs();

            $languages = $this->languageMenuProcessor->process($this->cObj, [], [], []);
            $errorTriggered = false;
            foreach ($languages['languagemenu'] as $language) {
                $hreflangKey = $language['hreflang'];
                // skip all languages which are not used in hreflang
                if (!isset($allHrefLangs[$hreflangKey]) || $hreflangKey === 'x-default') {
                    continue;
                }

                try {
                    $check = $addressAvailabilityChecker->check($language['languageId']);

                    if (!$check) {
                        unset($allHrefLangs[$hreflangKey]);
                    }
                } catch (\UnexpectedValueException $e) {
                    $errorTriggered = true;
                }
            }

            if (!$errorTriggered) {
                if (count($allHrefLangs) <= 2) {
                    unset($allHrefLangs['x-default']);
                }
                $event->setHrefLangs($allHrefLangs);
            }
        }
    }
}
