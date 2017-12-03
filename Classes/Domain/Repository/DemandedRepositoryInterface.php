<?php

namespace WapplerSystems\Address\Domain\Repository;

/**
 * This file is part of the "address" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */
use WapplerSystems\Address\Domain\Model\DemandInterface;

/**
 * Demand domain model interface
 *
 */
interface DemandedRepositoryInterface
{
    public function findDemanded(DemandInterface $demand, $respectEnableFields = true);

    public function countDemanded(DemandInterface $demand);
}
