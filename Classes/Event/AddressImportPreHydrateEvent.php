<?php

/*
 * This file is part of the "address" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace WapplerSystems\Address\Event;

use WapplerSystems\Address\Domain\Service\AddressImportService;

final class AddressImportPreHydrateEvent
{
    /**
     * @var AddressImportService
     */
    private $addressImportService;

    /**
     * @var array
     */
    private $importItem;

    public function __construct(AddressImportService $addressImportService, array $importItem)
    {
        $this->addressImportService = $addressImportService;
        $this->importItem = $importItem;
    }

    /**
     * Get the importer service
     */
    public function getAddressImportService(): AddressImportService
    {
        return $this->addressImportService;
    }

    /**
     * Set the importer service
     */
    public function setAddressImportService(AddressImportService $addressImportService): self
    {
        $this->addressImportService = $addressImportService;

        return $this;
    }

    /**
     * Get the importItem
     */
    public function getImportItem(): array
    {
        return $this->importItem;
    }

    /**
     * Set the importItem
     */
    public function setImportItem(array $importItem): self
    {
        $this->importItem = $importItem;

        return $this;
    }
}
