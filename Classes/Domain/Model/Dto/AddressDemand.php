<?php

namespace WapplerSystems\Address\Domain\Model\Dto;

/**
 * This file is part of the "address" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use WapplerSystems\Address\Domain\Model\DemandInterface;

/**
 * Address Demand object which holds all information to get the correct address records.
 */
class AddressDemand extends AbstractEntity implements DemandInterface
{

    protected array $categories = [];

    protected string $categoryConjunction = '';

    protected bool $includeSubCategories = false;

    protected array $tags = [];

    protected string $archiveRestriction;

    protected int $topAddressRestriction;

    protected string $searchFields;

    protected ?Search $search = null;

    protected string $order = '';

    protected string $orderByAllowed = '';

    protected bool $topAddressFirst = false;

    protected array $storagePage;

    protected int $limit;

    protected int $offset;

    protected bool $excludeAlreadyDisplayedAddress;

    protected array $hideIdList;

    protected string $action = '';

    protected string $class = '';

    protected array $ids = [];

    /**
     * List of allowed types
     */
    protected array $types = [];

    /**
     * Set archive settings
     *
     * @param string $archiveRestriction archive setting
     * @return AddressDemand
     */
    public function setArchiveRestriction(string $archiveRestriction): AddressDemand
    {
        $this->archiveRestriction = $archiveRestriction;
        return $this;
    }

    /**
     * Get archive setting
     *
     * @return string
     */
    public function getArchiveRestriction(): string
    {
        return $this->archiveRestriction;
    }

    /**
     * List of allowed categories
     *
     * @param array $categories categories
     * @return AddressDemand
     */
    public function setCategories(array $categories): AddressDemand
    {
        $this->categories = $categories;
        return $this;
    }

    /**
     * Get allowed categories
     *
     * @return array
     */
    public function getCategories(): array
    {
        return $this->categories;
    }

    /**
     * Set category mode
     *
     * @param string $categoryConjunction
     * @return AddressDemand
     */
    public function setCategoryConjunction(string $categoryConjunction): AddressDemand
    {
        $this->categoryConjunction = $categoryConjunction;
        return $this;
    }

    /**
     * Get category mode
     *
     * @return string
     */
    public function getCategoryConjunction(): string
    {
        return $this->categoryConjunction;
    }

    /**
     * Get include sub categories
     * @return bool
     */
    public function getIncludeSubCategories(): bool
    {
        return $this->includeSubCategories;
    }

    /**
     * @param bool $includeSubCategories
     * @return AddressDemand
     */
    public function setIncludeSubCategories(bool $includeSubCategories): AddressDemand
    {
        $this->includeSubCategories = $includeSubCategories;
        return $this;
    }


    /**
     * Get Tags
     */
    public function getTags(): array
    {
        return $this->tags;
    }

    /**
     * Set Tags
     */
    public function setTags(array $tags): AddressDemand
    {
        $this->tags = $tags;
        return $this;
    }

    /**
     * Set order
     */
    public function setOrder(string $order): AddressDemand
    {
        $this->order = $order;
        return $this;
    }

    /**
     * Get order
     */
    public function getOrder(): string
    {
        return $this->order;
    }

    /**
     * Set order allowed
     */
    public function setOrderByAllowed(string $orderByAllowed): AddressDemand
    {
        $this->orderByAllowed = $orderByAllowed;
        return $this;
    }

    /**
     * Get allowed order fields
     */
    public function getOrderByAllowed(): string
    {
        return $this->orderByAllowed;
    }

    /**
     * Set order respect top address flag
     *
     * @param bool $topAddressFirst respect top address flag
     * @return AddressDemand
     */
    public function setTopAddressFirst(bool $topAddressFirst): AddressDemand
    {
        $this->topAddressFirst = $topAddressFirst;
        return $this;
    }

    /**
     * Get order respect top address flag
     */
    public function getTopAddressFirst(): bool
    {
        return $this->topAddressFirst;
    }

    /**
     * Set search fields
     */
    public function setSearchFields(string $searchFields): AddressDemand
    {
        $this->searchFields = $searchFields;
        return $this;
    }

    /**
     * Get search fields
     */
    public function getSearchFields(): string
    {
        return $this->searchFields;
    }

    /**
     * Set top address setting
     */
    public function setTopAddressRestriction(int $topAddressRestriction): AddressDemand
    {
        $this->topAddressRestriction = $topAddressRestriction;
        return $this;
    }

    /**
     * Get top address setting
     */
    public function getTopAddressRestriction(): int
    {
        return $this->topAddressRestriction;
    }

    /**
     * Set list of storage pages
     *
     */
    public function setStoragePage(array $storagePage): AddressDemand
    {
        $this->storagePage = $storagePage;
        return $this;
    }

    /**
     * Get list of storage pages
     */
    public function getStoragePage(): array
    {
        return $this->storagePage;
    }


    /**
     * Set limit
     *
     * @param int $limit limit
     * @return AddressDemand
     */
    public function setLimit(int $limit): AddressDemand
    {
        $this->limit = $limit;
        return $this;
    }

    /**
     * Get limit
     *
     * @return int
     */
    public function getLimit(): int
    {
        return $this->limit;
    }

    /**
     * Set offset
     *
     * @param int $offset offset
     * @return AddressDemand
     */
    public function setOffset(int $offset): AddressDemand
    {
        $this->offset = $offset;
        return $this;
    }

    /**
     * Get offset
     *
     * @return int
     */
    public function getOffset(): int
    {
        return $this->offset;
    }


    /**
     * Get search object
     *
     * @return Search|null
     */
    public function getSearch(): ?Search
    {
        return $this->search;
    }

    /**
     * Set search object
     */
    public function setSearch(?Search $search = null): AddressDemand
    {
        $this->search = $search;
        return $this;
    }

    /**
     * Set flag if displayed address records should be excluded
     *
     * @param bool $excludeAlreadyDisplayedAddress
     * @return AddressDemand
     */
    public function setExcludeAlreadyDisplayedAddress(bool $excludeAlreadyDisplayedAddress): AddressDemand
    {
        $this->excludeAlreadyDisplayedAddress = $excludeAlreadyDisplayedAddress;
        return $this;
    }

    /**
     * Get flag if displayed address records should be excluded
     *
     * @return bool
     */
    public function getExcludeAlreadyDisplayedAddress(): bool
    {
        return $this->excludeAlreadyDisplayedAddress;
    }

    public function getHideIdList(): array
    {
        return $this->hideIdList;
    }

    public function setHideIdList(array $hideIdList): AddressDemand
    {
        $this->hideIdList = $hideIdList;
        return $this;
    }

    /**
     * @return string
     */
    public function getAction(): string
    {
        return $this->action;
    }

    /**
     * @param string $action
     * @return AddressDemand
     */
    public function setAction(string $action): AddressDemand
    {
        $this->action = $action;
        return $this;
    }

    /**
     * @return string
     */
    public function getClass(): string
    {
        return $this->class;
    }

    /**
     * @param string $class
     * @return AddressDemand
     */
    public function setClass(string $class): AddressDemand
    {
        $this->class = $class;
        return $this;
    }

    /**
     * @param string $action
     * @param string $controller
     * @return AddressDemand
     */
    public function setActionAndClass(string $action, string $controller): AddressDemand
    {
        $this->action = $action;
        $this->class = $controller;
        return $this;
    }

    /**
     * Get allowed types
     *
     * @return array
     */
    public function getTypes(): array
    {
        return $this->types;
    }

    /**
     * Set allowed types
     *
     * @param array $types
     */
    public function setTypes(array $types): void
    {
        $this->types = $types;
    }

    /**
     * @return array
     */
    public function getIds(): array
    {
        return $this->ids;
    }

    /**
     * @param array $ids
     */
    public function setIds(array $ids): void
    {
        $this->ids = $ids;
    }


}
