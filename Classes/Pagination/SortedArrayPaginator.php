<?php
namespace WapplerSystems\Address\Pagination;

/**
 * This file is part of the "address" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Pagination\AbstractPaginator;

class SortedArrayPaginator extends AbstractPaginator {

    /**
     * @var array
     */
    private $items;

    /**
     * @var array
     */
    private $sortedIds;

    /**
     * @var array
     */
    private $paginatedItems = [];

    public function __construct(
        array $items,
        array $sortedIds,
        int $currentPageNumber = 1,
        int $itemsPerPage = 10
    ) {
        $this->items = $items;
        $this->sortedIds = $sortedIds;
        $this->setCurrentPageNumber($currentPageNumber);
        $this->setItemsPerPage($itemsPerPage);

        $this->updateInternalState();
    }

    /**
     * @return iterable|array
     */
    public function getPaginatedItems(): iterable
    {
        return $this->paginatedItems;
    }

    protected function updatePaginatedItems(int $itemsPerPage, int $offset): void
    {
        $sortedIds = $this->sortedIds;

        $slicedItems = new \ArrayObject(array_slice($this->items, $offset, $itemsPerPage));
        $slicedItems->uasort(function ($first, $second) use ($sortedIds) {
            foreach ($sortedIds as $myCustomId) {
                if ($first->getUid() === (int)$myCustomId) {
                    return -1;
                }
                if ($second->getUid() === (int)$myCustomId) {
                    return 1;
                }
            }
            return 0;
        });
        $this->paginatedItems = $slicedItems;
    }

    protected function getTotalAmountOfItems(): int
    {
        return count($this->items);
    }

    protected function getAmountOfItemsOnCurrentPage(): int
    {
        return count($this->paginatedItems);
    }


}