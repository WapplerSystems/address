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

    protected int $tagPid = 0;

    protected bool $prependAtCopy = true;

    protected string $categoryRestriction = '';

    protected bool $categoryBeGroupTceFormsRestriction = false;

    protected bool $contentElementRelation = true;

    protected bool $contentElementPreview = true;

    protected bool $manualSorting = false;

    protected string $archiveDate = 'date';

    protected bool $dateTimeNotRequired = false;

    protected bool $showImporter = false;

    protected bool $rteForTeaser = false;

    protected bool $showAdministrationModule = true;

    protected bool $hidePageTreeForAdministrationModule = false;

    protected int $storageUidImporter = 1;

    protected string $resourceFolderImporter = '/address_import';

    protected string $slugBehaviour = 'unique';

    public function getTagPid(): int
    {
        return $this->tagPid;
    }

    public function getPrependAtCopy(): bool
    {
        return $this->prependAtCopy;
    }

    public function getCategoryRestriction(): string
    {
        return $this->categoryRestriction;
    }

    /**
     * Get categoryBeGroupTceFormsRestriction
     */
    public function getCategoryBeGroupTceFormsRestriction(): bool
    {
        return $this->categoryBeGroupTceFormsRestriction;
    }

    public function getContentElementRelation(): bool
    {
        return $this->contentElementRelation;
    }

    public function getContentElementPreview(): bool
    {
        return $this->contentElementPreview;
    }

    public function getManualSorting(): bool
    {
        return $this->manualSorting;
    }

    public function getArchiveDate(): string
    {
        return $this->archiveDate;
    }

    public function getShowImporter(): bool
    {
        return $this->showImporter;
    }

    public function setShowAdministrationModule(bool $showAdministrationModule): void
    {
        $this->showAdministrationModule = $showAdministrationModule;
    }

    public function getShowAdministrationModule(): bool
    {
        return $this->showAdministrationModule;
    }

    public function getRteForTeaser(): bool
    {
        return $this->rteForTeaser;
    }

    public function getResourceFolderImporter(): string
    {
        return $this->resourceFolderImporter;
    }

    public function getStorageUidImporter(): int
    {
        return $this->storageUidImporter;
    }

    public function getHidePageTreeForAdministrationModule(): bool
    {
        return $this->hidePageTreeForAdministrationModule;
    }

    public function getSlugBehaviour(): string
    {
        return $this->slugBehaviour;
    }
}
