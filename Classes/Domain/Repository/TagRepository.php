<?php

namespace WapplerSystems\Address\Domain\Repository;

/**
 * This file is part of the "address" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException;
use TYPO3\CMS\Extbase\Persistence\Generic\Qom\ConstraintInterface;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use WapplerSystems\Address\Domain\Model\DemandInterface;
use WapplerSystems\Address\Utility\Validation;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;

/**
 * Repository for tag objects
 */
class TagRepository extends AbstractDemandedRepository
{

    /**
     * Find categories by a given pid
     *
     * @param array $idList list of id s
     * @param array $ordering ordering
     * @param string|null $startingPoint starting point uid or comma separated list
     * @return array|QueryResultInterface|object[]
     * @throws InvalidQueryException
     */
    public function findByIdList(array $idList, array $ordering = [], ?string $startingPoint = null): array|QueryResultInterface
    {
        if (empty($idList)) {
            throw new \InvalidArgumentException('The given id list is empty.', 1484823596);
        }
        $query = $this->createQuery();
        $query->getQuerySettings()->setRespectStoragePage(false);
        $query->getQuerySettings()->setRespectSysLanguage(false);

        if (count($ordering) > 0) {
            $query->setOrderings($ordering);
        }

        $conditions = [];
        $conditions[] = $query->in('uid', $idList);

        if ($startingPoint !== null) {
            $conditions[] = $query->in('pid', GeneralUtility::trimExplode(',', $startingPoint, true));
        }

        return $query->matching(
            $query->logicalAnd(
                ...$conditions
            ))->execute();
    }

    /**
     * Returns an array of constraints created from a given demand object.
     *
     * @param QueryInterface $query
     * @param DemandInterface $demand
     * @return array<ConstraintInterface>
     * @throws InvalidQueryException
     */
    protected function createConstraintsFromDemand(QueryInterface $query, DemandInterface $demand): array
    {
        $constraints = [];

        if (count($demand->getStoragePage()) > 0) {
            $constraints[] = $query->in('pid', $demand->getStoragePage());
        }

        if (count($demand->getTags()) > 0) {
            $constraints[] = $query->in('uid', $demand->getTags());
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
     * @return array<ConstraintInterface>
     */
    protected function createOrderingsFromDemand(DemandInterface $demand): array
    {
        $orderings = [];

        if (Validation::isValidOrdering($demand->getOrder(), $demand->getOrderByAllowed())) {
            $orderList = GeneralUtility::trimExplode(',', $demand->getOrder(), true);

            if (!empty($orderList)) {
                // go through every order statement
                foreach ($orderList as $orderItem) {
                    [$orderField, $ascDesc] = GeneralUtility::trimExplode(' ', $orderItem, true);
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
}
