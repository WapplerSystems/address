<?php

namespace WapplerSystems\Address\Service;

/**
 * This file is part of the "address" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Reflection\ObjectAccess;

class SettingsService
{
    protected mixed $settings = null;

    public function __construct(
        private readonly ConfigurationManagerInterface $configurationManager,
    ) {}

    public function getSettings(): array
    {
        if ($this->settings === null) {
            $this->settings = $this->configurationManager->getConfiguration(
                ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS,
                'Address',
                'Pi1'
            );
        }
        return $this->settings;
    }

    public function getByPath(string $path): mixed
    {
        return ObjectAccess::getPropertyPath($this->getSettings(), $path);
    }
}