<?php
declare(strict_types=1);

namespace WapplerSystems\Address\Updates;

/**
 * This file is part of the "address" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use TYPO3\CMS\Install\Attribute\UpgradeWizard;
use WapplerSystems\Address\Service\SlugService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Install\Updates\DatabaseUpdatedPrerequisite;
use TYPO3\CMS\Install\Updates\UpgradeWizardInterface;

/**
 * Migrate empty slugs
 */
#[UpgradeWizard('addressSlugUpdater')]
class AddressSlugUpdater implements UpgradeWizardInterface
{
    const TABLE = 'tx_address_domain_model_address';

    /** @var SlugService */
    protected $slugService;

    public function __construct()
    {
        $this->slugService = GeneralUtility::makeInstance(SlugService::class);
    }

    public function executeUpdate(): bool
    {
        $this->slugService->performUpdates();
        return true;
    }

    public function updateNecessary(): bool
    {
        $elementCount = $this->slugService->countOfSlugUpdates();

        return $elementCount > 0;
    }

    public function getPrerequisites(): array
    {
        return [
            DatabaseUpdatedPrerequisite::class
        ];
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle(): string
    {
        return 'Updates slug field "path_segment" of EXT:address records';
    }

    /**
     * Get description
     *
     * @return string Longer description of this updater
     */
    public function getDescription(): string
    {
        return 'Fills empty slug field "path_segment" of EXT:address records with urlized title. '.$this->slugService->countOfSlugUpdates().' elements need to be updated.';
    }

    /**
     * @return string Unique identifier of this updater
     */
    public function getIdentifier(): string
    {
        return 'addressSlug';
    }
}
