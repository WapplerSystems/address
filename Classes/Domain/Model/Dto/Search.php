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
     * Field using for date queries
     *
     * @var string
     */
    protected $dateField;

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


}
