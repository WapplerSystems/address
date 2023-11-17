<?php
namespace WapplerSystems\Address\Controller;

use WapplerSystems\Address\Event\TagListActionEvent;

/**
 * This file is part of the "address" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

/**
 * Tag controller
 */
class TagController extends AddressController
{
    const SIGNAL_TAG_LIST_ACTION = 'listAction';

    /**
     * List tags
     *
     * @param array|null $overwriteDemand
     * @param int $currentPage
     */
    public function listAction(array $overwriteDemand = null, int $currentPage = 1): \Psr\Http\Message\ResponseInterface
    {

        $demand = $this->createDemandObjectFromSettings($this->settings);
        $demand->setActionAndClass(__METHOD__, __CLASS__);

        if ($overwriteDemand !== null && $this->settings['disableOverrideDemand'] != 1) {
            $demand = $this->overwriteDemandObject($demand, $overwriteDemand);
        }

        $assignedValues = [
            'tags' => $this->tagRepository->findDemanded($demand),
            'overwriteDemand' => $overwriteDemand,
            'demand' => $demand,
        ];

        $event = $this->eventDispatcher->dispatch(new TagListActionEvent($this, $assignedValues, $this->request));

        $this->view->assignMultiple($event->getAssignedValues());

        return $this->htmlResponse();
    }
}
