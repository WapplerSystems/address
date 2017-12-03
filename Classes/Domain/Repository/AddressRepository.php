<?php

namespace WapplerSystems\Address\Domain\Repository;

/**
 * This file is part of the "address" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */
use WapplerSystems\Address\Domain\Model\DemandInterface;
use WapplerSystems\Address\Domain\Model\Dto\AddressDemand;
use WapplerSystems\Address\Service\CategoryService;
use WapplerSystems\Address\Utility\ConstraintHelper;
use WapplerSystems\Address\Utility\Validation;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;

/**
 * Address repository with all the callable functionality
 */
class AddressRepository extends \WapplerSystems\Address\Domain\Repository\AbstractDemandedRepository
{

    /**
     * Returns a category constraint created by
     * a given list of categories and a junction string
     *
     * @param QueryInterface $query
     * @param  array $categories
     * @param  string $conjunction
     * @param  bool $includeSubCategories
     * @return \TYPO3\CMS\Extbase\Persistence\Generic\Qom\ConstraintInterface|null
     */
    protected function createCategoryConstraint(
        QueryInterface $query,
        $categories,
        $conjunction,
        $includeSubCategories = false
    ) {
        $constraint = null;
        $categoryConstraints = [];

        // If "ignore category selection" is used, nothing needs to be done
        if (empty($conjunction)) {
            return $constraint;
        }

        if (!is_array($categories)) {
            $categories = GeneralUtility::intExplode(',', $categories, true);
        }
        foreach ($categories as $category) {
            if ($includeSubCategories) {
                $subCategories = GeneralUtility::trimExplode(',',
                    CategoryService::getChildrenCategories($category, 0, '', true), true);
                $subCategoryConstraint = [];
                $subCategoryConstraint[] = $query->contains('categories', $category);
                if (count($subCategories) > 0) {
                    foreach ($subCategories as $subCategory) {
                        $subCategoryConstraint[] = $query->contains('categories', $subCategory);
                    }
                }
                if ($subCategoryConstraint) {
                    $categoryConstraints[] = $query->logicalOr($subCategoryConstraint);
                }
            } else {
                $categoryConstraints[] = $query->contains('categories', $category);
            }
        }

        if ($categoryConstraints) {
            switch (strtolower($conjunction)) {
                case 'or':
                    $constraint = $query->logicalOr($categoryConstraints);
                    break;
                case 'notor':
                    $constraint = $query->logicalNot($query->logicalOr($categoryConstraints));
                    break;
                case 'notand':
                    $constraint = $query->logicalNot($query->logicalAnd($categoryConstraints));
                    break;
                case 'and':
                default:
                    $constraint = $query->logicalAnd($categoryConstraints);
            }
        }

        return $constraint;
    }

    /**
     * Returns an array of constraints created from a given demand object.
     *
     * @param QueryInterface $query
     * @param DemandInterface $demand
     * @throws \UnexpectedValueException
     * @throws \InvalidArgumentException
     * @throws \Exception
     * @return array<\TYPO3\CMS\Extbase\Persistence\Generic\Qom\ConstraintInterface>
     */
    protected function createConstraintsFromDemand(QueryInterface $query, DemandInterface $demand)
    {
        /** @var AddressDemand $demand */
        $constraints = [];

        if ($demand->getCategories() && $demand->getCategories() !== '0') {
            $constraints['categories'] = $this->createCategoryConstraint(
                $query,
                $demand->getCategories(),
                $demand->getCategoryConjunction(),
                $demand->getIncludeSubCategories()
            );
        }

        if ($demand->getTypes()) {
            $constraints['type'] = $query->in('type', $demand->getTypes());
        }

        // archived
        if ($demand->getArchiveRestriction() === 'archived') {
            $constraints['archived'] = $query->logicalAnd(
                $query->lessThan('archive', $GLOBALS['EXEC_TIME']),
                $query->greaterThan('archive', 0)
            );
        } elseif ($demand->getArchiveRestriction() === 'active') {
            $constraints['active'] = $query->logicalOr(
                $query->greaterThanOrEqual('archive', $GLOBALS['EXEC_TIME']),
                $query->equals('archive', 0)
            );
        }

        // top address
        if ($demand->getTopAddressRestriction() == 1) {
            $constraints['topAddress1'] = $query->equals('istopaddress', 1);
        } elseif ($demand->getTopAddressRestriction() == 2) {
            $constraints['topAddress2'] = $query->equals('istopaddress', 0);
        }

        // storage page
        if ($demand->getStoragePage() != 0) {
            $pidList = GeneralUtility::intExplode(',', $demand->getStoragePage(), true);
            $constraints['pid'] = $query->in('pid', $pidList);
        }

        // Tags
        $tags = $demand->getTags();
        if ($tags && is_string($tags)) {
            $tagList = explode(',', $tags);

            $subConstraints = [];
            foreach ($tagList as $singleTag) {
                $subConstraints[] = $query->contains('tags', $singleTag);
            }
            if (count($subConstraints) > 0) {
                $constraints['tags'] = $query->logicalOr($subConstraints);
            }
        }

        // Search
        $searchConstraints = $this->getSearchConstraints($query, $demand);
        if (!empty($searchConstraints)) {
            $constraints['search'] = $query->logicalAnd($searchConstraints);
        }

        // Exclude already displayed
        if ($demand->getExcludeAlreadyDisplayedAddress() && isset($GLOBALS['EXT']['address']['alreadyDisplayed']) && !empty($GLOBALS['EXT']['address']['alreadyDisplayed'])) {
            $constraints['excludeAlreadyDisplayedAddress'] = $query->logicalNot(
                $query->in(
                    'uid',
                    $GLOBALS['EXT']['address']['alreadyDisplayed']
                )
            );
        }

        // Hide id list
        $hideIdList = $demand->getHideIdList();
        if ($hideIdList) {
            $constraints['excludeAlreadyDisplayedAddress'] = $query->logicalNot(
                $query->in(
                    'uid',
                    GeneralUtility::intExplode(',', $hideIdList)
                )
            );
        }

        // Clean not used constraints
        foreach ($constraints as $key => $value) {
            if (is_null($value)) {
                unset($constraints[$key]);
            }
        }

        return $constraints;
    }

    /**
     * Returns an array of orderings created from a given demand object.
     *
     * @param DemandInterface $demand
     * @return array<\TYPO3\CMS\Extbase\Persistence\Generic\Qom\ConstraintInterface>
     */
    protected function createOrderingsFromDemand(DemandInterface $demand)
    {
        $orderings = [];
        if ($demand->getTopAddressFirst()) {
            $orderings['istopaddress'] = QueryInterface::ORDER_DESCENDING;
        }

        if (Validation::isValidOrdering($demand->getOrder(), $demand->getOrderByAllowed())) {
            $orderList = GeneralUtility::trimExplode(',', $demand->getOrder(), true);

            if (!empty($orderList)) {
                // go through every order statement
                foreach ($orderList as $orderItem) {
                    list($orderField, $ascDesc) = GeneralUtility::trimExplode(' ', $orderItem, true);
                    // count == 1 means that no direction is given
                    if ($ascDesc) {
                        $orderings[$orderField] = ((strtolower($ascDesc) === 'desc') ?
                            QueryInterface::ORDER_DESCENDING :
                            QueryInterface::ORDER_ASCENDING);
                    } else {
                        $orderings[$orderField] = QueryInterface::ORDER_ASCENDING;
                    }
                }
            }
        }

        return $orderings;
    }

    /**
     * Find first address by import and source id
     *
     * @param string $importSource import source
     * @param int $importId import id
     * @return \WapplerSystems\Address\Domain\Model\Address
     */
    public function findOneByImportSourceAndImportId($importSource, $importId)
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setRespectStoragePage(false);
        $query->getQuerySettings()->setRespectSysLanguage(false);
        $query->getQuerySettings()->setIgnoreEnableFields(true);

        return $query->matching(
            $query->logicalAnd(
                $query->equals('importSource', $importSource),
                $query->equals('importId', $importId)
            ))->execute()->getFirst();
    }

    /**
     * Override default findByUid function to enable also the option to turn of
     * the enableField setting
     *
     * @param int $uid id of record
     * @param bool $respectEnableFields if set to false, hidden records are shown
     * @return \WapplerSystems\Address\Domain\Model\Address
     */
    public function findByUid($uid, $respectEnableFields = true)
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setRespectStoragePage(false);
        $query->getQuerySettings()->setRespectSysLanguage(false);
        $query->getQuerySettings()->setIgnoreEnableFields(!$respectEnableFields);

        return $query->matching(
            $query->logicalAnd(
                $query->equals('uid', $uid),
                $query->equals('deleted', 0)
            ))->execute()->getFirst();
    }



    /**
     * Get the search constraints
     *
     * @param QueryInterface $query
     * @param DemandInterface $demand
     * @return array
     * @throws \UnexpectedValueException
     */
    protected function getSearchConstraints(QueryInterface $query, DemandInterface $demand)
    {
        $constraints = [];
        if ($demand->getSearch() === null) {
            return $constraints;
        }

        /* @var $searchObject \WapplerSystems\Address\Domain\Model\Dto\Search */
        $searchObject = $demand->getSearch();

        $searchSubject = $searchObject->getSubject();
        if (!empty($searchSubject)) {
            $searchFields = GeneralUtility::trimExplode(',', $searchObject->getFields(), true);
            $searchConstraints = [];

            if (count($searchFields) === 0) {
                throw new \UnexpectedValueException('No search fields defined', 1318497755);
            }

            foreach ($searchFields as $field) {
                if (!empty($searchSubject)) {
                    $searchConstraints[] = $query->like($field, '%' . $searchSubject . '%');
                }
            }

            if (count($searchConstraints)) {
                $constraints[] = $query->logicalOr($searchConstraints);
            }
        }

        return $constraints;
    }
}
