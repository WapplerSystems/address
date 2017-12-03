<?php
namespace WapplerSystems\Address\ViewHelpers\Form;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use TYPO3\CMS\Fluid\ViewHelpers\Form\AbstractFormFieldViewHelper;

/**
 * View Helper which creates a text field (<input type="text">).
 *
 *
 *
 * @api
 */
class ValueViewHelper extends AbstractFormFieldViewHelper
{
    /**
     * @var string
     */
    protected $tagName = 'span';

    /**
     * Initialize the arguments.
     *
     * @throws \TYPO3Fluid\Fluid\Core\ViewHelper\Exception
     * @api
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
    }

    /**
     *
     * @return string
     * @api
     */
    public function render()
    {
        $value = $this->getValueAttribute();

        if ($value !== null) {
            $this->tag->setContent($value);
        }

        return $this->tag->render();
    }
}
