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
     *
     * @var string
     */
    protected $subject;

    /**
     * Search fields
     *
     * @var string
     */
    protected $fields;

    /**
     * @var int
     */
    protected $distance;

    /**
     * @var string
     */
    protected $location;


    /**
     * @var array
     */
    protected $settings = [];


    /**
     * Get the subject
     *
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Set subject
     *
     * @param string $subject
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    /**
     * Get fields
     *
     * @return string
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * Set fields
     *
     * @param $fields
     */
    public function setFields($fields)
    {
        $this->fields = $fields;
    }


    /**
     * @return string
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @param string $location
     */
    public function setLocation(string $location)
    {
        $this->location = $location;
    }

    /**
     * @return int
     */
    public function getDistance()
    {
        return $this->distance;
    }

    /**
     * @param int $distance
     */
    public function setDistance(int $distance)
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
    public function setSettings(array $settings)
    {
        $this->settings = $settings;
    }





}
