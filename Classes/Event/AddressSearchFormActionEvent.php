<?php

/*
 * This file is part of the "address" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace WapplerSystems\Address\Event;

use WapplerSystems\Address\Controller\AddressController;
use TYPO3\CMS\Extbase\Mvc\Request;

final class AddressSearchFormActionEvent
{
    /**
     * @var AddressController
     */
    private $addressController;

    /**
     * @var array
     */
    private $assignedValues;

    /** @var Request */
    private $request;

    public function __construct(AddressController $addressController, array $assignedValues, Request $request)
    {
        $this->addressController = $addressController;
        $this->assignedValues = $assignedValues;
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
     * Get the assignedValues
     */
    public function getAssignedValues(): array
    {
        return $this->assignedValues;
    }

    /**
     * Set the assignedValues
     */
    public function setAssignedValues(array $assignedValues): self
    {
        $this->assignedValues = $assignedValues;

        return $this;
    }

    public function getRequest(): Request
    {
        return $this->request;
    }
}
