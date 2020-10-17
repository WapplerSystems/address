<?php
declare(strict_types=1);
namespace WapplerSystems\Address\Domain\Form\Finishers;


use Symfony\Component\Mime\Address;
use TYPO3\CMS\Form\Domain\Finishers\EmailFinisher;
use TYPO3\CMS\Form\Domain\Finishers\Exception\FinisherException;


class SendToAddressFinisher extends EmailFinisher
{

    /**
     * @var \WapplerSystems\Address\Domain\Repository\AddressRepository
     */
    protected $addressRepository;


    /**
     * Inject the category repository
     *
     * @param \WapplerSystems\Address\Domain\Repository\AddressRepository $addressRepository
     */
    public function injectCategoryRepository(\WapplerSystems\Address\Domain\Repository\AddressRepository $addressRepository)
    {
        $this->addressRepository = $addressRepository;
    }


    /**
     * Get mail recipients
     *
     * @param string $listOption List option name
     * @param string $singleAddressOption Single address option
     * @param string|null $singleAddressNameOption Single address name
     * @return array
     *
     * @deprecated since TYPO3 v10.0, will be removed in TYPO3 v11.0.
     */
    protected function getRecipients(
        string $listOption,
        string $singleAddressOption,
        string $singleAddressNameOption = null
    ): array {

        $values = $this->finisherContext->getFormValues();

        if ((int)$values['addressUid'] === 0) {
            throw new FinisherException('No address given.', 132706567666);
        }

        $address = $this->addressRepository->findByUid($values['addressUid']);
        if ($address === null) {
            throw new FinisherException('No address found.', 132706567632);
        }

        $recipientAddress = $address->getEmail();
        $recipientName = $address->getName();

        $addresses = [];
        $addresses[] = new Address($recipientAddress, $recipientName);
        return $addresses;
    }

}