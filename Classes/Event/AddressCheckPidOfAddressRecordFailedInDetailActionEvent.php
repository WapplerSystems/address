<?php
declare(strict_types=1);

/*
 * This file is part of the "address" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace WapplerSystems\Address\Event;

use TYPO3\CMS\Extbase\Mvc\RequestInterface;
use WapplerSystems\Address\Controller\AddressController;
use WapplerSystems\Address\Domain\Model\Address;
use TYPO3\CMS\Extbase\Mvc\Request;

final class AddressCheckPidOfAddressRecordFailedInDetailActionEvent
{
    private AddressController $addressController;

    private Address $address;

    private RequestInterface $request;

    public function __construct(AddressController $addressController, Address $address, RequestInterface $request)
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
    public function getRequest(): RequestInterface
    {
        return $this->request;
    }
}
