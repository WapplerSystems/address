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
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Context\WorkspaceAspect;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\Restriction\WorkspaceRestriction;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Seo\XmlSitemap\AbstractXmlSitemapDataProvider;
use TYPO3\CMS\Seo\XmlSitemap\Exception\MissingConfigurationException;

/**
 * Generate sitemap for address records
 */
class AddressXmlSitemapDataProvider extends AbstractXmlSitemapDataProvider
{
    /**
     * The number of all elements
     *
     * @var int
     */
    protected $itemCount = 0;

    /**
     * @param ServerRequestInterface $request
     * @param string $key
     * @param array $config
     * @param ContentObjectRenderer|null $cObj
     * @throws MissingConfigurationException
     */
    public function __construct(ServerRequestInterface $request, string $key, array $config = [], ContentObjectRenderer $cObj = null)
    {
        parent::__construct($request, $key, $config, $cObj);

        $this->generateItems();
    }

    /**
     * @throws MissingConfigurationException
     */
    public function generateItems(): void
    {
        $table = 'tx_address_domain_model_address';

        $pids = !empty($this->config['pid']) ? GeneralUtility::intExplode(',', $this->config['pid']) : [];
        $lastModifiedField = $this->config['lastModifiedField'] ?? 'tstamp';
        $sortField = $this->config['sortField'] ?? 'datetime';
        $forGoogleAddress = $this->config['googleAddress'] ?? false;

        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable($table);

        $constraints = [];
        if (!empty($GLOBALS['TCA'][$table]['ctrl']['languageField'])) {
            $constraints[] = $queryBuilder->expr()->in(
                $GLOBALS['TCA'][$table]['ctrl']['languageField'],
                [
                    -1, // All languages
                    // @extensionScannerIgnoreLine
                    $this->getLanguageId(),  // Current language
                ]
            );
        }

        if (!empty($pids)) {
            $recursiveLevel = isset($this->config['recursive']) ? (int)$this->config['recursive'] : 0;
            if ($recursiveLevel) {
                $newList = [];
                foreach ($pids as $pid) {
                    $list = $this->cObj->getTreeList($pid, $recursiveLevel);
                    if ($list) {
                        $newList = array_merge($newList, explode(',', $list));
                    }
                }
                $pids = array_merge($pids, $newList);
            }

            $constraints[] = $queryBuilder->expr()->in('pid', $pids);
        }

        if ($forGoogleAddress) {
            $constraints[] = $queryBuilder->expr()->gt($sortField, (new \DateTime('-2 days'))->getTimestamp());
        }

        if (!empty($this->config['excludedTypes'])) {
            $excludedTypes = GeneralUtility::trimExplode(',', $this->config['excludedTypes'], true);
            if (!empty($excludedTypes)) {
                $constraints[] = $queryBuilder->expr()->notIn(
                    'type',
                    $queryBuilder->createNamedParameter($excludedTypes, Connection::PARAM_STR_ARRAY)
                );
            }
        }

        if (!empty($this->config['additionalWhere'])) {
            $constraints[] = $this->config['additionalWhere'];
        }

        $queryBuilder->getRestrictions()->add(
            GeneralUtility::makeInstance(WorkspaceRestriction::class, $this->getCurrentWorkspaceAspect()->getId())
        );

        $queryBuilder->select('*')
            ->from($table);

        if (!empty($constraints)) {
            $queryBuilder->where(
                ...$constraints
            );
        }

        // Count all items
        $queryBuilder->count('*');
        $this->itemCount = $queryBuilder->executeQuery()->fetchOne();

        // Select only the right range
        $queryBuilder->select('*');
        $pageNumber = (int)($this->request->getQueryParams()['page'] ?? 0);
        $page = $pageNumber > 0 ? $pageNumber : 0;
        $queryBuilder
            ->setFirstResult($page * $this->numberOfItemsPerPage)
            ->setMaxResults($this->numberOfItemsPerPage);

        $rows = $queryBuilder->orderBy($sortField, $forGoogleAddress ? 'DESC' : 'ASC')
            ->executeQuery()->fetchAllAssociative();

        foreach ($rows as $row) {
            $this->items[] = [
                'data' => $row,
                'lastMod' => (int)$row[$lastModifiedField],
                'priority' => 0.5,
            ];
        }
    }

    /**
     * Get the current items
     *
     * @return array
     */
    public function getItems(): array
    {
        return array_map([$this, 'defineUrl'], $this->items);
    }

    /**
     * Get the number of pages
     *
     * @return int
     */
    public function getNumberOfPages(): int
    {
        return (int)ceil($this->itemCount / $this->numberOfItemsPerPage);
    }

    /**
     * @param array $data
     * @return array
     */
    protected function defineUrl(array $data): array
    {
        // @extensionScannerIgnoreLine
        $pageId = $this->config['url']['pageId'] ?? $GLOBALS['TSFE']->id;
        if (($this->config['url']['useCategorySinglePid'] ?? false) && $pageIdFromCategory = $this->getSinglePidFromCategory($data['data']['uid'])) {
            $pageId = $pageIdFromCategory;
        }

        $additionalParams = [];
        $additionalParams = $this->getUrlFieldParameterMap($additionalParams, $data['data']);
        $additionalParams = $this->getUrlAdditionalParams($additionalParams);

        if (!empty($this->config['url']['hrDate']) && !empty($data['data']['datetime'])) {
            // adjust timezone (database field is UTC)
            $timezoneCorrectedDatetime = (int)$data['data']['datetime'] + date('Z', (int)$data['data']['datetime']);
            $dateTime = \DateTime::createFromFormat('U', (string)$timezoneCorrectedDatetime);
            if (!empty($this->config['url']['hrDate']['day'])) {
                $additionalParams['tx_address_pi1[day]'] = $dateTime->format($this->config['url']['hrDate']['day']);
            }
            if (!empty($this->config['url']['hrDate']['month'])) {
                $additionalParams['tx_address_pi1[month]'] = $dateTime->format($this->config['url']['hrDate']['month']);
            }
            if (!empty($this->config['url']['hrDate']['year'])) {
                $additionalParams['tx_address_pi1[year]'] = $dateTime->format($this->config['url']['hrDate']['year']);
            }
        }

        $additionalParamsString = http_build_query(
            $additionalParams,
            '',
            '&',
            PHP_QUERY_RFC3986
        );

        $typoLinkConfig = [
            'parameter' => $pageId,
            'additionalParams' => $additionalParamsString ? '&' . $additionalParamsString : '',
            'forceAbsoluteUrl' => 1,
        ];

        $data['loc'] = $this->cObj->typoLink_URL($typoLinkConfig);

        return $data;
    }

    /**
     * Obtains a pid for the single view from the category.
     *
     * @param int $addressId
     * @return int
     */
    protected function getSinglePidFromCategory(int $addressId): int
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('sys_category');
        $categoryRecord = $queryBuilder
            ->select('title', 'single_pid')
            ->from('sys_category')
            ->leftJoin(
                'sys_category',
                'sys_category_record_mm',
                'sys_category_record_mm',
                $queryBuilder->expr()->eq('sys_category_record_mm.uid_local', $queryBuilder->quoteIdentifier('sys_category.uid'))
            )
            ->where(
                $queryBuilder->expr()->eq('sys_category_record_mm.tablenames', $queryBuilder->createNamedParameter('tx_address_domain_model_address', \PDO::PARAM_STR)),
                $queryBuilder->expr()->gt('sys_category.single_pid', $queryBuilder->createNamedParameter(0, \PDO::PARAM_INT)),
                $queryBuilder->expr()->eq('sys_category_record_mm.uid_foreign', $queryBuilder->createNamedParameter($addressId, \PDO::PARAM_INT))
            )
            ->setMaxResults(1)
            ->executeQuery()->fetchAssociative();
        return (int)($categoryRecord['single_pid'] ?? 0);
    }

    /**
     * @param array $additionalParams
     * @param array $data
     * @return array
     */
    protected function getUrlFieldParameterMap(array $additionalParams, array $data): array
    {
        if (!empty($this->config['url']['fieldToParameterMap']) &&
            \is_array($this->config['url']['fieldToParameterMap'])) {
            foreach ($this->config['url']['fieldToParameterMap'] as $field => $urlPart) {
                $additionalParams[$urlPart] = $data[$field];
            }
        }

        return $additionalParams;
    }

    /**
     * @param array $additionalParams
     * @return array
     */
    protected function getUrlAdditionalParams(array $additionalParams): array
    {
        if (!empty($this->config['url']['additionalGetParameters']) &&
            is_array($this->config['url']['additionalGetParameters'])) {
            foreach ($this->config['url']['additionalGetParameters'] as $extension => $extensionConfig) {
                foreach ($extensionConfig as $key => $value) {
                    $additionalParams[$extension . '[' . $key . ']'] = $value;
                }
            }
        }

        return $additionalParams;
    }

    /**
     * @return int
     * @throws \TYPO3\CMS\Core\Context\Exception\AspectNotFoundException
     */
    protected function getLanguageId(): int
    {
        $context = GeneralUtility::makeInstance(Context::class);
        return (int)$context->getPropertyFromAspect('language', 'id');
    }

    /**
     * @return WorkspaceAspect
     */
    protected function getCurrentWorkspaceAspect(): WorkspaceAspect
    {
        return GeneralUtility::makeInstance(Context::class)->getAspect('workspace');
    }
}
