<?php

namespace WapplerSystems\Address\Service\Import;

/**
 * This file is part of the "address" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

/**
 * Import Service interface
 */
interface DataProviderServiceInterface
{
    public function getTotalRecordCount();

    public function getImportData();
}
