<?php

/*
 * This file is part of the "address" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace WapplerSystems\Address\Event;

use WapplerSystems\Address\Controller\AddressController;
use WapplerSystems\Address\Domain\Model\Address;
use TYPO3\CMS\Extbase\Mvc\Request;

final class AddressCheckPidOfAddressRecordFailedInDetailActionEvent
{
    /**
     * @var AddressController
     */
    private $addressController;

    /**
     * @var Address
     */
    private $address;

    /** @var Request */
    private $request;

    public function __construct(AddressController $addressController, Address $address, Request $request)
    {
        $this->addressController = $addressController;
        $this->address = $address;
        $this->request = $request;
    }

    /**
     * Get the address controller
     */
    public function getAddressController(): AddressController
    {
        return $this->addressController;
    }

    /**
     * Set the address controller
     */
    public function setAddressController(AddressController $addressController): self
    {
        $this->addressController = $addressController;

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

    /**
     * @return Request
     */
    public function getRequest(): Request
    {
        return $this->request;
    }
}
