<?php

namespace WapplerSystems\Address\ViewHelpers;

/**
 * This file is part of the "address" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Domain\Model\Category;
use WapplerSystems\Address\Domain\Model\Address;

/**
 *
 *
 *
 *
 */
class CategoryNamesViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper
{

    /**
     * Initialize arguments.
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('address', Address::class, 'address item', true);
        $this->registerArgument('prefix', 'string', 'class prefix', false);
    }

    /**
     * @return string
     */
    public function render()
    {
        $output = '';
        /** @var Address $address */
        $address = $this->arguments['address'];

        $prefix = isset($this->arguments['prefix']) ? $this->arguments['prefix'] : '';


        $categories = $address->getCategories();
        /** @var Category $category */
        foreach ($categories as $category) {
            $output .= ' '.$prefix.GeneralUtility::underscoredToLowerCamelCase($category->getTitle());
        }
        return $output;
    }

}
