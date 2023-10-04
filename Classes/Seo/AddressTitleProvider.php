<?php

declare(strict_types=1);

/*
 * This file is part of the "address" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace WapplerSystems\Address\Seo;

use WapplerSystems\Address\Domain\Model\Address;
use TYPO3\CMS\Core\PageTitle\AbstractPageTitleProvider;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Generate page title based on properties of the address model
 */
class AddressTitleProvider extends AbstractPageTitleProvider
{
    private const DEFAULT_PROPERTIES = 'title';
    private const DEFAULT_GLUE = '" "';

    /**
     * @param Address $address
     * @param array $configuration
     */
    public function setTitleByAddress(Address $address, array $configuration = []): void
    {
        $title = '';
        $fields = GeneralUtility::trimExplode(',', $configuration['properties'] ?? self::DEFAULT_PROPERTIES, true);

        foreach ($fields as $field) {
            $getter = 'get' . ucfirst($field);
            $value = $address->$getter();
            if ($value) {
                $title = $value;
                break;
            }
        }
        if ($title) {
            $this->title = $title;
        }
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }
}
