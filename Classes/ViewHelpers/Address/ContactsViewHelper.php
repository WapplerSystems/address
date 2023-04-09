<?php

namespace WapplerSystems\Address\ViewHelpers\Address;


use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use WapplerSystems\Address\Domain\Model\Address;


class ContactsViewHelper extends AbstractViewHelper
{

    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('address', Address::class, 'address item', true);
        $this->registerArgument('type', 'string', 'Type of contact (email, telephone, fax)', true);
    }

    /**
     *
     * @return array
     */
    public function render(): array
    {
        /** @var Address $address */
        $address = $this->arguments['address'];

        return $address->getContactsByType($this->arguments['type']);
    }

}
