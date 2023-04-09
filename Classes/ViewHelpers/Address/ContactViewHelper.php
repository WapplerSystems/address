<?php

namespace WapplerSystems\Address\ViewHelpers\Address;


use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use WapplerSystems\Address\Domain\Model\Address;
use WapplerSystems\Address\Domain\Model\Contact;


class ContactViewHelper extends AbstractViewHelper
{

    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('address', Address::class, 'address item', true);
        $this->registerArgument('type', 'string', 'Type of contact (email, telephone, fax)', true);
    }

    /**
     *
     * @return Contact|null
     */
    public function render(): ?Contact
    {
        /** @var Address $address */
        $address = $this->arguments['address'];

        return $address->getContactByType($this->arguments['type']);

    }

}
