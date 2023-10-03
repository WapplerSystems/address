<?php

/*
 * This file is part of the "address" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace WapplerSystems\Address\Event;

use WapplerSystems\Address\Domain\Model\Address;
use WapplerSystems\Address\Domain\Service\AddressImportService;

final class AddressImportPostHydrateEvent
{
    /**
     * @var AddressImportService
     */
    private $addressImportService;

    /**
     * @var array
     */
    private $importItem;

    /**
     * @var Address
     */
    private $address;

    public function __construct(AddressImportService $addressImportService, array $importItem, Address $address)
    {
        $this->addressImportService = $addressImportService;
        $this->importItem = $importItem;
        $this->address = $address;
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

    /**
     * Get the address
     */
    public function getAddress(): Address
    {
        return $this->address;
    }

    /**
     * Set the address
     */
    public function setAddress(Address $address): self
    {
        $this->address = $address;

        return $this;
    }
}
