<?php
declare(strict_types=1);

namespace WapplerSystems\Address\Domain\Repository;

/**
 * This file is part of the "address" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use WapplerSystems\Address\Domain\Model\DemandInterface;

/**
 * Demand domain model interface
 *
 */
interface DemandedRepositoryInterface
{
    public function findDemanded(DemandInterface $demand, bool $respectEnableFields = true): QueryResultInterface;

    public function countDemanded(DemandInterface $demand): int;
}
