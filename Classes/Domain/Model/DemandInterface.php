<?php

namespace WapplerSystems\Address\Domain\Model;

use WapplerSystems\Address\Domain\Model\Dto\Search;

/**
 * This file is part of the "address" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

/**
 * Demanded repository interface
 */
interface DemandInterface
{

    public function getSearch(): ?Search;

    public function getOrder(): string;

    public function getOrderByAllowed(): string;

    public function getTopAddressFirst(): bool;

    public function getIds(): array;

}
