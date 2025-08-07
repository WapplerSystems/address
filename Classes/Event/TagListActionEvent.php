<?php

/*
 * This file is part of the "address" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace WapplerSystems\Address\Event;

use TYPO3\CMS\Extbase\Mvc\RequestInterface;
use WapplerSystems\Address\Controller\TagController;
use TYPO3\CMS\Extbase\Mvc\Request;

final class TagListActionEvent
{
    private TagController $tagController;

    private array $assignedValues;

    private RequestInterface $request;

    public function __construct(TagController $tagController, array $assignedValues, RequestInterface $request)
    {
        $this->tagController = $tagController;
        $this->assignedValues = $assignedValues;
        $this->request = $request;
    }

    /**
     * Get the tag controller
     */
    public function getTagController(): TagController
    {
        return $this->tagController;
    }

    /**
     * Set the tag controller
     */
    public function setTagController(TagController $tagController): self
    {
        $this->tagController = $tagController;

        return $this;
    }

    /**
     * Get the assignedValues
     */
    public function getAssignedValues(): array
    {
        return $this->assignedValues;
    }

    /**
     * Set the assignedValues
     */
    public function setAssignedValues(array $assignedValues): self
    {
        $this->assignedValues = $assignedValues;

        return $this;
    }

    public function getRequest(): RequestInterface
    {
        return $this->request;
    }
}
