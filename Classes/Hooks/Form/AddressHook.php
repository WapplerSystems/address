<?php

namespace WapplerSystems\Address\Hooks\Form;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Form\Domain\Exception\RenderingException;
use TYPO3\CMS\Form\Domain\Model\FormElements\GenericFormElement;
use WapplerSystems\Address\Domain\Repository\AddressRepository;

class AddressHook
{

    /**
     * @param GenericFormElement $renderable
     * @return void
     * @deprecated
     */
    public function initializeFormElement(GenericFormElement $renderable)
    {
        $param = GeneralUtility::_GP('tx_address_pi1');
        if (is_array($param) && $renderable->getIdentifier() === 'addressUid' && (int)$param['contactAddress'] > 0) {
            $renderable->setDefaultValue((int)$param['contactAddress']);
        }
    }


    /**
     * @param \TYPO3\CMS\Form\Domain\Runtime\FormRuntime $formRuntime
     * @param \TYPO3\CMS\Form\Domain\Model\Renderable\RootRenderableInterface $renderable
     * @return void
     * @throws RenderingException
     */
    public function beforeRendering(\TYPO3\CMS\Form\Domain\Runtime\FormRuntime $formRuntime, \TYPO3\CMS\Form\Domain\Model\Renderable\RootRenderableInterface $renderable)
    {
        $param = GeneralUtility::_GP('tx_address_pi1');

        $addressUid = $formRuntime->getFormState()->getFormValue('addressUid') ?? $param['address'] ?? null;

        if ($addressUid === null) return;

        $repository = GeneralUtility::makeInstance(AddressRepository::class);

        $address = $repository->findByUid((int)$addressUid);
        if (!$address) {
            throw new RenderingException('Address not found.', 13273242424);
        }

        if (is_array($param) && $renderable->getIdentifier() === 'addressUid') {
            $formRuntime->getFormState()->setFormValue('addressUid',(int)$addressUid);
        }

        if (is_array($param) && $renderable->getIdentifier() === 'contactlabel') {
            $formRuntime->getFormState()->setFormValue('contactlabel',$address->getName());
        }

    }


}
