<?php
declare(strict_types=1);
namespace WapplerSystems\Address\Domain\Form\Finishers;

/**
 * This file is part of the "address" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Mime\Address;
use TYPO3\CMS\Core\Mail\MailerInterface;
use TYPO3\CMS\Core\Mail\TemplatedEmailFactory;
use TYPO3\CMS\Form\Domain\Finishers\EmailFinisher;
use TYPO3\CMS\Form\Domain\Finishers\Exception\FinisherException;
use WapplerSystems\Address\Domain\Repository\AddressRepository;


class SendToAddressFinisher extends EmailFinisher
{
    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        TemplatedEmailFactory $templatedEmailFactory,
        MailerInterface $mailer,
        private readonly AddressRepository $addressRepository,
    ) {
        parent::__construct($eventDispatcher, $templatedEmailFactory, $mailer);
    }

    protected function getRecipients(
        string $listOption,
    ): array {

        $addresses = parent::getRecipients($listOption);

        if ($listOption === 'recipients') {

            $values = $this->finisherContext->getFormValues();

            if ((int)$values['addressUid'] === 0) {
                throw new FinisherException('No address given.', 132706567666);
            }

            $address = $this->addressRepository->findByUid($values['addressUid']);
            if ($address === null) {
                throw new FinisherException('No address found.', 132706567632);
            }

            $recipientAddress = $address->getFirstEmailAddress();
            if ($recipientAddress === '') {
                return [];
            }
            $recipientName = $address->getName();

            $addresses[] = new Address($recipientAddress, $recipientName);
        }
        return $addresses;
    }
}