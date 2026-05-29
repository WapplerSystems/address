<?php
declare(strict_types=1);

namespace WapplerSystems\Address\Utility;

/**
 * This file is part of the "address" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use WapplerSystems\Address\Domain\Model\Dto\EmConfiguration as EmConfigurationDto;

/**
 * Legacy facade around {@see EmConfigurationDto}. Historically this class
 * unserialised `$GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['address']`,
 * which in TYPO3 v11+ is no longer a serialised string but a plain array —
 * the unserialise then silently produced `false`, and every call site got
 * empty defaults instead of the real settings.
 *
 * The facade is kept so existing call sites keep compiling; new code should
 * take an {@see EmConfigurationDto} directly via constructor injection.
 *
 * @deprecated will be removed in a future major; inject EmConfigurationDto.
 */
class EmConfiguration
{
    /**
     * Returns the typed configuration DTO. Equivalent to making the DTO via
     * the DI container — the DTO's own constructor pulls the array from
     * {@see ExtensionConfiguration::get()} when called with no argument.
     */
    public static function getSettings(): EmConfigurationDto
    {
        return GeneralUtility::makeInstance(EmConfigurationDto::class);
    }

    /**
     * Returns the raw extension settings array. The historic implementation
     * called `unserialize()`; on v11+ the value is already an array, so we
     * go through ExtensionConfiguration which handles both shapes.
     *
     * @return array<string,mixed>
     */
    public static function parseSettings(): array
    {
        try {
            $configuration = GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('address');
        } catch (\Throwable) {
            return [];
        }
        return is_array($configuration) ? $configuration : [];
    }
}
