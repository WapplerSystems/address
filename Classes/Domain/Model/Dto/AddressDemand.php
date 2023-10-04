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

    /**
     * @var array
     */
    protected array $categories = [];

    /**
     * @var string
     */
    protected string $categoryConjunction = '';

    /**
     * @var bool
     */
    protected bool $includeSubCategories = false;



    /** @var ObjectStorage|null */
    protected $tags;

    /**
     * @var string
     */
    protected string $archiveRestriction;

    /** @var int */
    protected $topAddressRestriction;

    /** @var string */
    protected $searchFields;

    /** @var Search|null */
    protected $search;

    /** @var string */
    protected string $order = '';

    /** @var string */
    protected string $orderByAllowed = '';

    /** @var bool */
    protected bool $topAddressFirst = false;

    /** @var int */
    protected $storagePage;

    /** @var int */
    protected int $limit;

    /** @var int */
    protected int $offset;

    /** @var bool */
    protected bool $excludeAlreadyDisplayedAddress;

    /** @var string */
    protected string $hideIdList;

    /** @var string */
    protected string $action = '';

    /** @var string */
    protected string $class = '';

    /**
     * @var array
     */
    protected array $ids = [];

    /**
     * List of allowed types
     *
     * @var array
     */
    protected array $types = [];

    /**
     * Set archive settings
     *
     * @param string $archiveRestriction archive setting
     * @return AddressDemand
     */
    public function setArchiveRestriction($archiveRestriction)
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
    public function setCategoryConjunction($categoryConjunction): AddressDemand
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
    public function setIncludeSubCategories($includeSubCategories): AddressDemand
    {
        $this->includeSubCategories = $includeSubCategories;
        return $this;
    }


    /**
     * Get Tags
     *
     * @return ObjectStorage|null
     */
    public function getTags(): ?ObjectStorage
    {
        return $this->tags;
    }

    /**
     * Set Tags
     *
     * @param ObjectStorage $tags tags
     * @return AddressDemand
     */
    public function setTags(ObjectStorage $tags): AddressDemand
    {
        $this->tags = $tags;
        return $this;
    }

    /**
     * Set order
     *
     * @param string $order order
     * @return AddressDemand
     */
    public function setOrder($order)
    {
        $this->order = $order;
        return $this;
    }

    /**
     * Get order
     *
     * @return string
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Set order allowed
     *
     * @param string $orderByAllowed allowed fields for ordering
     * @return AddressDemand
     */
    public function setOrderByAllowed($orderByAllowed): AddressDemand
    {
        $this->orderByAllowed = $orderByAllowed;
        return $this;
    }

    /**
     * Get allowed order fields
     *
     * @return string
     */
    public function getOrderByAllowed()
    {
        return $this->orderByAllowed;
    }

    /**
     * Set order respect top address flag
     *
     * @param bool $topAddressFirst respect top address flag
     * @return AddressDemand
     */
    public function setTopAddressFirst($topAddressFirst): AddressDemand
    {
        $this->topAddressFirst = $topAddressFirst;
        return $this;
    }

    /**
     * Get order respect top address flag
     *
     * @return int
     */
    public function getTopAddressFirst()
    {
        return $this->topAddressFirst;
    }

    /**
     * Set search fields
     *
     * @param string $searchFields search fields
     * @return $this
     */
    public function setSearchFields($searchFields): AddressDemand
    {
        $this->searchFields = $searchFields;
        return $this;
    }

    /**
     * Get search fields
     *
     * @return string
     */
    public function getSearchFields()
    {
        return $this->searchFields;
    }

    /**
     * Set top address setting
     *
     * @param string $topAddressRestriction top address settings
     * @return AddressDemand
     */
    public function setTopAddressRestriction($topAddressRestriction): AddressDemand
    {
        $this->topAddressRestriction = $topAddressRestriction;
        return $this;
    }

    /**
     * Get top address setting
     *
     * @return string
     */
    public function getTopAddressRestriction()
    {
        return $this->topAddressRestriction;
    }

    /**
     * Set list of storage pages
     *
     * @param string $storagePage storage page list
     * @return AddressDemand
     */
    public function setStoragePage($storagePage): AddressDemand
    {
        $this->storagePage = $storagePage;
        return $this;
    }

    /**
     * Get list of storage pages
     *
     * @return string
     */
    public function getStoragePage(): int|string
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
        $this->limit = (int)$limit;
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
     *
     * @param Search $search search object
     * @return AddressDemand
     */
    public function setSearch(Search $search = null): AddressDemand
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

    /**
     * @return string
     */
    public function getHideIdList(): string
    {
        return $this->hideIdList;
    }

    /**
     * @param string $hideIdList
     * @return AddressDemand
     */
    public function setHideIdList(string $hideIdList): AddressDemand
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
    public function setTypes($types): void
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
