<?php

namespace WapplerSystems\Address\Domain\Model;

/**
 * This file is part of the "address" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Extbase\Domain\Model\FileReference;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;


/**
 * Address model
 */
class Address extends AbstractEntity
{

    public const int TYPE_PERSON = 1;
    public const int TYPE_COMPANY = 2;

    /**
     * @var bool
     */
    protected bool $hidden = false;

    /**
     * @var bool
     */
    protected bool $deleted = false;

    /**
     * @var string
     */
    protected string $bodytext = '';

    /**
     * @var ObjectStorage<Category>
     * @TYPO3\CMS\Extbase\Annotation\ORM\Lazy
     */
    protected ObjectStorage $categories;

    /**
     * @var ObjectStorage<\WapplerSystems\Address\Domain\Model\Address>
     * @TYPO3\CMS\Extbase\Annotation\ORM\Lazy
     */
    protected ObjectStorage $related;

    /**
     * @var ObjectStorage<\WapplerSystems\Address\Domain\Model\Address>
     * @TYPO3\CMS\Extbase\Annotation\ORM\Lazy
     */
    protected ObjectStorage $relatedFrom;

    /**
     * Fal related files
     *
     * @var ObjectStorage<\WapplerSystems\Address\Domain\Model\FileReference>
     * @TYPO3\CMS\Extbase\Annotation\ORM\Lazy
     */
    protected ObjectStorage $relatedFiles;

    /**
     * @var ObjectStorage<\WapplerSystems\Address\Domain\Model\Link>
     * @TYPO3\CMS\Extbase\Annotation\ORM\Lazy
     */
    protected ObjectStorage $relatedLinks;

    /**
     * @var string
     */
    protected string $type = '';

    /**
     * @var string
     */
    protected string $keywords = '';

    /**
     * @var string
     */
    protected string $description = '';

    /**
     * @var string
     */
    protected string $teaser = '';

    /**
     * Fal media items
     *
     * @var ObjectStorage<\WapplerSystems\Address\Domain\Model\FileReference>
     * @TYPO3\CMS\Extbase\Annotation\ORM\Lazy
     */
    protected ObjectStorage $media;

    /**
     * Fal media items with showinpreview set
     *
     * @var array
     * @TYPO3\CMS\Extbase\Annotation\ORM\Transient
     */
    protected array $mediaPreviews = [];

    /**
     * Fal media items with showinpreview not set
     *
     * @var array
     * @TYPO3\CMS\Extbase\Annotation\ORM\Transient
     */
    protected array $mediaNonPreviews = [];


    /**
     * @var ObjectStorage<\WapplerSystems\Address\Domain\Model\TtContent>
     * @TYPO3\CMS\Extbase\Annotation\ORM\Lazy
     */
    protected ObjectStorage $contentElements;

    /**
     * @var string
     */
    protected string $pathSegment = '';

    /**
     * @var int
     */
    protected int $editlock;

    /**
     * @var int
     */
    protected int $importId;

    /**
     * @var string
     */
    protected string $importSource;

    /**
     * @var int
     */
    protected int $sorting;

    /**
     * @var bool
     */
    protected bool $isTopAddress = false;

    /**
     * @var \DateTime
     */
    protected \DateTime $archive;

    /**
     * @var bool
     */
    protected bool $directContact = false;

    /**
     * @var string
     */
    protected string $email = '';

    /**
     * @var string
     */
    protected string $url;

    /**
     * @var string
     */
    protected string $firstName = '';

    /**
     * @var string
     */
    protected string $lastName = '';

    /**
     * @var string
     */
    protected string $middleName = '';

    /**
     * @var string
     */
    protected string $abbreviation = '';

    /**
     * @var string
     */
    protected string $academicTitle = '';

    /**
     * @var bool
     */
    protected bool $appendAcademicTitle = false;

    /**
     * @var string
     */
    protected string $title = '';

    /**
     * @var string
     */
    protected string $address;

    /**
     * @var string
     */
    protected string $building;

    /**
     * @var string
     */
    protected string $city;

    /**
     * @var string
     */
    protected string $zip;

    /**
     * @var string
     */
    protected string $country;

    /**
     * @var string
     */
    protected string $position;

    /**
     * @var string
     */
    protected string $phone;

    /**
     * @var string
     */
    protected string $fax;

    /**
     * @var int
     */
    protected int $detailPid;

    /**
     * no default value
     */
    protected ?float $longitude = null;

    /**
     * no default value
     */
    protected ?float $latitude = null;

    /**
     * @var ObjectStorage<Contact>
     * @TYPO3\CMS\Extbase\Annotation\ORM\Lazy
     */
    protected ObjectStorage $contacts;

    protected ?FileReference $markerIcon;

    protected string $markerColor;

    protected ?\DateTime $starttime = null;


    /**
     *
     * @var ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\Tag>
     * @TYPO3\CMS\Extbase\Annotation\ORM\Lazy
     */
    protected ObjectStorage $tags;


    /**
     * Initialize categories and media relation
     */
    public function __construct()
    {
        $this->categories = new ObjectStorage();
        $this->contentElements = new ObjectStorage();
        $this->relatedLinks = new ObjectStorage();
        $this->media = new ObjectStorage();
        $this->relatedFiles = new ObjectStorage();
        $this->contacts = new ObjectStorage();
        $this->tags = new ObjectStorage();
    }


    /**
     *
     * @return string
     */
    public function getBodytext(): string
    {
        return $this->bodytext;
    }

    /**
     *
     * @param string $bodytext main content
     */
    public function setBodytext(string $bodytext): void
    {
        $this->bodytext = $bodytext;
    }


    /**
     * Get categories
     *
     * @return ObjectStorage<Category>
     */
    public function getCategories(): ObjectStorage
    {
        return $this->categories;
    }

    /**
     * Get first category
     *
     * @return Category
     */
    public function getFirstCategory(): ?Category
    {
        $categories = $this->getCategories();
        $categories->rewind();
        return $categories->current();
    }

    /**
     * Set categories
     *
     * @param ObjectStorage $categories
     */
    public function setCategories(ObjectStorage $categories): void
    {
        $this->categories = $categories;
    }

    /**
     * Adds a category to categories.
     *
     * @param Category $category
     */
    public function addCategory(Category $category): void
    {
        $this->getCategories()->attach($category);
    }

    /**
     * Get related address
     *
     * @return ObjectStorage<\WapplerSystems\Address\Domain\Model\Address>
     */
    public function getRelated(): ObjectStorage
    {
        return $this->related;
    }

    /**
     * Set related from
     *
     * @param ObjectStorage<\WapplerSystems\Address\Domain\Model\Address> $relatedFrom
     */
    public function setRelatedFrom(ObjectStorage $relatedFrom): void
    {
        $this->relatedFrom = $relatedFrom;
    }

    /**
     * Get related from
     *
     * @return ObjectStorage<\WapplerSystems\Address\Domain\Model\Address>
     */
    public function getRelatedFrom(): ObjectStorage
    {
        return $this->relatedFrom;
    }


    /**
     * Set related addresses
     *
     * @param ObjectStorage $related related addresses
     */
    public function setRelated(ObjectStorage $related): void
    {
        $this->related = $related;
    }

    /**
     * Get related links
     *
     * @return ObjectStorage<\WapplerSystems\Address\Domain\Model\Link>
     */
    public function getRelatedLinks(): ObjectStorage
    {
        return $this->relatedLinks;
    }

    /**
     * Get FAL related files
     *
     * @return ObjectStorage<\WapplerSystems\Address\Domain\Model\FileReference>
     */
    public function getRelatedFiles(): ObjectStorage
    {
        return $this->relatedFiles;
    }

    /**
     * Set FAL related files
     *
     * @param ObjectStorage $relatedFiles FAL related files
     */
    public function setRelatedFiles(ObjectStorage $relatedFiles): void
    {
        $this->relatedFiles = $relatedFiles;
    }

    /**
     * Adds a file to this files.
     *
     * @param FileReference $file
     */
    public function addRelatedFile(FileReference $file): void
    {
        if ($this->getRelatedFiles() === null) {
            $this->relatedFiles = new ObjectStorage();
        }
        $this->getRelatedFiles()->attach($file);
    }

    /**
     * Set related links
     *
     * @param ObjectStorage<\WapplerSystems\Address\Domain\Model\Link> $relatedLinks related links relation
     */
    public function setRelatedLinks(ObjectStorage $relatedLinks): void
    {
        $this->relatedLinks = $relatedLinks;
    }

    /**
     * Get type of address
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Set type of address
     *
     * @param int $type type
     */
    public function setType(int $type): void
    {
        $this->type = $type;
    }

    /**
     * Get keywords
     *
     * @return string
     */
    public function getKeywords(): string
    {
        return $this->keywords;
    }

    /**
     * Set keywords
     *
     * @param string $keywords keywords
     */
    public function setKeywords(string $keywords): void
    {
        $this->keywords = $keywords;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Set description
     *
     * @param string $description description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * Adds a related link.
     *
     * @param Link $relatedLink
     */
    public function addRelatedLink(Link $relatedLink): void
    {
        $this->relatedLinks->attach($relatedLink);
    }


    /**
     * Set Fal media relation
     *
     * @param ObjectStorage $media
     */
    public function setMedia(ObjectStorage $media): void
    {
        $this->media = $media;
    }

    /**
     * Add a Fal media file reference
     *
     * @param FileReference $media
     */
    public function addMedia(FileReference $media): void
    {
        if ($this->getMedia() === null) {
            $this->media = new ObjectStorage();
        }
        $this->media->attach($media);
    }

    /**
     * Get the Fal media items
     *
     * @return array
     * @throws \InvalidArgumentException
     */
    public function getMediaPreviews(): array
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
     * @throws \InvalidArgumentException
     */
    public function getMediaNonPreviews(): array
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
     * @return FileReference|null
     */
    public function getFirstFalImagePreview(): ?FileReference
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
     * @return FileReference
     */
    public function getFirstPreview(): ?FileReference
    {
        return $this->getFirstFalImagePreview();
    }


    /**
     * Get top address flag
     *
     * @return bool
     */
    public function getIsTopAddress(): bool
    {
        return $this->isTopAddress;
    }

    /**
     * Set top address flag
     *
     * @param bool $isTopAddress top address flag
     */
    public function setIsTopAddress(bool $isTopAddress): void
    {
        $this->isTopAddress = $isTopAddress;
    }

    /**
     * Get path segment
     *
     * @return string
     */
    public function getPathSegment(): string
    {
        return $this->pathSegment;
    }

    /**
     * Set path segment
     *
     * @param string $pathSegment
     */
    public function setPathSegment(string $pathSegment): void
    {
        $this->pathSegment = $pathSegment;
    }

    /**
     * Get hidden flag
     */
    public function getHidden(): bool
    {
        return $this->hidden;
    }

    /**
     * Set hidden flag
     *
     * @param int $hidden hidden flag
     */
    public function setHidden(int $hidden): void
    {
        $this->hidden = $hidden;
    }

    /**
     * Get deleted flag
     *
     * @return bool
     */
    public function getDeleted(): bool
    {
        return $this->deleted;
    }

    /**
     * Set deleted flag
     *
     * @param bool $deleted deleted flag
     */
    public function setDeleted(bool $deleted): void
    {
        $this->deleted = $deleted;
    }

    /**
     * Get start time
     *
     * @return \DateTime|null
     */
    public function getStarttime(): ?\DateTime
    {
        return $this->starttime;
    }

    public function setStarttime(\DateTime $starttime): void
    {
        $this->starttime = $starttime;
    }


    /**
     * Get import id
     *
     * @return int
     */
    public function getImportId(): int
    {
        return $this->importId;
    }

    /**
     * Set import id
     *
     * @param int $importId import id
     */
    public function setImportId(int $importId): void
    {
        $this->importId = $importId;
    }

    /**
     * Get sorting
     *
     * @return int
     */
    public function getSorting(): int
    {
        return $this->sorting;
    }

    /**
     * Set sorting
     *
     * @param int $sorting sorting
     */
    public function setSorting(int $sorting): void
    {
        $this->sorting = $sorting;
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
    public function getName(): string
    {
        return $this->firstName . ' ' . $this->lastName;
    }


    public function getArchive(): \DateTime
    {
        return $this->archive;
    }

    /**
     * @param \DateTime $archive
     */
    public function setArchive($archive): void
    {
        $this->archive = $archive;
    }

    /**
     * @return bool
     */
    public function isDirectContact(): bool
    {
        return $this->directContact;
    }

    /**
     * @param bool $directContact
     */
    public function setDirectContact($directContact): void
    {
        $this->directContact = $directContact;
    }


    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

    /**
     * @return ObjectStorage
     */
    public function getContentElements(): ObjectStorage
    {
        return $this->contentElements;
    }

    /**
     * @param ObjectStorage $contentElements
     */
    public function setContentElements(ObjectStorage $contentElements): void
    {
        $this->contentElements = $contentElements;
    }


    /**
     * Get id list of content elements
     *
     * @return string
     */
    public function getContentElementIdList()
    {
        $idList = [];
        $contentElements = $this->getContentElements();
        if ($contentElements) {
            foreach ($this->getContentElements() as $contentElement) {
                $idList[] = $contentElement->getUid();
            }
        }
        return implode(',', $idList);
    }

    /**
     * @return ObjectStorage
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
    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     */
    public function setLastName(string $lastName): void
    {
        $this->lastName = $lastName;
    }

    /**
     * @return string
     */
    public function getMiddleName(): string
    {
        return $this->middleName;
    }

    /**
     * @param string $middleName
     */
    public function setMiddleName(string $middleName): void
    {
        $this->middleName = $middleName;
    }

    public function getAcademicTitle(): string
    {
        return $this->academicTitle;
    }

    public function setAcademicTitle(string $academicTitle): void
    {
        $this->academicTitle = $academicTitle;
    }

    public function getAbbreviation(): string
    {
        return $this->abbreviation;
    }

    public function setAbbreviation(string $abbreviation): void
    {
        $this->abbreviation = $abbreviation;
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
     * @return bool
     */
    public function getAppendAcademicTitle(): bool
    {
        return $this->appendAcademicTitle;
    }

    /**
     * @param bool $appendAcademicTitle
     */
    public function setAppendAcademicTitle(bool $appendAcademicTitle)
    {
        $this->appendAcademicTitle = $appendAcademicTitle;
    }

    /**
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param string $city
     */
    public function setCity(string $city)
    {
        $this->city = $city;
    }

    public function getZip(): string
    {
        return $this->zip;
    }

    public function setZip(string $zip): void
    {
        $this->zip = $zip;
    }

    public function getCountry(): string
    {
        return $this->country;
    }

    public function setCountry(string $country): void
    {
        $this->country = $country;
    }


    public function getIsPerson(): bool
    {
        return (int)$this->type === self::TYPE_PERSON;
    }

    public function getIsCompany(): bool
    {
        return (int)$this->type === self::TYPE_COMPANY;
    }

    public function getHasRelatedCompany(): bool
    {
        $items = $this->getRelated();
        /** @var Address $item */
        foreach ($items as $item) {
            if ($item->getIsCompany()) return true;
        }
        return false;
    }

    public function getRelatedCompany(): ?Address
    {
        $items = $this->getRelated();
        /** @var Address $item */
        foreach ($items as $item) {
            if ($item->getIsCompany()) {
                return $item;
            }
        }
        return null;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(?float $longitude): void
    {
        $this->longitude = $longitude;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(?float $latitude): void
    {
        $this->latitude = $latitude;
    }

    public function getContacts(): ObjectStorage
    {
        return $this->contacts;
    }

    public function setContacts(ObjectStorage $contacts): void
    {
        $this->contacts = $contacts;
    }

    /**
     * get one contact by type, ordered by sorting
     */
    public function getContactByType(string $type): ?Contact
    {
        /** @var Contact $contact */
        foreach ($this->contacts as $contact) {
            if ($contact->getType() === $type) {
                return $contact;
            }
        }
        return null;
    }

    public function getContactsByType(string $type): array
    {
        $contacts = [];
        /** @var Contact $contact */
        foreach ($this->contacts as $contact) {
            if ($contact->getType() === $type) {
                $contacts[] = $contact;
            }
        }
        return $contacts;
    }

    public function getFirstEmailAddress(): string
    {
        $contacts = $this->getContactsByType('email');
        if (count($contacts) > 0) {
            return $contacts[0]->getContent();
        }
        return '';
    }

    public function getMarkerIcon(): ?FileReference
    {
        return $this->markerIcon;
    }

    public function setMarkerIcon(?FileReference $markerIcon): void
    {
        $this->markerIcon = $markerIcon;
    }

    public function getMarkerColor(): string
    {
        return $this->markerColor;
    }

    public function setMarkerColor(string $markerColor): void
    {
        $this->markerColor = $markerColor;
    }

    public function getTags(): ObjectStorage
    {
        return $this->tags;
    }

    public function setTags(ObjectStorage $tags): void
    {
        $this->tags = $tags;
    }


}
