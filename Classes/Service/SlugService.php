<?php

namespace WapplerSystems\Address\Service;

use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;
use TYPO3\CMS\Core\DataHandling\SlugHelper;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * This file is part of the "address" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */
class SlugService
{

    /** @var SlugHelper */
    protected $slugService;

    public function __construct()
    {
        $fieldConfig = $GLOBALS['TCA']['tx_address_domain_model_address']['columns']['path_segment']['config'];
        $this->slugService = GeneralUtility::makeInstance(SlugHelper::class, 'tx_address_domain_model_address', 'path_segment', $fieldConfig);
    }

    /**
     * @return int
     */
    public function countOfSlugUpdates(): int
    {
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_address_domain_model_address');
        $queryBuilder->getRestrictions()->removeAll();
        $elementCount = $queryBuilder->count('uid')
            ->from('tx_address_domain_model_address')
            ->where(
                $queryBuilder->expr()->orX(
                    $queryBuilder->expr()->eq('path_segment', $queryBuilder->createNamedParameter('', \PDO::PARAM_STR)),
                    $queryBuilder->expr()->isNull('path_segment')
                )
            )
            ->executeQuery()->fetchOne();

        return $elementCount;
    }

    /**
     * @return array
     */
    public function performUpdates(): array
    {
        $databaseQueries = [];

        /** @var Connection $connection */
        $connection = GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable('tx_address_domain_model_address');
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = $connection->createQueryBuilder();
        $queryBuilder->getRestrictions()->removeAll();
        $statement = $queryBuilder->select('*')
            ->from('tx_address_domain_model_address')
            ->where(
                $queryBuilder->expr()->or(
                    $queryBuilder->expr()->eq('path_segment', $queryBuilder->createNamedParameter('', \PDO::PARAM_STR)),
                    $queryBuilder->expr()->isNull('path_segment')
                )
            )
            ->executeQuery();
        while ($record = $statement->fetchAssociative()) {
            $slug = $this->slugService->generate($record, $record['pid']);
            if ($slug !== '') {
                /** @var QueryBuilder $queryBuilder */
                $queryBuilder = $connection->createQueryBuilder();
                $queryBuilder->update('tx_address_domain_model_address')
                    ->where(
                        $queryBuilder->expr()->eq(
                            'uid',
                            $queryBuilder->createNamedParameter($record['uid'], \PDO::PARAM_INT)
                        )
                    )
                    ->set('path_segment', $this->getUniqueValue($record['uid'], $record['sys_language_uid'], $slug));
                $databaseQueries[] = $queryBuilder->getSQL();
                $queryBuilder->executeStatement();
            }
        }

        return $databaseQueries;
    }

    /**
     * @param int $uid
     * @param string $slug
     * @return string
     */
    protected function getUniqueValue(int $uid, int $languageId, string $slug): string
    {
        $queryBuilder = $this->getUniqueCountStatement($uid, $languageId, $slug);
        // For as long as records with the test-value existing, try again (with incremented numbers appended)
        $statement = $queryBuilder->prepare();
        $result = $statement->executeQuery();
        if ($result->fetchOne()) {
            for ($counter = 0; $counter <= 100; $counter++) {
                $result->free();
                $newSlug = $slug . '-' . $counter;
                $statement->bindValue(1, $newSlug);
                $result = $statement->executeQuery();
                if (!$result->fetchOne()) {
                    break;
                }
            }
            $result->free();
        }

        return $newSlug ?? $slug;
    }

    /**
     * @param int $uid
     * @param string $slug
     * @return QueryBuilder
     */
    protected function getUniqueCountStatement(int $uid, string $slug)
    {
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_address_domain_model_address');
        /** @var DeletedRestriction $deleteRestriction */
        $deleteRestriction = GeneralUtility::makeInstance(DeletedRestriction::class);
        $queryBuilder->getRestrictions()->removeAll()->add($deleteRestriction);

        return $queryBuilder
            ->count('uid')
            ->from('tx_address_domain_model_address')
            ->where(
                $queryBuilder->expr()->eq(
                    'path_segment',
                    $queryBuilder->createPositionalParameter($slug, \PDO::PARAM_STR)
                ),
                $queryBuilder->expr()->neq('uid', $queryBuilder->createPositionalParameter($uid, \PDO::PARAM_INT))
            );
    }

}
