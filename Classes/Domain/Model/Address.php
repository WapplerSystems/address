<?php

namespace WapplerSystems\Address\Domain\Model;

/**
 * This file is part of the "address" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

/**
 * Address model
 */
class Address extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{

    const TYPE_PERSON = 1;
    const TYPE_COMPANY = 2;

    /**
     * @var bool
     */
    protected $hidden;

    /**
     * @var bool
     */
    protected $deleted;

    /**
     * @var string
     */
    protected $bodytext;

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\WapplerSystems\Address\Domain\Model\Category>
     * @lazy
     */
    protected $categories;

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\WapplerSystems\Address\Domain\Model\Address>
     * @lazy
     */
    protected $related;

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\WapplerSystems\Address\Domain\Model\Address>
     * @lazy
     */
    protected $relatedFrom;

    /**
     * Fal related files
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\WapplerSystems\Address\Domain\Model\FileReference>
     * @lazy
     */
    protected $relatedFiles;

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\WapplerSystems\Address\Domain\Model\Link>
     * @lazy
     */
    protected $relatedLinks;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $keywords;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var string
     */
    protected $teaser;

    /**
     * Fal media items
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\WapplerSystems\Address\Domain\Model\FileReference>
     * @lazy
     */
    protected $media;

    /**
     * Fal media items with showinpreview set
     *
     * @var array
     * @transient
     */
    protected $mediaPreviews;

    /**
     * Fal media items with showinpreview not set
     *
     * @var array
     * @transient
     */
    protected $mediaNonPreviews;


    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\WapplerSystems\Address\Domain\Model\TtContent>
     * @lazy
     */
    protected $contentElements;

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\WapplerSystems\Address\Domain\Model\Tag>
     * @lazy
     */
    protected $tags;

    /**
     * @var string
     */
    protected $pathSegment;

    /**
     * @var int
     */
    protected $editlock;

    /**
     * @var string
     */
    protected $importId;

    /**
     * @var string
     */
    protected $importSource;

    /**
     * @var int
     */
    protected $sorting;

    /**
     * @var int
     */
    protected $isTopAddress;

    /**
     * @var \DateTime
     */
    protected $archive;

    /**
     * @var bool
     */
    protected $directContact = false;

    /**
     * @var string
     */
    protected $email = '';


    /**
     * @var string
     */
    protected $url;


    /**
     * @var string
     */
    protected $firstName;


    /**
     * @var string
     */
    protected $lastName;


    /**
     * @var string
     */
    protected $middleName;


    /**
     * @var string
     */
    protected $academicTitle;


    /**
     * @var int
     */
    protected $appendAcademicTitle;


    /**
     * @var string
     */
    protected $title;


    /**
     * @var string
     */
    protected $address;


    /**
     * @var string
     */
    protected $building;


    /**
     * @var string
     */
    protected $position;


    /**
     * @var string
     */
    protected $phone;

    /**
     * @var string
     */
    protected $fax;


    /**
     * @var string
     */
    protected $externalurl;


    /**
     * @var int
     */
    protected $detailPid;



    /**
     * Initialize categories and media relation
     *
     * @return \WapplerSystems\Address\Domain\Model\Address
     */
    public function __construct()
    {
        $this->categories = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        $this->contentElements = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        $this->relatedLinks = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        $this->media = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        $this->relatedFiles = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
    }


    /**
     * Get bodytext
     *
     * @return string
     */
    public function getBodytext()
    {
        return $this->bodytext;
    }

    /**
     * Set bodytext
     *
     * @param string $bodytext main content
     */
    public function setBodytext($bodytext)
    {
        $this->bodytext = $bodytext;
    }


    /**
     * Get categories
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\WapplerSystems\Address\Domain\Model\Category>
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * Get first category
     *
     * @return Category
     */
    public function getFirstCategory()
    {
        $categories = $this->getCategories();
        if ($categories !== null) {
            $categories->rewind();
            return $categories->current();
        }
        return null;
    }

    /**
     * Set categories
     *
     * @param  \TYPO3\CMS\Extbase\Persistence\ObjectStorage $categories
     */
    public function setCategories($categories)
    {
        $this->categories = $categories;
    }

    /**
     * Adds a category to this categories.
     *
     * @param Category $category
     */
    public function addCategory(Category $category)
    {
        $this->getCategories()->attach($category);
    }

    /**
     * Get related address
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\WapplerSystems\Address\Domain\Model\Address>
     */
    public function getRelated()
    {
        return $this->related;
    }

    /**
     * Set related from
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\WapplerSystems\Address\Domain\Model\Address> $relatedFrom
     */
    public function setRelatedFrom($relatedFrom)
    {
        $this->relatedFrom = $relatedFrom;
    }

    /**
     * Get related from
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\WapplerSystems\Address\Domain\Model\Address>
     */
    public function getRelatedFrom()
    {
        return $this->relatedFrom;
    }

    /**
     * Return related from items sorted by datetime
     *
     * @return array
     */
    public function getRelatedFromSorted()
    {
        $items = $this->getRelatedFrom();
        if ($items) {
            $items = $items->toArray();
            usort($items, create_function('$a, $b', 'return $a->getDatetime() < $b->getDatetime();'));
        }
        return $items;
    }

    /**
     * Return related from items sorted by datetime
     *
     * @return array
     */
    public function getAllRelatedSorted()
    {
        $all = [];
        $itemsRelated = $this->getRelated();
        if ($itemsRelated) {
            $all = array_merge($all, $itemsRelated->toArray());
        }

        $itemsRelatedFrom = $this->getRelatedFrom();
        if ($itemsRelatedFrom) {
            $all = array_merge($all, $itemsRelatedFrom->toArray());
        }
        $all = array_unique($all);

        if (count($all) > 0) {
            usort($all, create_function('$a, $b', 'return $a->getDatetime() < $b->getDatetime();'));
        }
        return $all;
    }

    /**
     * Return related items sorted by datetime
     *
     * @return array
     */
    public function getRelatedSorted()
    {
        $items = $this->getRelated();
        if ($items) {
            $items = $items->toArray();
            usort($items, create_function('$a, $b', 'return $a->getDatetime() < $b->getDatetime();'));
        }
        return $items;
    }

    /**
     * Set related addresses
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage $related related addresses
     */
    public function setRelated($related)
    {
        $this->related = $related;
    }

    /**
     * Get related links
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\WapplerSystems\Address\Domain\Model\Link>
     */
    public function getRelatedLinks()
    {
        return $this->relatedLinks;
    }

    /**
     * Get FAL related files
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\WapplerSystems\Address\Domain\Model\FileReference>
     */
    public function getRelatedFiles()
    {
        return $this->relatedFiles;
    }

    /**
     * Set FAL related files
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage $relatedFiles FAL related files
     */
    public function setRelatedFiles($relatedFiles)
    {
        $this->relatedFiles = $relatedFiles;
    }

    /**
     * Adds a file to this files.
     *
     * @param FileReference $file
     */
    public function addRelatedFile(FileReference $file)
    {
        if ($this->getRelatedFiles() === null) {
            $this->relatedFiles = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        }
        $this->getRelatedFiles()->attach($file);
    }

    /**
     * Set related links
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\WapplerSystems\Address\Domain\Model\Link> $relatedLinks related links relation
     */
    public function setRelatedLinks($relatedLinks)
    {
        $this->relatedLinks = $relatedLinks;
    }

    /**
     * Get type of address
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set type of address
     *
     * @param int $type type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * Get keywords
     *
     * @return string
     */
    public function getKeywords()
    {
        return $this->keywords;
    }

    /**
     * Set keywords
     *
     * @param string $keywords keywords
     */
    public function setKeywords($keywords)
    {
        $this->keywords = $keywords;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set description
     *
     * @param string $description description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Adds a related link.
     *
     * @param Link $relatedLink
     */
    public function addRelatedLink(Link $relatedLink)
    {
        if ($this->relatedLinks === null) {
            $this->relatedLinks = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        }
        $this->relatedLinks->attach($relatedLink);
    }


    /**
     * Set Fal media relation
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage $media
     */
    public function setMedia(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $media)
    {
        $this->media = $media;
    }

    /**
     * Add a Fal media file reference
     *
     * @param FileReference $media
     */
    public function addMedia(FileReference $media)
    {
        if ($this->getMedia() === null) {
            $this->media = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        }
        $this->media->attach($media);
    }

    /**
     * Get the Fal media items
     *
     * @return array
     */
    public function getMediaPreviews()
    {
        if ($this->mediaPreviews === null && $this->getMedia()) {
            $this->mediaPreviews = [];
            /** @var $mediaItem FileReference */
            foreach ($this->getMedia() as $mediaItem) {
                if ($mediaItem->getOriginalResource()->getProperty('showinpreview')) {
                    $this->mediaPreviews[] = $mediaItem;
                }
            }
        }
        return $this->mediaPreviews;
    }

    /**
     * Get all media elements which are not tagged as preview
     *
     * @return array
     */
    public function getMediaNonPreviews()
    {
        if ($this->mediaNonPreviews === null && $this->getMedia()) {
            $this->mediaNonPreviews = [];
            /** @var $mediaItem FileReference */
            foreach ($this->getMedia() as $mediaItem) {
                if (!$mediaItem->getOriginalResource()->getProperty('showinpreview')) {
                    $this->mediaNonPreviews[] = $mediaItem;
                }
            }
        }
        return $this->mediaNonPreviews;
    }


    /**
     * Get first media element which is tagged as preview and is of type image
     *
     * @return \TYPO3\CMS\Extbase\Domain\Model\FileReference
     */
    public function getFirstFalImagePreview()
    {
        $mediaElements = $this->getMediaPreviews();
        if (is_array($mediaElements)) {
            foreach ($mediaElements as $mediaElement) {
                return $mediaElement;
            }
        }
        return null;
    }

    /**
     * Short method for getFirstFalImagePreview
     *
     * @return \TYPO3\CMS\Extbase\Domain\Model\FileReference
     */
    public function getFirstPreview()
    {
        return $this->getFirstFalImagePreview();
    }


    /**
     * Get top address flag
     *
     * @return bool
     */
    public function getIsTopAddress()
    {
        return $this->isTopAddress;
    }

    /**
     * Set top address flag
     *
     * @param bool $isTopAddress top address flag
     */
    public function setIsTopAddress($isTopAddress)
    {
        $this->isTopAddress = $isTopAddress;
    }

    /**
     * Get Tags
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * Set Tags
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage $tags tags
     */
    public function setTags($tags)
    {
        $this->tags = $tags;
    }

    /**
     * Get path segment
     *
     * @return string
     */
    public function getPathSegment()
    {
        return $this->pathSegment;
    }

    /**
     * Set path segment
     *
     * @param string $pathSegment
     */
    public function setPathSegment($pathSegment)
    {
        $this->pathSegment = $pathSegment;
    }

    /**
     * Get hidden flag
     *
     * @return int
     */
    public function getHidden()
    {
        return $this->hidden;
    }

    /**
     * Set hidden flag
     *
     * @param int $hidden hidden flag
     */
    public function setHidden($hidden)
    {
        $this->hidden = $hidden;
    }

    /**
     * Get deleted flag
     *
     * @return int
     */
    public function getDeleted()
    {
        return $this->deleted;
    }

    /**
     * Set deleted flag
     *
     * @param int $deleted deleted flag
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;
    }

    /**
     * Get start time
     *
     * @return \DateTime
     */
    public function getStarttime()
    {
        return $this->starttime;
    }

    /**
     * Set start time
     *
     * @param int $starttime start time
     */
    public function setStarttime($starttime)
    {
        $this->starttime = $starttime;
    }


    /**
     * Get import id
     *
     * @return int
     */
    public function getImportId()
    {
        return $this->importId;
    }

    /**
     * Set import id
     *
     * @param int $importId import id
     */
    public function setImportId($importId)
    {
        $this->importId = $importId;
    }

    /**
     * Get sorting
     *
     * @return int
     */
    public function getSorting()
    {
        return $this->sorting;
    }

    /**
     * Set sorting
     *
     * @param int $sorting sorting
     */
    public function setSorting($sorting)
    {
        $this->sorting = $sorting;
    }

    /**
     * Set importSource
     *
     * @param  string $importSource
     */
    public function setImportSource($importSource)
    {
        $this->importSource = $importSource;
    }

    /**
     * Get importSource
     *
     * @return string
     */
    public function getImportSource()
    {
        return $this->importSource;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->firstName . ' '.$this->lastName;
    }


    /**
     * @return \DateTime
     */
    public function getArchive()
    {
        return $this->archive;
    }

    /**
     * @param \DateTime $archive
     */
    public function setArchive($archive)
    {
        $this->archive = $archive;
    }

    /**
     * @return bool
     */
    public function isDirectContact()
    {
        return $this->directContact;
    }

    /**
     * @param bool $directContact
     */
    public function setDirectContact($directContact)
    {
        $this->directContact = $directContact;
    }


    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage
     */
    public function getContentElements()
    {
        return $this->contentElements;
    }

    /**
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage $contentElements
     */
    public function setContentElements($contentElements)
    {
        $this->contentElements = $contentElements;
    }

    /**
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage
     */
    public function getMedia()
    {
        return $this->media;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }

    /**
     * @return string
     */
    public function getMiddleName()
    {
        return $this->middleName;
    }

    /**
     * @param string $middleName
     */
    public function setMiddleName($middleName)
    {
        $this->middleName = $middleName;
    }

    /**
     * @return string
     */
    public function getAcademicTitle()
    {
        return $this->academicTitle;
    }

    /**
     * @param string $academicTitle
     */
    public function setAcademicTitle($academicTitle)
    {
        $this->academicTitle = $academicTitle;
    }

    /**
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param string $address
     */
    public function setAddress($address)
    {
        $this->address = $address;
    }

    /**
     * @return string
     */
    public function getBuilding()
    {
        return $this->building;
    }

    /**
     * @param string $building
     */
    public function setBuilding($building)
    {
        $this->building = $building;
    }

    /**
     * @return string
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param string $position
     */
    public function setPosition($position)
    {
        $this->position = $position;
    }

    /**
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param string $phone
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    /**
     * @return string
     */
    public function getFax()
    {
        return $this->fax;
    }

    /**
     * @param string $fax
     */
    public function setFax($fax)
    {
        $this->fax = $fax;
    }

    /**
     * @return mixed
     */
    public function getTeaser()
    {
        return $this->teaser;
    }

    /**
     * @param mixed $teaser
     */
    public function setTeaser($teaser)
    {
        $this->teaser = $teaser;
    }

    /**
     * @return string
     */
    public function getExternalurl()
    {
        return $this->externalurl;
    }

    /**
     * @param string $externalurl
     */
    public function setExternalurl($externalurl)
    {
        $this->externalurl = $externalurl;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return int
     */
    public function getDetailPid()
    {
        return $this->detailPid;
    }

    /**
     * @param int $detailPid
     */
    public function setDetailPid(int $detailPid)
    {
        $this->detailPid = $detailPid;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title)
    {
        $this->title = $title;
    }

    /**
     * @return int
     */
    public function getAppendAcademicTitle(): int
    {
        return $this->appendAcademicTitle;
    }

    /**
     * @param int $appendAcademicTitle
     */
    public function setAppendAcademicTitle(int $appendAcademicTitle)
    {
        $this->appendAcademicTitle = $appendAcademicTitle;
    }


}
