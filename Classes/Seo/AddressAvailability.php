<?php

declare(strict_types=1);

/*
 * This file is part of the "address" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace WapplerSystems\Address\Seo;

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Routing\PageArguments;
use TYPO3\CMS\Core\Site\Entity\SiteInterface;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Check if a address record is available
 */
class AddressAvailability
{
    /**
     * @param int $languageId
     * @param int $addressId
     * @return bool
     */
    public function check(int $languageId, int $addressId = 0): bool
    {
        // get it from current request
        if ($addressId === 0) {
            $addressId = $this->getAddressIdFromRequest();
        }
        if ($addressId === 0) {
            throw new \UnexpectedValueException('No address id provided', 1586431984);
        }

        /** @var SiteInterface $site */
        $site = $this->getRequest()->getAttribute('site');
        $allAvailableLanguagesOfSite = $site->getAllLanguages();

        $targetLanguage = $this->getLanguageFromAllLanguages($allAvailableLanguagesOfSite, $languageId);
        if (!$targetLanguage) {
            throw new \UnexpectedValueException('Target language could not be found', 1586431985);
        }
        return $this->mustBeIncluded($addressId, $targetLanguage);
    }

    protected function mustBeIncluded(int $addressId, SiteLanguage $language): bool
    {
        if ($language->getFallbackType() === 'strict') {
            // @extensionScannerIgnoreLine
            $addressRecord = $this->getAddressRecord($addressId, $language->getLanguageId());

            if (!is_array($addressRecord) || empty($addressRecord)) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param SiteLanguage[] $allLanguages
     * @param int $languageId
     */
    protected function getLanguageFromAllLanguages(array $allLanguages, int $languageId): ?SiteLanguage
    {
        foreach ($allLanguages as $siteLanguage) {
            if ($siteLanguage->getLanguageId() === $languageId) {
                return $siteLanguage;
            }
        }
        return null;
    }

    protected function getAddressRecord(int $addressId, int $language)
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_address_domain_model_address');
        if ($language === 0) {
            $where = [
                $queryBuilder->expr()->or(
                    $queryBuilder->expr()->eq('sys_language_uid', $queryBuilder->createNamedParameter($language, \PDO::PARAM_INT)),
                    $queryBuilder->expr()->eq('sys_language_uid', $queryBuilder->createNamedParameter(-1, \PDO::PARAM_INT))
                ),
                $queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($addressId, \PDO::PARAM_INT)),
            ];
        } else {
            $where = [
                $queryBuilder->expr()->or(
                    $queryBuilder->expr()->and(
                        $queryBuilder->expr()->eq('sys_language_uid', $queryBuilder->createNamedParameter(-1, \PDO::PARAM_INT)),
                        $queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($addressId, \PDO::PARAM_INT))
                    ),
                    $queryBuilder->expr()->and(
                        $queryBuilder->expr()->eq('l10n_parent', $queryBuilder->createNamedParameter($addressId, \PDO::PARAM_INT)),
                        $queryBuilder->expr()->eq('sys_language_uid', $queryBuilder->createNamedParameter($language, \PDO::PARAM_INT))
                    ),
                    $queryBuilder->expr()->and(
                        $queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($addressId, \PDO::PARAM_INT)),
                        $queryBuilder->expr()->eq('l10n_parent', $queryBuilder->createNamedParameter(0, \PDO::PARAM_INT)),
                        $queryBuilder->expr()->eq('sys_language_uid', $queryBuilder->createNamedParameter($language, \PDO::PARAM_INT))
                    )
                ),
            ];
        }

        $row = $queryBuilder
            ->select('uid', 'l10n_parent', 'sys_language_uid')
            ->from('tx_address_domain_model_address')
            ->where(...$where)
            ->executeQuery()->fetchAssociative();

        return $row ?: null;
    }

    /**
     * @return ServerRequestInterface
     */
    protected function getRequest(): ServerRequestInterface
    {
        return $GLOBALS['TYPO3_REQUEST'];
    }

    public function getAddressIdFromRequest(): int
    {
        $addressId = 0;
        /** @var PageArguments $pageArguments */
        $pageArguments = $this->getRequest()->getAttribute('routing');
        if (isset($pageArguments, $pageArguments->getRouteArguments()['tx_address_pi1']['address'])) {
            $addressId = (int)$pageArguments->getRouteArguments()['tx_address_pi1']['address'];
        } elseif (isset($this->getRequest()->getQueryParams()['tx_address_pi1']['address'])) {
            $addressId = (int)$this->getRequest()->getQueryParams()['tx_address_pi1']['address'];
        }
        return $addressId;
    }
}
