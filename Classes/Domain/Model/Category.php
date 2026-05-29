<?php
declare(strict_types=1);

namespace WapplerSystems\Address\Domain\Model;

/**
 * This file is part of the "address" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Extbase\Persistence\ObjectStorage;


/**
 * Category Model
 */
class Category extends \TYPO3\CMS\Extbase\Domain\Model\Category
{

    /**
     * @var int
     */
    protected int $sorting;

    /**
     * @var \DateTime
     */
    protected \DateTime $crdate;

    /**
     * @var \DateTime
     */
    protected \DateTime $tstamp;

    /**
     * @var \DateTime
     */
    protected \DateTime $starttime;

    /**
     * @var bool
     */
    protected bool $hidden;

    /**
     * @var \DateTime
     */
    protected \DateTime $endtime;

    /**
     * @var int
     */
    protected int $sysLanguageUid;

    /**
     * @var int
     */
    protected int $l10nParent;

    /**
     * @var Category
     * @TYPO3\CMS\Extbase\Annotation\ORM\Lazy
     */
    protected $parentcategory;

    /**
     * @var ObjectStorage<FileReference>
     * @TYPO3\CMS\Extbase\Annotation\ORM\Lazy
     */
    protected $images;

    /**
     * @var int
     */
    protected $shortcut;

    /**
     * @var int
     */
    protected $singlePid;

    /**
     * @var string
     */
    protected $importId;

    /**
     * @var string
     */
    protected $importSource;

    /**
     * keep it as string as it should be only used during imports
     * @var string
     */
    protected $feGroup;

    /**
     * @var string
     */
    protected $seoTitle;

    /**
     * @var string
     */
    protected $seoDescription;

    /**
     * @var string
     */
    protected $seoHeadline;

    /**
     * @var string
     */
    protected $seoText;

    /**
     * Initialize images
     *
     * @return Category
     */
    public function __construct()
    {
        $this->images = new ObjectStorage();
    }

    /**
     * Get creation date
     *
     * @return \DateTime
     */
    public function getCrdate()
    {
        return $this->crdate;
    }

    /**
     * Set Creation Date
     *
     * @param \DateTime $crdate crdate
     */
    public function setCrdate($crdate)
    {
        $this->crdate = $crdate;
    }

    /**
     * Get Tstamp
     *
     * @return \DateTime
     */
    public function getTstamp()
    {
        return $this->tstamp;
    }

    /**
     * Set tstamp
     *
     * @param \DateTime $tstamp tstamp
     */
    public function setTstamp(\DateTime $tstamp): void
    {
        $this->tstamp = $tstamp;
    }

    /**
     * Get starttime
     *
     * @return \DateTime
     */
    public function getStarttime(): \DateTime
    {
        return $this->starttime;
    }

    /**
     * Set starttime
     *
     * @param \DateTime $starttime starttime
     */
    public function setStarttime(\DateTime $starttime): void
    {
        $this->starttime = $starttime;
    }

    /**
     * Get Endtime
     *
     * @return \DateTime
     */
    public function getEndtime(): \DateTime
    {
        return $this->endtime;
    }

    /**
     * Set Endtime
     *
     * @param \DateTime $endtime endttime
     */
    public function setEndtime(\DateTime $endtime): void
    {
        $this->endtime = $endtime;
    }

    /**
     * Get Hidden
     *
     * @return bool
     */
    public function getHidden(): bool
    {
        return $this->hidden;
    }

    /**
     * Set Hidden
     *
     * @param bool $hidden
     */
    public function setHidden(bool $hidden): void
    {
        $this->hidden = $hidden;
    }

    /**
     * Get sys language
     *
     * @return int
     */
    public function getSysLanguageUid(): int
    {
        return $this->_languageUid;
    }

    /**
     * Set sys language
     *
     * @param int $sysLanguageUid language uid
     */
    public function setSysLanguageUid($sysLanguageUid): void
    {
        $this->_languageUid = $sysLanguageUid;
    }

    /**
     * Get language parent
     *
     * @return int
     */
    public function getL10nParent(): int
    {
        return $this->l10nParent;
    }

    /**
     * Set language parent
     *
     * @param int $l10nParent l10nParent
     */
    public function setL10nParent(int $l10nParent): void
    {
        $this->l10nParent = $l10nParent;
    }


    /**
     * @param ObjectStorage $images
     */
    public function setImages(ObjectStorage $images): void
    {
        $this->images = $images;
    }

    /**
     * @return ObjectStorage<FileReference>
     */
    public function getImages(): ObjectStorage
    {
        return $this->images;
    }

    /**
     * Add image
     *
     * @param FileReference $image
     */
    public function addImage(FileReference $image): void
    {
        $this->images->attach($image);
    }

    /**
     * Remove image
     *
     * @param FileReference $image
     */
    public function removeImage(FileReference $image): void
    {
        $this->images->detach($image);
    }

    /**
     * Get the first image
     *
     * @return FileReference|null
     */
    public function getFirstImage(): ?FileReference
    {
        $images = $this->getImages();
        foreach ($images as $image) {
            return $image;
        }

        return null;
    }

    /**
     * Get parent category
     *
     * @return Category
     */
    public function getParentcategory(): Category
    {
        return $this->parentcategory;
    }

    /**
     * Set parent category
     *
     * @param Category $category parent category
     */
    public function setParentcategory(Category $category): void
    {
        $this->parentcategory = $category;
    }

    /**
     * Get shortcut
     *
     * @return int
     */
    public function getShortcut(): int
    {
        return $this->shortcut;
    }

    /**
     * Set shortcut
     *
     * @param int $shortcut shortcut
     */
    public function setShortcut(int $shortcut): void
    {
        $this->shortcut = $shortcut;
    }

    /**
     * Get single pid of category
     *
     * @return int
     */
    public function getSinglePid(): int
    {
        return $this->singlePid;
    }

    /**
     * Set single pid
     *
     * @param int $singlePid single pid
     */
    public function setSinglePid(int $singlePid): void
    {
        $this->singlePid = $singlePid;
    }

    /**
     * Get import id
     *
     * @return string
     */
    public function getImportId(): string
    {
        return $this->importId;
    }

    /**
     * Set import id
     *
     * @param string $importId import id
     */
    public function setImportId(string $importId): void
    {
        $this->importId = $importId;
    }

    /**
     * Get sorting id
     *
     * @return int sorting id
     */
    public function getSorting(): int
    {
        return $this->sorting;
    }

    /**
     * Set sorting id
     *
     * @param int $sorting sorting id
     */
    public function setSorting(int $sorting): void
    {
        $this->sorting = $sorting;
    }

    /**
     * Get feGroup
     *
     * @return string
     */
    public function getFeGroup(): string
    {
        return $this->feGroup;
    }

    /**
     * Get feGroup
     *
     * @param string $feGroup feGroup
     */
    public function setFeGroup(string $feGroup): void
    {
        $this->feGroup = $feGroup;
    }

    /**
     * Set importSource
     *
     * @param string $importSource
     */
    public function setImportSource(string $importSource): void
    {
        $this->importSource = $importSource;
    }

    /**
     * Get importSource
     *
     * @return string
     */
    public function getImportSource(): string
    {
        return $this->importSource;
    }

    /**
     * @return string
     */
    public function getSeoTitle(): string
    {
        return $this->seoTitle;
    }

    /**
     * @param string $seoTitle
     */
    public function setSeoTitle($seoTitle): void
    {
        $this->seoTitle = $seoTitle;
    }

    /**
     * @return string
     */
    public function getSeoDescription(): string
    {
        return $this->seoDescription;
    }

    /**
     * @param string $seoDescription
     */
    public function setSeoDescription(string $seoDescription): void
    {
        $this->seoDescription = $seoDescription;
    }

    /**
     * @return string
     */
    public function getSeoHeadline(): string
    {
        return $this->seoHeadline;
    }

    /**
     * @param string $seoHeadline
     */
    public function setSeoHeadline($seoHeadline): void
    {
        $this->seoHeadline = $seoHeadline;
    }

    /**
     * @return string
     */
    public function getSeoText(): string
    {
        return $this->seoText;
    }

    /**
     * @param string $seoText
     */
    public function setSeoText($seoText): void
    {
        $this->seoText = $seoText;
    }
}
