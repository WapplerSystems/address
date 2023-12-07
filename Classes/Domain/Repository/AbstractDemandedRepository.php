<?php

namespace WapplerSystems\Address\Domain\Repository;

/**
 * This file is part of the "address" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\Qom\ConstraintInterface;
use TYPO3\CMS\Extbase\Persistence\Generic\Storage\BackendInterface;
use TYPO3\CMS\Extbase\Persistence\Generic\Storage\Typo3DbQueryParser;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;
use WapplerSystems\Address\Domain\Model\DemandInterface;

/**
 * Abstract demanded repository
 *
 */
abstract class AbstractDemandedRepository extends Repository implements DemandedRepositoryInterface
{


    public function __construct(readonly BackendInterface $storageBackend)
    {
        parent::__construct();
    }

    /**
     * Returns an array of constraints created from a given demand object.
     *
     * @param QueryInterface $query
     * @param DemandInterface $demand
     * @return ConstraintInterface[]
     * @abstract
     */
    abstract protected function createConstraintsFromDemand(
        QueryInterface $query,
        DemandInterface $demand
    ): array;

    /**
     * Returns an array of orderings created from a given demand object.
     *
     * @param DemandInterface $demand
     * @return ConstraintInterface[]
     * @abstract
     */
    abstract protected function createOrderingsFromDemand(DemandInterface $demand): array;

    /**
     * Returns the objects of this repository matching the demand.
     *
     * @param DemandInterface $demand
     * @param bool $respectEnableFields
     * @param bool $disableLanguageOverlayMode
     * @return \TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     */
    public function findDemanded(DemandInterface $demand, $respectEnableFields = true, $disableLanguageOverlayMode = false)
    {
        $query = $this->generateQuery($demand, $respectEnableFields, $disableLanguageOverlayMode);

        return $query->execute();
    }

    /**
     * Returns the database query to get the matching result
     *
     * @param DemandInterface $demand
     * @param bool $respectEnableFields
     * @param bool $disableLanguageOverlayMode
     * @return string
     */
    public function findDemandedRaw(DemandInterface $demand, $respectEnableFields = true, $disableLanguageOverlayMode = false)
    {
        $query = $this->generateQuery($demand, $respectEnableFields, $disableLanguageOverlayMode);
        $queryParser = GeneralUtility::makeInstance(Typo3DbQueryParser::class);

        $queryBuilder = $queryParser->convertQueryToDoctrineQueryBuilder($query);
        $queryParameters = $queryBuilder->getParameters();
        $params = [];
        foreach ($queryParameters as $key => $value) {
            // prefix array keys with ':'
            $params[':' . $key] = (is_numeric($value)) ? $value : "'" . $value . "'"; //all non numeric values have to be quoted
            unset($params[$key]);
        }
        // replace placeholders with real values
        $query = strtr($queryBuilder->getSQL(), $params);
        return $query;
    }

    /**
     * @param DemandInterface $demand
     * @param bool $respectEnableFields
     * @param bool $disableLanguageOverlayMode
     * @return QueryInterface
     */
    protected function generateQuery(DemandInterface $demand, bool $respectEnableFields = true, bool $disableLanguageOverlayMode = false): QueryInterface
    {
        $query = $this->createQuery();

        $query->getQuerySettings()->setRespectStoragePage(false);

        if ($disableLanguageOverlayMode) {
            $query->getQuerySettings()->setLanguageOverlayMode(false);
        }

        $constraints = $this->createConstraintsFromDemand($query, $demand);

        // Call hook functions for additional constraints
        if ($hooks = $GLOBALS['TYPO3_CONF_VARS']['EXT']['address']['Domain/Repository/AbstractDemandedRepository.php']['findDemanded'] ?? []) {
            $params = [
                'demand' => $demand,
                'respectEnableFields' => &$respectEnableFields,
                'query' => $query,
                'constraints' => &$constraints,
            ];
            foreach ($hooks as $reference) {
                GeneralUtility::callUserFunction($reference, $params, $this);
            }
        }

        if ($demand->getIds()) {
            $constraints[] = $query->in('uid', $demand->getIds());
        }

        if ($respectEnableFields === false) {
            $query->getQuerySettings()->setIgnoreEnableFields(true);

            $constraints[] = $query->equals('deleted', 0);
        }

        if (!empty($constraints)) {
            $query->matching(
                $query->logicalAnd(...array_values($constraints))
            );
        }

        if ($orderings = $this->createOrderingsFromDemand($demand)) {
            $query->setOrderings($orderings);
        }

        // @todo consider moving this to a separate function as well
        if ($demand->getLimit() != null) {
            $query->setLimit($demand->getLimit());
        }

        // @todo consider moving this to a separate function as well
        if ($demand->getOffset() != null) {
            if (!$query->getLimit()) {
                $query->setLimit(PHP_INT_MAX);
            }
            $query->setOffset($demand->getOffset());
        }

        return $query;
    }

    /**
     * Returns the total number objects of this repository matching the demand.
     *
     * @param DemandInterface $demand
     * @return int
     */
    public function countDemanded(DemandInterface $demand)
    {
        $query = $this->createQuery();

        if ($constraints = $this->createConstraintsFromDemand($query, $demand)) {
            $query->matching(
                $query->logicalAnd(...$constraints)
            );
        }

        return $query->execute()->count();
    }
}
