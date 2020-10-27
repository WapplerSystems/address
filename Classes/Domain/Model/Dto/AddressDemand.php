<?php
namespace WapplerSystems\Address\Domain\Model\Dto;

/**
 * This file is part of the "address" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use WapplerSystems\Address\Domain\Model\DemandInterface;

/**
 * Address Demand object which holds all information to get the correct address records.
 */
class AddressDemand extends AbstractEntity implements DemandInterface
{

    /**
     * @var array
     */
    protected $categories;

    /**
     * @var string
     */
    protected $categoryConjunction;

    /**
     * @var bool
     */
    protected $includeSubCategories = false;



    /** @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage */
    protected $tags;

    /**
     * @var string
     */
    protected $archiveRestriction;

    /**
     * @var string
     */
    protected $timeRestriction = null;

    /** @var string */
    protected $timeRestrictionHigh = null;

    /** @var int */
    protected $topAddressRestriction;

    /** @var string */
    protected $searchFields;

    /** @var \WapplerSystems\Address\Domain\Model\Dto\Search */
    protected $search;

    /** @var string */
    protected $order;

    /** @var string */
    protected $orderByAllowed;

    /** @var bool */
    protected $topAddressFirst;

    /** @var int */
    protected $storagePage;

    /** @var int */
    protected $limit;

    /** @var int */
    protected $offset;

    /** @var bool */
    protected $excludeAlreadyDisplayedAddress;

    /** @var string */
    protected $hideIdList;

    /** @var string */
    protected $action = '';

    /** @var string */
    protected $class = '';

    /**
     * @var array
     */
    protected $ids = [];

    /**
     * List of allowed types
     *
     * @var array
     */
    protected $types = [];

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
    public function getArchiveRestriction()
    {
        return $this->archiveRestriction;
    }

    /**
     * List of allowed categories
     *
     * @param array $categories categories
     * @return AddressDemand
     */
    public function setCategories($categories)
    {
        $this->categories = $categories;
        return $this;
    }

    /**
     * Get allowed categories
     *
     * @return array
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * Set category mode
     *
     * @param string $categoryConjunction
     * @return AddressDemand
     */
    public function setCategoryConjunction($categoryConjunction)
    {
        $this->categoryConjunction = $categoryConjunction;
        return $this;
    }

    /**
     * Get category mode
     *
     * @return string
     */
    public function getCategoryConjunction()
    {
        return $this->categoryConjunction;
    }

    /**
     * Get include sub categories
     * @return bool
     */
    public function getIncludeSubCategories()
    {
        return (boolean)$this->includeSubCategories;
    }

    /**
     * @param bool $includeSubCategories
     * @return AddressDemand
     */
    public function setIncludeSubCategories($includeSubCategories)
    {
        $this->includeSubCategories = $includeSubCategories;
        return $this;
    }


    /**
     * Get Tags
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * Set Tags
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage $tags tags
     * @return AddressDemand
     */
    public function setTags($tags)
    {
        $this->tags = $tags;
        return $this;
    }

    /**
     * Set time limit low, either integer or string
     *
     * @param mixed $timeRestriction
     * @return AddressDemand
     */
    public function setTimeRestriction($timeRestriction)
    {
        $this->timeRestriction = $timeRestriction;
        return $this;
    }

    /**
     * Get time limit low
     *
     * @return mixed
     */
    public function getTimeRestriction()
    {
        return $this->timeRestriction;
    }

    /**
     * Get time limit high
     *
     * @return mixed
     */
    public function getTimeRestrictionHigh()
    {
        return $this->timeRestrictionHigh;
    }

    /**
     * Set time limit high
     *
     * @param mixed $timeRestrictionHigh
     * @return AddressDemand
     */
    public function setTimeRestrictionHigh($timeRestrictionHigh)
    {
        $this->timeRestrictionHigh = $timeRestrictionHigh;
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
    public function setOrderByAllowed($orderByAllowed)
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
    public function setTopAddressFirst($topAddressFirst)
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
    public function setSearchFields($searchFields)
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
    public function setTopAddressRestriction($topAddressRestriction)
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
    public function setStoragePage($storagePage)
    {
        $this->storagePage = $storagePage;
        return $this;
    }

    /**
     * Get list of storage pages
     *
     * @return string
     */
    public function getStoragePage()
    {
        return $this->storagePage;
    }


    /**
     * Set limit
     *
     * @param int $limit limit
     * @return AddressDemand
     */
    public function setLimit($limit)
    {
        $this->limit = (int)$limit;
        return $this;
    }

    /**
     * Get limit
     *
     * @return int
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * Set offset
     *
     * @param int $offset offset
     * @return AddressDemand
     */
    public function setOffset($offset)
    {
        $this->offset = (int)$offset;
        return $this;
    }

    /**
     * Get offset
     *
     * @return int
     */
    public function getOffset()
    {
        return $this->offset;
    }


    /**
     * Get search object
     *
     * @return \WapplerSystems\Address\Domain\Model\Dto\Search
     */
    public function getSearch()
    {
        return $this->search;
    }

    /**
     * Set search object
     *
     * @param \WapplerSystems\Address\Domain\Model\Dto\Search $search search object
     * @return AddressDemand
     */
    public function setSearch($search = null)
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
    public function setExcludeAlreadyDisplayedAddress($excludeAlreadyDisplayedAddress)
    {
        $this->excludeAlreadyDisplayedAddress = (bool)$excludeAlreadyDisplayedAddress;
        return $this;
    }

    /**
     * Get flag if displayed address records should be excluded
     *
     * @return bool
     */
    public function getExcludeAlreadyDisplayedAddress()
    {
        return $this->excludeAlreadyDisplayedAddress;
    }

    /**
     * @return string
     */
    public function getHideIdList()
    {
        return $this->hideIdList;
    }

    /**
     * @param string $hideIdList
     * @return AddressDemand
     */
    public function setHideIdList($hideIdList)
    {
        $this->hideIdList = $hideIdList;
        return $this;
    }

    /**
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @param string $action
     * @return AddressDemand
     */
    public function setAction($action)
    {
        $this->action = $action;
        return $this;
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @param string $class
     * @return AddressDemand
     */
    public function setClass($class)
    {
        $this->class = $class;
        return $this;
    }

    /**
     * @param string $action
     * @param string $controller
     * @return AddressDemand
     */
    public function setActionAndClass($action, $controller)
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
    public function getTypes()
    {
        return $this->types;
    }

    /**
     * Set allowed types
     *
     * @param array $types
     */
    public function setTypes($types)
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
