<?php
declare(strict_types=1);

namespace WapplerSystems\Address\Hooks\Form;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Form\Domain\Exception\RenderingException;
use TYPO3\CMS\Form\Domain\Model\FormElements\GenericFormElement;
use TYPO3\CMS\Form\Domain\Model\Renderable\RootRenderableInterface;
use TYPO3\CMS\Form\Domain\Runtime\FormRuntime;
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
        $param = $renderable->getRequest()->getQueryParams()['tx_address_pi1'] ?? [];
        if (is_array($param) && $renderable->getIdentifier() === 'addressUid' && (int)$param['contactAddress'] > 0) {
            $renderable->setDefaultValue((int)$param['contactAddress']);
        }
    }


    /**
     * @param FormRuntime $formRuntime
     * @param RootRenderableInterface $renderable
     * @return void
     * @throws RenderingException
     */
    public function beforeRendering(FormRuntime $formRuntime, RootRenderableInterface $renderable)
    {
        $param = $formRuntime->getRequest()->getQueryParams()['tx_address_pi1'] ?? [];

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
