<?php

namespace WapplerSystems\Address\ViewHelpers\Widget;

use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;

/**
 * This file is part of the "address" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

/**
 * This ViewHelper renders a Pagination of objects.
 *
 * = Examples =
 *
 * <code title="required arguments">
 * <f:widget.paginate objects="{blogs}" as="paginatedBlogs">
 *   // use {paginatedBlogs} as you used {blogs} before, most certainly inside
 *   // a <f:for> loop.
 * </f:widget.paginate>
 * </code>
 *
 */
class PaginateViewHelper extends \TYPO3\CMS\Fluid\Core\Widget\AbstractWidgetViewHelper
{

    /**
     * @var \WapplerSystems\Address\ViewHelpers\Widget\Controller\PaginateController
     */
    protected $controller;

    /**
     * Inject controller
     *
     * @param \WapplerSystems\Address\ViewHelpers\Widget\Controller\PaginateController $controller
     */
    public function injectController(\WapplerSystems\Address\ViewHelpers\Widget\Controller\PaginateController $controller)
    {
        $this->controller = $controller;
    }

    /**
     * Initialize arguments
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('objects', QueryResultInterface::class, 'Objects to auto-complete', true);
        $this->registerArgument('as', 'string', 'Property to fill', true);
        $this->registerArgument('configuration', 'array', 'Configuration', false, ['itemsPerPage' => 10, 'insertAbove' => false, 'insertBelow' => true]);
        $this->registerArgument('initial', 'array', 'Initial configuration', false, []);
    }

    /**
     * Render everything
     *
     * @internal param array $initial
     * @return string
     */
    public function render()
    {
        return $this->initiateSubRequest();
    }
}
