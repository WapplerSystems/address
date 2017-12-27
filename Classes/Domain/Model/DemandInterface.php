<?php
namespace WapplerSystems\Address\Domain\Model;

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

    public function getSearch();

    public function getOrder();

    public function getOrderByAllowed();

    public function getTopAddressFirst();

}
