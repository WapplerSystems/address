<?php

declare(strict_types=1);

/*
 * This file is part of the "address" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace WapplerSystems\Address\Event;

use WapplerSystems\Address\Domain\Model\Address;

final class ModifyCacheTagsFromAddressEvent
{
    /**
     * @var array
     */
    private $cacheTags;

    /**
     * @var Address
     */
    private $address;

    public function __construct(array $cacheTags, Address $address)
    {
        $this->cacheTags = $cacheTags;
        $this->address = $address;
    }

    public function getCacheTags(): array
    {
        return $this->cacheTags;
    }

    public function setCacheTags(array $cacheTags): void
    {
        $this->cacheTags = $cacheTags;
    }

    public function getAddress(): Address
    {
        return $this->address;
    }
}
