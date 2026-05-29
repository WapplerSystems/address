<?php
declare(strict_types=1);

namespace WapplerSystems\Address\Backend;

/**
 * This file is part of the "address" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Backend\Utility\BackendUtility as BackendUtilityCore;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\DataHandling\DataHandler as DataHandlerCore;
use TYPO3\CMS\Core\Http\JsonResponse;
use TYPO3\CMS\Core\Http\Response;
use TYPO3\CMS\Core\Localization\LanguageServiceFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use WapplerSystems\Address\Utility\EmConfiguration;

/**
 * Ajax response for the custom suggest receiver. Creates a tag record if no
 * matching one exists yet for the given address page, and returns the dash-
 * separated tuple expected by the BE suggest JS module.
 */
class TagEndPoint
{
    public const TAG = 'tx_address_domain_model_tag';
    public const ADDRESS = 'tx_address_domain_model_address';
    public const LL_PATH = 'LLL:EXT:address/Resources/Private/Language/locallang_be.xlf:tag_suggest_';

    public function __construct(
        private readonly ConnectionPool $connectionPool,
        private readonly LanguageServiceFactory $languageServiceFactory,
    ) {}

    public function create(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $parsed = $request->getParsedBody();
            $query = $request->getQueryParams();
            $item = is_array($parsed) && isset($parsed['item']) ? (string)$parsed['item'] : (string)($query['item'] ?? '');

            if ($item === '') {
                throw new \RuntimeException('error_no-tag');
            }

            $addressUid = is_array($parsed) && isset($parsed['addressid']) ? (int)$parsed['addressid'] : (int)($query['addressid'] ?? 0);
            if ($addressUid === 0) {
                throw new \RuntimeException('error_no-addressid');
            }

            $newTagId = $this->getTagUid($item, $addressUid);

            $content = [
                $newTagId,
                $item,
                self::TAG,
                self::ADDRESS,
                'tags',
                'data[tx_address_domain_model_address][' . $addressUid . '][tags]',
                $addressUid,
            ];
            $response = new Response();
            $response->getBody()->write(implode('-', $content));
            return $response;
        } catch (\Throwable $e) {
            // Resolve the translated message via the LanguageService factory
            // — $GLOBALS['LANG'] is not guaranteed in v14 ajax contexts.
            $language = $this->languageServiceFactory->createFromUserPreferences($GLOBALS['BE_USER'] ?? null);
            $message = $language->sL(self::LL_PATH . $e->getMessage());
            if ($message === '') {
                $message = $e->getMessage();
            }
            return new JsonResponse(['error' => $message], 400);
        }
    }

    /**
     * Get the uid of the tag, either by inserting a new record or by reusing
     * the existing one with the same title on the configured tag storage pid.
     *
     * @throws \RuntimeException when the storage pid is not configured or the
     *         DataHandler refuses to create the record
     */
    protected function getTagUid(string $title, int $addressUid): int
    {
        $configuration = EmConfiguration::getSettings();

        $pid = (int)$configuration->getTagPid();
        if ($pid === 0) {
            $pid = $this->getTagPidFromTsConfig($addressUid);
        }

        if ($pid === 0) {
            throw new \RuntimeException('error_no-pid-defined');
        }

        $queryBuilder = $this->connectionPool->getQueryBuilderForTable(self::TAG);
        // We need rows regardless of `hidden`/`disabled` flags but obey the
        // soft-delete column — same semantics as the old `deleted=0` literal.
        $queryBuilder->getRestrictions()->removeAll();
        $row = $queryBuilder
            ->select('uid')
            ->from(self::TAG)
            ->where(
                $queryBuilder->expr()->eq('deleted', $queryBuilder->createNamedParameter(0, \PDO::PARAM_INT)),
                $queryBuilder->expr()->eq('pid', $queryBuilder->createNamedParameter($pid, \PDO::PARAM_INT)),
                $queryBuilder->expr()->eq('title', $queryBuilder->createNamedParameter($title)),
            )
            ->setMaxResults(1)
            ->executeQuery()
            ->fetchAssociative();

        if (is_array($row) && isset($row['uid'])) {
            return (int)$row['uid'];
        }

        $tcemainData = [
            self::TAG => [
                'NEW' => [
                    'pid' => $pid,
                    'title' => $title,
                ],
            ],
        ];

        $dataHandler = GeneralUtility::makeInstance(DataHandlerCore::class);
        $dataHandler->start($tcemainData, []);
        $dataHandler->process_datamap();

        $tagUid = (int)($dataHandler->substNEWwithIDs['NEW'] ?? 0);
        if ($tagUid === 0) {
            throw new \RuntimeException('error_no-tag-created');
        }
        return $tagUid;
    }

    /**
     * Resolve the tag storage pid from page TSconfig at the address record's
     * page. Returns 0 when nothing is configured — caller treats that as an
     * error.
     */
    protected function getTagPidFromTsConfig(int $addressUid): int
    {
        $addressRecord = BackendUtilityCore::getRecord(self::ADDRESS, $addressUid);
        if (!is_array($addressRecord)) {
            return 0;
        }
        $pagesTsConfig = BackendUtilityCore::getPagesTSconfig((int)($addressRecord['pid'] ?? 0));
        return (int)($pagesTsConfig['tx_address.']['tagPid'] ?? 0);
    }
}
