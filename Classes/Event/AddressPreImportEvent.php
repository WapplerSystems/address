<?php

declare(strict_types=1);

/*
 * This file is part of the "address" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace WapplerSystems\Address\Event;

use WapplerSystems\Address\Domain\Service\AddressImportService;

final class AddressPreImportEvent
{
    private $addressImportService;

    private $importData;

    public function __construct(AddressImportService $addressImportService, array $importData)
    {
        $this->addressImportService = $addressImportService;
        $this->importData = $importData;
    }

    public function getAddressImportService(): AddressImportService
    {
        return $this->addressImportService;
    }

    public function setAddressImportService(AddressImportService $addressImportService): self
    {
        $this->addressImportService = $addressImportService;

        return $this;
    }

    public function getImportData(): array
    {
        return $this->importData;
    }

    public function setImportData(array $importData): self
    {
        $this->importData = $importData;

        return $this;
    }
}
