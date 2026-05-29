<?php
declare(strict_types=1);

namespace WapplerSystems\Address\Service;

/**
 * This file is part of the "address" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\TimeTracker\TimeTracker;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Service for sys_category tree resolution + small helpers.
 *
 * Modern API (post v14 audit):
 *  - {@see getChildren()}       — recursively collect descendant category ids
 *  - {@see getRootline()}       — walk parent chain up to the root
 *  - {@see removeFromList()}    — comma-list set-difference
 *  - {@see translateRecord()}   — BE-only localised label lookup
 *
 * Legacy static methods (getChildrenCategories, getRootlineRecursive,
 * removeValuesFromString, translateCategoryRecord) are kept as thin shims
 * that delegate to the instance for backwards compatibility with the two
 * Repository callers and any third-party extensions. They are marked
 * @deprecated and will be removed in a future major.
 */
class CategoryService
{
    private const TABLE = 'sys_category';
    private const RECURSION_LIMIT = 10000;
    private const CACHE_NAME = 'address_category';

    public function __construct(
        private readonly ConnectionPool $connectionPool,
        private readonly CacheManager $cacheManager,
    ) {}

    /**
     * Collect the comma-separated descendant ids for a list of starting
     * category uids. Cached by input id list, walked depth-first.
     *
     * @param string $idList comma-separated list of starting uids
     * @param bool $removeStartingIds when true, the input ids are removed
     *        from the result — useful for callers that want strict descendants
     */
    public function getChildren(string $idList, bool $removeStartingIds = false): string
    {
        $cache = $this->getCache();
        $cacheIdentifier = sha1('children-' . $idList);

        $entry = $cache->get($cacheIdentifier);
        if (!is_string($entry)) {
            $entry = $this->getChildrenRecursive($idList, 0);
            $cache->set($cacheIdentifier, $entry);
        }

        return $removeStartingIds ? $this->removeFromList($entry, $idList) : $entry;
    }

    /**
     * Walk the parent chain of $id and return a comma-separated list of all
     * ancestor uids, parent-first, then the parent's parent and so on.
     */
    public function getRootline(int $id): string
    {
        $cache = $this->getCache();
        $cacheIdentifier = sha1('rootline-' . $id);

        $entry = $cache->get($cacheIdentifier);
        if (!is_string($entry)) {
            $entry = $this->getRootlineRecursiveInternal($id, 0);
            $cache->set($cacheIdentifier, $entry);
        }
        return $entry;
    }

    /**
     * Comma-list set difference. Returns the elements of $list that are not
     * present in $remove.
     */
    public function removeFromList(string $list, string $remove): string
    {
        $listArr = GeneralUtility::trimExplode(',', $list, true);
        $removeArr = GeneralUtility::trimExplode(',', $remove, true);
        return implode(',', array_diff($listArr, $removeArr));
    }

    /**
     * Translate a category row to the BE user's overlay language. When no
     * overlay exists or we're not in a BE context, returns $default.
     *
     * @param array<string,mixed> $row sys_category row
     */
    public function translateRecord(string $default, array $row = []): string
    {
        $beUser = $GLOBALS['BE_USER'] ?? null;
        if (!is_object($beUser) || !isset($beUser->uc['addressoverlay'])) {
            return $default;
        }
        $overlayLanguage = (int)$beUser->uc['addressoverlay'];
        $uid = (int)($row['uid'] ?? 0);
        $sysLanguageUid = (int)($row['sys_language_uid'] ?? 0);
        if ($uid === 0 || $overlayLanguage === 0 || $sysLanguageUid !== 0) {
            return $default;
        }

        $qb = $this->connectionPool->getQueryBuilderForTable(self::TABLE);
        $qb->getRestrictions()->removeAll();
        $overlay = $qb
            ->select('title')
            ->from(self::TABLE)
            ->where(
                $qb->expr()->eq('deleted', $qb->createNamedParameter(0, \PDO::PARAM_INT)),
                $qb->expr()->eq('sys_language_uid', $qb->createNamedParameter($overlayLanguage, \PDO::PARAM_INT)),
                $qb->expr()->eq('l10n_parent', $qb->createNamedParameter($uid, \PDO::PARAM_INT)),
            )
            ->setMaxResults(1)
            ->executeQuery()
            ->fetchAssociative();

        if (is_array($overlay) && isset($overlay['title']) && $overlay['title'] !== '') {
            return $overlay['title'] . ' (' . ($row['title'] ?? '') . ')';
        }
        return $default;
    }

    private function getChildrenRecursive(string $idList, int $counter): string
    {
        $startingIds = array_filter(array_map('intval', GeneralUtility::trimExplode(',', $idList, true)));
        if ($startingIds === []) {
            return '';
        }

        $result = [];
        if ($counter === 0) {
            $result[] = implode(',', $startingIds);
        }

        $qb = $this->connectionPool->getQueryBuilderForTable(self::TABLE);
        $qb->getRestrictions()->removeAll();
        $rows = $qb
            ->select('uid')
            ->from(self::TABLE)
            ->where(
                $qb->expr()->eq('deleted', $qb->createNamedParameter(0, \PDO::PARAM_INT)),
                $qb->expr()->in('parent', $qb->createNamedParameter($startingIds, \TYPO3\CMS\Core\Database\Connection::PARAM_INT_ARRAY)),
            )
            ->executeQuery()
            ->fetchAllAssociative();

        foreach ($rows as $row) {
            $counter++;
            if ($counter > self::RECURSION_LIMIT) {
                $this->logRecursionWarning();
                return implode(',', array_filter($result));
            }
            $childUid = (int)$row['uid'];
            $sub = $this->getChildrenRecursive((string)$childUid, $counter);
            $result[] = $childUid . ($sub !== '' ? ',' . $sub : '');
        }

        return implode(',', array_filter($result));
    }

    private function getRootlineRecursiveInternal(int $id, int $counter): string
    {
        if ($id === 0 || $counter > self::RECURSION_LIMIT) {
            if ($counter > self::RECURSION_LIMIT) {
                $this->logRecursionWarning();
            }
            return '';
        }

        $qb = $this->connectionPool->getQueryBuilderForTable(self::TABLE);
        $qb->getRestrictions()->removeAll();
        $row = $qb
            ->select('uid', 'parent')
            ->from(self::TABLE)
            ->where(
                $qb->expr()->eq('uid', $qb->createNamedParameter($id, \PDO::PARAM_INT)),
                $qb->expr()->eq('deleted', $qb->createNamedParameter(0, \PDO::PARAM_INT)),
            )
            ->setMaxResults(1)
            ->executeQuery()
            ->fetchAssociative();

        if (!is_array($row)) {
            return '';
        }
        $parent = (int)$row['parent'];
        if ($parent === 0) {
            return '';
        }
        $up = $this->getRootlineRecursiveInternal($parent, $counter + 1);
        return $up === '' ? (string)$parent : $parent . ',' . $up;
    }

    private function getCache(): FrontendInterface
    {
        return $this->cacheManager->getCache(self::CACHE_NAME);
    }

    private function logRecursionWarning(): void
    {
        $tt = $GLOBALS['TT'] ?? null;
        if ($tt instanceof TimeTracker) {
            $tt->setTSlogMessage('EXT:address: one or more recursive categories were found');
        }
    }

    // ----------------------------------------------------------------------
    // Legacy static facade — kept for BC, delegates to instance methods.
    // ----------------------------------------------------------------------

    /**
     * @deprecated since 14.1, inject CategoryService and call getChildren()
     */
    public static function getChildrenCategories(
        string $idList,
        int $counter = 0,
        string $additionalWhere = '',
        bool $removeGivenIdListFromResult = false,
    ): string {
        if ($additionalWhere !== '' || $counter !== 0) {
            trigger_error(
                'CategoryService::getChildrenCategories(): $counter and $additionalWhere were removed — values are ignored. Use ->getChildren() on a DI-injected instance.',
                E_USER_DEPRECATED,
            );
        }
        return GeneralUtility::makeInstance(self::class)->getChildren($idList, $removeGivenIdListFromResult);
    }

    /**
     * @deprecated since 14.1, inject CategoryService and call removeFromList()
     */
    public static function removeValuesFromString(string $result, string $toBeRemoved): string
    {
        return GeneralUtility::makeInstance(self::class)->removeFromList($result, $toBeRemoved);
    }

    /**
     * @deprecated since 14.1, inject CategoryService and call getRootline()
     */
    public static function getRootlineRecursive(int $id, int $counter = 0, string $additionalWhere = ''): string
    {
        if ($additionalWhere !== '' || $counter !== 0) {
            trigger_error(
                'CategoryService::getRootlineRecursive(): $counter and $additionalWhere were removed — values are ignored. Use ->getRootline() on a DI-injected instance.',
                E_USER_DEPRECATED,
            );
        }
        return GeneralUtility::makeInstance(self::class)->getRootline($id);
    }

    /**
     * @deprecated since 14.1, inject CategoryService and call translateRecord()
     * @param array<string,mixed> $row
     */
    public static function translateCategoryRecord(string $default, array $row = []): string
    {
        return GeneralUtility::makeInstance(self::class)->translateRecord($default, $row);
    }
}
