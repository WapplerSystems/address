<?php
namespace WapplerSystems\Address\Domain\Model;

use TYPO3\CMS\Extbase\Domain\Model\FileReference;
use TYPO3\CMS\Extbase\DomainObject\AbstractValueObject;

/**
 * This file is part of the "address" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

/**
 * Link model
 */
class MarkerIcon extends AbstractValueObject
{

    protected ?FileReference $icon;

    protected string $size;

    protected string $anchor;

    public function getIcon(): ?FileReference
    {
        return $this->icon;
    }

    public function setIcon(?FileReference $icon): void
    {
        $this->icon = $icon;
    }

    public function getSize(): string
    {
        return $this->size;
    }

    public function setSize(string $size): void
    {
        $this->size = $size;
    }

    public function getAnchor(): string
    {
        return $this->anchor;
    }

    public function setAnchor(string $anchor): void
    {
        $this->anchor = $anchor;
    }


}
