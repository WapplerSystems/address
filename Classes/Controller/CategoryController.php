<?php
namespace WapplerSystems\Address\Controller;

use WapplerSystems\Address\Domain\Repository\AddressRepository;
use WapplerSystems\Address\Domain\Repository\CategoryRepository;
use WapplerSystems\Address\Domain\Repository\TagRepository;
use WapplerSystems\Address\Event\CategoryListActionEvent;

/**
 * This file is part of the "address" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

/**
 * Category controller
 */
class CategoryController extends AddressController
{
    const SIGNAL_CATEGORY_LIST_ACTION = 'listAction';


    /**
     * List categories
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

        $idList = explode(',', $this->settings['categories']);

        $startingPoint = null;
        if (!empty($this->settings['startingpoint'])) {
            $startingPoint = $this->settings['startingpoint'];
        }

        $assignedValues = [
            'categories' => $this->categoryRepository->findTree($idList, $startingPoint),
            'overwriteDemand' => $overwriteDemand,
            'demand' => $demand,
        ];

        $event = $this->eventDispatcher->dispatch(new CategoryListActionEvent($this, $assignedValues, $this->request));

        $this->view->assignMultiple($event->getAssignedValues());

        return $this->htmlResponse();
    }
}
