<?php
namespace WapplerSystems\Address\Domain\Model;

/**
 * This file is part of the "address" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

/**
 * File Reference
 */
class FileReference extends \TYPO3\CMS\Extbase\Domain\Model\FileReference
{

    /**
     * @var string
     */
    protected string $title = '';

    /**
     * @var string
     */
    protected string $description = '';

    /**
     * @var string
     */
    protected string $alternative = '';

    /**
     * @var string
     */
    protected string $link = '';

    /**
     * @var bool
     */
    protected bool $showinpreview = false;

    /**
     * Set File uid
     *
     * @param int $fileUid
     */
    public function setFileUid(int $fileUid): void
    {
        $this->uidLocal = $fileUid;
    }

    /**
     * Get File UID
     *
     * @return int
     */
    public function getFileUid(): int
    {
        return $this->uidLocal;
    }

    /**
     * Set alternative
     *
     * @param string $alternative
     */
    public function setAlternative(string $alternative): void
    {
        $this->alternative = $alternative;
    }

    /**
     * Get alternative
     *
     * @return string
     */
    public function getAlternative(): ?string
    {
        return $this->alternative !== '' ? $this->alternative : $this->getOriginalResource()->getAlternative();
    }

    /**
     * Set description
     *
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription(): ?string
    {
        return $this->description !== '' ? $this->description : $this->getOriginalResource()->getDescription();
    }

    /**
     * Set link
     *
     * @param string $link
     */
    public function setLink(string $link): void
    {
        $this->link = $link;
    }

    /**
     * Get link
     *
     */
    public function getLink(): ?string
    {
        return $this->link !== '' ? $this->link : $this->getOriginalResource()->getLink();
    }

    /**
     * Set title
     *
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle(): ?string
    {
        return $this->title !== '' ? $this->title : $this->getOriginalResource()->getTitle();
    }

    /**
     * Set showinpreview
     *
     * @param bool $showinpreview
     */
    public function setShowinpreview(bool $showinpreview): void
    {
        $this->showinpreview = $showinpreview;
    }

    /**
     * Get showinpreview
     *
     * @return bool
     */
    public function getShowinpreview(): bool
    {
        return $this->showinpreview;
    }
}
