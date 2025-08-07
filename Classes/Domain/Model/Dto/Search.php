<?php
namespace WapplerSystems\Address\Domain\Model\Dto;

/**
 * This file is part of the "address" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

/**
 * Address Demand object which holds all information to get the correct
 * address records.
 *
 */
class Search extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{

    /**
     * Basic search word
     */
    protected string $subject;

    /**
     * Search fields
     */
    protected string $fields;

    protected int $distance;

    protected string $location;

    protected array $settings = [];


    /**
     * Get the subject
     *
     * @return string
     */
    public function getSubject(): string
    {
        return $this->subject;
    }

    /**
     * Set subject
     *
     * @param string $subject
     */
    public function setSubject($subject): void
    {
        $this->subject = $subject;
    }

    /**
     * Get fields
     */
    public function getFields(): string
    {
        return $this->fields;
    }

    /**
     * Set fields
     */
    public function setFields($fields): void
    {
        $this->fields = $fields;
    }


    /**
     * @return string
     */
    public function getLocation(): string
    {
        return $this->location;
    }

    /**
     * @param string $location
     */
    public function setLocation(string $location): void
    {
        $this->location = $location;
    }

    /**
     * @return int
     */
    public function getDistance(): int
    {
        return $this->distance;
    }

    /**
     * @param int $distance
     */
    public function setDistance(int $distance): void
    {
        $this->distance = $distance;
    }

    /**
     * @return array
     */
    public function getSettings(): array
    {
        return $this->settings;
    }

    /**
     * @param array $settings
     */
    public function setSettings(array $settings): void
    {
        $this->settings = $settings;
    }





}
