<?php

declare(strict_types=1);

/*
 * This file is part of the "news" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace WapplerSystems\Address\Updates;

use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;
use TYPO3\CMS\Core\Service\FlexFormService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Install\Attribute\UpgradeWizard;
use TYPO3\CMS\Install\Updates\DatabaseUpdatedPrerequisite;
use TYPO3\CMS\Install\Updates\UpgradeWizardInterface;

#[UpgradeWizard('addressMigrateToContactsUpdater')]
class MigrateToContactsUpdater implements UpgradeWizardInterface
{


    public function __construct()
    {
    }

    public function getIdentifier(): string
    {
        return 'addressMigrateToContactsUpdater';
    }

    public function getTitle(): string
    {
        return 'EXT:address: Migrate address fields to contacts records';
    }

    public function getDescription(): string
    {
        return '';
    }

    public function getPrerequisites(): array
    {
        return [
            DatabaseUpdatedPrerequisite::class,
        ];
    }

    public function updateNecessary(): bool
    {
        return $this->checkIfWizardIsRequired();
    }

    public function executeUpdate(): bool
    {
        return $this->performMigration();
    }

    public function checkIfWizardIsRequired(): bool
    {
        return count($this->getMigrationRecords()) > 0;
    }

    public function performMigration(): bool
    {
        $records = $this->getMigrationRecords();

        foreach ($records as $record) {
            $this->updateContentElement($record);
        }

        return true;
    }

    protected function getMigrationRecords(): array
    {
        $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
        $queryBuilder = $connectionPool->getQueryBuilderForTable('tx_address_domain_model_address');
        $queryBuilder->getRestrictions()->removeAll();

        return $queryBuilder
            ->select('uid', 'pid', 'phone', 'fax', 'email', 'www')
            ->from('tx_address_domain_model_address')
            ->where(
                $queryBuilder->expr()->or(
                    $queryBuilder->expr()->neq(
                        'phone',
                        $queryBuilder->createNamedParameter('')
                    ),
                    $queryBuilder->expr()->neq(
                        'fax',
                        $queryBuilder->createNamedParameter('')
                    ),
                    $queryBuilder->expr()->neq(
                        'email',
                        $queryBuilder->createNamedParameter('')
                    ),
                    $queryBuilder->expr()->neq(
                        'www',
                        $queryBuilder->createNamedParameter('')
                    )
                )
            )
            ->executeQuery()
            ->fetchAllAssociative();
    }



    /**
     *
     */
    protected function updateContentElement(array $record): void
    {

        $queryBuilderContact = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_address_domain_model_contact');

        $queryBuilderAddress = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_address_domain_model_address');
        $queryBuilderAddress->getRestrictions()->removeAll();

        if ($record['phone'] !== '') {
            $queryBuilderContact->insert('tx_address_domain_model_contact')
                ->values([
                    'pid' => (int)$record['pid'],
                    'content' => $record['phone'],
                    'type' => 'telephone',
                    'address' => (int)$record['uid'],
                ])
                ->executeStatement();
        }
        $queryBuilderAddress->update('tx_address_domain_model_address')
            ->set('phone', '')
            ->where(
                $queryBuilderAddress->expr()->eq(
                    'uid',
                    $queryBuilderAddress->createNamedParameter($record['uid'], Connection::PARAM_INT)
                )
            )
            ->executeStatement();

        if ($record['fax'] !== '') {
            $queryBuilderContact->insert('tx_address_domain_model_contact')
                ->values([
                    'pid' => (int)$record['pid'],
                    'content' => $record['fax'],
                    'type' => 'fax',
                    'address' => (int)$record['uid'],
                ])
                ->executeStatement();
        }
        $queryBuilderAddress->update('tx_address_domain_model_address')
            ->set('fax', '')
            ->where(
                $queryBuilderAddress->expr()->eq(
                    'uid',
                    $queryBuilderAddress->createNamedParameter($record['uid'], Connection::PARAM_INT)
                )
            )
            ->executeStatement();

        if ($record['email'] !== '') {
            $queryBuilderContact->insert('tx_address_domain_model_contact')
                ->values([
                    'pid' => (int)$record['pid'],
                    'content' => $record['email'],
                    'type' => 'email',
                    'address' => (int)$record['uid'],
                ])
                ->executeStatement();
        }
        $queryBuilderAddress->update('tx_address_domain_model_address')
            ->set('email', '')
            ->where(
                $queryBuilderAddress->expr()->eq(
                    'uid',
                    $queryBuilderAddress->createNamedParameter($record['uid'], Connection::PARAM_INT)
                )
            )
            ->executeStatement();

        if ($record['www'] !== '') {
            $queryBuilderContact->insert('tx_address_domain_model_contact')
                ->values([
                    'pid' => (int)$record['pid'],
                    'content' => $record['www'],
                    'type' => 'website',
                    'address' => (int)$record['uid'],
                ])
                ->executeStatement();
        }
        $queryBuilderAddress->update('tx_address_domain_model_address')
            ->set('www', '')
            ->where(
                $queryBuilderAddress->expr()->eq(
                    'uid',
                    $queryBuilderAddress->createNamedParameter($record['uid'], Connection::PARAM_INT)
                )
            )
            ->executeStatement();

    }


}
