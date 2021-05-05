<?php
namespace WapplerSystems\Address\Domain\Model\Dto;

use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * This file is part of the "address" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

/**
 * Extension Manager configuration
 */
class EmConfiguration
{

    /**
     * Fill the properties properly
     *
     * @param array $configuration em configuration
     */
    public function __construct(array $configuration = [])
    {
        if (empty($configuration)) {
            try {
                $extensionConfiguration = GeneralUtility::makeInstance(ExtensionConfiguration::class);
                $configuration = $extensionConfiguration->get('address');
            } catch (\Exception $exception) {
                // do nothing
            }
        }
        foreach ($configuration as $key => $value) {
            if (property_exists(__CLASS__, $key)) {
                $this->$key = $value;
            }
        }
    }

    /**
     * @var int
     */
    protected $tagPid = 0;

    /**
     * @var boolean;
     */
    protected $prependAtCopy = true;

    /**
     * @var string;
     */
    protected $categoryRestriction = '';

    /**
     * @var bool
     */
    protected $categoryBeGroupTceFormsRestriction = false;

    /**
     * @var bool
     */
    protected $contentElementRelation = true;

    /** @var bool */
    protected $contentElementPreview = true;

    /**
     * @var bool
     */
    protected $manualSorting = false;

    /**
     * @var string
     */
    protected $archiveDate = 'date';

    /**
     * @var bool
     */
    protected $dateTimeNotRequired = false;

    /**
     * @var bool
     */
    protected $showImporter = false;

    /** @var bool */
    protected $rteForTeaser = false;

    /**
     * @var bool
     */
    protected $showAdministrationModule = true;

    /** @var bool */
    protected $hidePageTreeForAdministrationModule = false;

    /**
     * @var int
     */
    protected $storageUidImporter = 1;

    /**
     * @var string
     */
    protected $resourceFolderImporter = '/address_import';

    /** @var string */
    protected $slugBehaviour = 'unique';

    /**
     * @return int
     */
    public function getTagPid(): int
    {
        return (int)$this->tagPid;
    }

    /**
     *
     * @return bool
     */
    public function getPrependAtCopy(): bool
    {
        return (boolean)$this->prependAtCopy;
    }

    /**
     * @return string
     */
    public function getCategoryRestriction(): string
    {
        return $this->categoryRestriction;
    }

    /**
     * Get categoryBeGroupTceFormsRestriction
     *
     * @return bool
     */
    public function getCategoryBeGroupTceFormsRestriction(): bool
    {
        return (bool)$this->categoryBeGroupTceFormsRestriction;
    }

    /**
     * @return bool
     */
    public function getContentElementRelation(): bool
    {
        return (boolean)$this->contentElementRelation;
    }

    /**
     * @return bool
     */
    public function getContentElementPreview(): bool
    {
        return (bool)$this->contentElementPreview;
    }

    /**
     * @return bool
     */
    public function getManualSorting(): bool
    {
        return (boolean)$this->manualSorting;
    }

    /**
     * @return string
     */
    public function getArchiveDate(): string
    {
        return $this->archiveDate;
    }

    /**
     * @return bool
     */
    public function getShowImporter(): bool
    {
        return (boolean)$this->showImporter;
    }

    /**
     * @param bool $showAdministrationModule
     */
    public function setShowAdministrationModule($showAdministrationModule)
    {
        $this->showAdministrationModule = $showAdministrationModule;
    }

    /**
     * @return bool
     */
    public function getShowAdministrationModule(): bool
    {
        return $this->showAdministrationModule;
    }

    /**
     * @return bool
     */
    public function getRteForTeaser(): bool
    {
        return $this->rteForTeaser;
    }

    /**
     * @return string
     */
    public function getResourceFolderImporter(): string
    {
        return $this->resourceFolderImporter;
    }

    /**
     * @return int
     */
    public function getStorageUidImporter(): int
    {
        return $this->storageUidImporter;
    }

    /**
     * @return bool
     */
    public function getDateTimeNotRequired(): bool
    {
        return (bool)$this->dateTimeNotRequired;
    }

    /**
     * @return bool
     */
    public function getDateTimeRequired(): bool
    {
        return !(bool)$this->dateTimeNotRequired;
    }

    /**
     * @return bool
     */
    public function getHidePageTreeForAdministrationModule(): bool
    {
        return (bool)$this->hidePageTreeForAdministrationModule;
    }

    public function getSlugBehaviour(): string
    {
        return $this->slugBehaviour;
    }
}
