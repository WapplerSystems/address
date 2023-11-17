<?php

namespace WapplerSystems\Address\Domain\Service;

/**
 * This file is part of the "address" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

use WapplerSystems\Address\Domain\Model\Address;
use WapplerSystems\Address\Domain\Model\FileReference;
use WapplerSystems\Address\Domain\Model\Link;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Log\LogManager;

/**
 * Address Import Service
 *
 */
class AddressImportService extends AbstractImportService
{
    const ACTION_IMPORT_L10N_OVERLAY = 1;

    /**
     * @var \WapplerSystems\Address\Domain\Repository\AddressRepository
     * @inject
     */
    protected $addressRepository;

    /**
     * @var \WapplerSystems\Address\Domain\Repository\TtContentRepository
     */
    protected $ttContentRepository;

    /**
     * @var \WapplerSystems\Address\Domain\Repository\CategoryRepository
     */
    protected $categoryRepository;


    /**
     * @var array
     */
    protected $settings = [];

    public function __construct()
    {
        $logger = GeneralUtility::makeInstance(LogManager::class)->getLogger(__CLASS__);
        $this->logger = $logger;

        parent::__construct();
    }


    /**
     * Inject the category repository
     *
     * @param \WapplerSystems\Address\Domain\Repository\CategoryRepository $categoryRepository
     */
    public function injectCategoryRepository(\WapplerSystems\Address\Domain\Repository\CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * Inject the ttcontent repository
     *
     * @param \WapplerSystems\Address\Domain\Repository\TtContentRepository $ttContentRepository
     */
    public function injectTtContentRepository(
        \WapplerSystems\Address\Domain\Repository\TtContentRepository $ttContentRepository
    ) {
        $this->ttContentRepository = $ttContentRepository;
    }


    /**
     * @param array $importItem
     * @return null|\WapplerSystems\Address\Domain\Model\Address
     */
    protected function initializeAddressRecord(array $importItem)
    {
        $address = null;

        $this->logger->info(sprintf('Import of address from source "%s" with id "%s"', $importItem['import_source'],
            $importItem['import_id']));

        if ($importItem['import_source'] && $importItem['import_id']) {
            $address = $this->addressRepository->findOneByImportSourceAndImportId($importItem['import_source'],
                $importItem['import_id']);
        }

        if ($address === null) {
            $address = new Address();
            $this->addressRepository->add($address);
        } else {
            $this->logger->info(sprintf('Address exists already with id "%s".', $address->getUid()));
            $this->addressRepository->update($address);
        }

        return $address;
    }

    /**
     * @param \WapplerSystems\Address\Domain\Model\Address $address
     * @param array $importItem
     * @param array $importItemOverwrite
     * @return \WapplerSystems\Address\Domain\Model\Address
     */
    protected function hydrateAddressRecord(
        \WapplerSystems\Address\Domain\Model\Address $address,
        array $importItem,
        array $importItemOverwrite
    ) {
        if (!empty($importItemOverwrite)) {
            $importItem = array_merge($importItem, $importItemOverwrite);
        }

        $address->setPid($importItem['pid']);
        $address->setHidden($importItem['hidden']);
        $address->setStarttime($importItem['starttime']);
        $address->setEndtime($importItem['endtime']);
        if (!empty($importItem['fe_group'])) {
            $address->setFeGroup((string)$importItem['fe_group']);
        }
        $address->setTstamp($importItem['tstamp']);
        $address->setCrdate($importItem['crdate']);
        $address->setSysLanguageUid($importItem['sys_language_uid']);
        $address->setSorting((int)$importItem['sorting']);

        $address->setTitle($importItem['title']);
        $address->setTeaser($importItem['teaser']);
        $address->setBodytext($importItem['bodytext']);

        $address->setType((string)$importItem['type']);
        $address->setKeywords($importItem['keywords']);
        $address->setArchive(new \DateTime(date('Y-m-d H:i:sP', $importItem['archive'])));

        $contentElementUidArray = GeneralUtility::trimExplode(',', $importItem['content_elements'], true);
        foreach ($contentElementUidArray as $contentElementUid) {
            if (is_object($contentElement = $this->ttContentRepository->findByUid($contentElementUid))) {
                $address->addContentElement($contentElement);
            }
        }

        $address->setInternalurl($importItem['internalurl']);
        $address->setExternalurl($importItem['externalurl']);

        $address->setType($importItem['type']);
        $address->setKeywords($importItem['keywords']);

        $address->setImportId($importItem['import_id']);
        $address->setImportSource($importItem['import_source']);

        if (is_array($importItem['categories'])) {
            foreach ($importItem['categories'] as $categoryUid) {
                if ($this->settings['findCategoriesByImportSource']) {
                    $category = $this->categoryRepository->findOneByImportSourceAndImportId(
                        $this->settings['findCategoriesByImportSource'], $categoryUid);
                } else {
                    $category = $this->categoryRepository->findByUid($categoryUid);
                }

                if ($category) {
                    $address->addCategory($category);
                } else {
                    $this->logger->warning(sprintf('Category with ID "%s" was not found', $categoryUid));
                }
            }
        }

        // media relation
        if (is_array($importItem['media'])) {
            foreach ($importItem['media'] as $mediaItem) {
                // get fileobject by given identifier (file UID, combined identifier or path/filename)
                try {
                    $file = $this->getResourceFactory()->retrieveFileOrFolderObject($mediaItem['image']);
                } catch (\TYPO3\CMS\Core\Resource\Exception\ResourceDoesNotExistException $exception) {
                    $file = false;
                }

                // no file found skip processing of this item
                if ($file === false) {
                    continue;
                }

                // file not inside a storage then search for same file based on hash (to prevent duplicates)
                if ($file->getStorage()->getUid() === 0) {
                    $existingFile = $this->findFileByHash($file->getSha1());
                    if ($existingFile !== null) {
                        $file = $existingFile;
                    }
                }

                /** @var $media FileReference */
                if (!$media = $this->getIfFalRelationIfAlreadyExists($address->getFalMedia(), $file)) {

                    // file not inside a storage copy the one form storage 0 to the import folder
                    if ($file->getStorage()->getUid() === 0) {
                        $file = $this->getResourceStorage()->copyFile($file, $this->getImportFolder());
                    }

                    $media = $this->objectManager->get(FileReference::class);
                    $media->setFileUid($file->getUid());
                    $address->addFalMedia($media);
                }

                if ($media) {
                    $media->setTitle($mediaItem['title']);
                    $media->setAlternative($mediaItem['alt']);
                    $media->setDescription($mediaItem['caption']);
                    $media->setShowinpreview($mediaItem['showinpreview']);
                    $media->setPid($importItem['pid']);
                }
            }
        }

        // related files
        if (is_array($importItem['related_files'])) {
            foreach ($importItem['related_files'] as $fileItem) {

                // get fileObject by given identifier (file UID, combined identifier or path/filename)
                try {
                    $file = $this->getResourceFactory()->retrieveFileOrFolderObject($fileItem['file']);
                } catch (\TYPO3\CMS\Core\Resource\Exception\ResourceDoesNotExistException $exception) {
                    $file = false;
                }

                // no file found skip processing of this item
                if ($file === false) {
                    continue;
                }

                // file not inside a storage then search for same file based on hash (to prevent duplicates)
                if ($file->getStorage()->getUid() === 0) {
                    $existingFile = $this->findFileByHash($file->getSha1());
                    if ($existingFile !== null) {
                        $file = $existingFile;
                    }
                }

                /** @var $relatedFile FileReference */
                if (!$relatedFile = $this->getIfFalRelationIfAlreadyExists($address->getFalRelatedFiles(), $file)) {

                    // file not inside a storage copy the one form storage 0 to the import folder
                    if ($file->getStorage()->getUid() === 0) {
                        $file = $this->getResourceStorage()->copyFile($file, $this->getImportFolder());
                    }

                    $relatedFile = $this->objectManager->get(FileReference::class);
                    $relatedFile->setFileUid($file->getUid());
                    $address->addFalRelatedFile($relatedFile);
                }

                if ($relatedFile) {
                    $relatedFile->setTitle($fileItem['title']);
                    $relatedFile->setDescription($fileItem['description']);
                    $relatedFile->setPid($importItem['pid']);
                }
            }
        }

        if (is_array($importItem['related_links'])) {
            foreach ($importItem['related_links'] as $link) {
                /** @var $relatedLink Link */
                if (($relatedLink = $this->getRelatedLinkIfAlreadyExists($address, $link['uri'])) === false) {
                    $relatedLink = $this->objectManager->get(\WapplerSystems\Address\Domain\Model\Link::class);
                    $relatedLink->setUri($link['uri']);
                    $address->addRelatedLink($relatedLink);
                }
                $relatedLink->setTitle($link['title']);
                $relatedLink->setDescription($link['description']);
                $relatedLink->setPid($importItem['pid']);
            }
        }

        $arguments = ['importItem' => $importItem, 'address' => $address];
        $this->emitSignal('postHydrate', $arguments);

        return $address;
    }

    /**
     * Import
     *
     * @param array $importData
     * @param array $importItemOverwrite
     * @param array $settings
     */
    public function import(array $importData, array $importItemOverwrite = [], $settings = [])
    {
        $this->settings = $settings;
        $this->logger->info(sprintf('Starting import for %s address', count($importData)));

        foreach ($importData as $importItem) {
            $arguments = ['importItem' => $importItem];
            $return = $this->emitSignal('preHydrate', $arguments);
            $importItem = $return['importItem'];

            // Store language overlay in post persist queue
            if ((int)$importItem['sys_language_uid'] > 0 && (string)$importItem['l10n_parent'] !== '0') {
                $this->postPersistQueue[$importItem['import_id']] = [
                    'action' => self::ACTION_IMPORT_L10N_OVERLAY,
                    'category' => null,
                    'importItem' => $importItem
                ];
                continue;
            }

            $address = $this->initializeAddressRecord($importItem);

            $this->hydrateAddressRecord($address, $importItem, $importItemOverwrite);
        }

        $this->persistenceManager->persistAll();

        foreach ($this->postPersistQueue as $queueItem) {
            if ($queueItem['action'] == self::ACTION_IMPORT_L10N_OVERLAY) {
                $this->importL10nOverlay($queueItem, $importItemOverwrite);
            }
        }

        $this->persistenceManager->persistAll();
    }

    /**
     * @param array $queueItem
     * @param array $importItemOverwrite
     */
    protected function importL10nOverlay(array $queueItem, array $importItemOverwrite)
    {
        $importItem = $queueItem['importItem'];
        $parentAddress = $this->addressRepository->findOneByImportSourceAndImportId(
            $importItem['import_source'],
            $importItem['l10n_parent']
        );

        if ($parentAddress !== null) {
            $address = $this->initializeAddressRecord($importItem);

            $this->hydrateAddressRecord($address, $importItem, $importItemOverwrite);

            $address->setSysLanguageUid($importItem['sys_language_uid']);
            $address->setL10nParent($parentAddress->getUid());
        }
    }

    /**
     * Get an existing items from the references that matches the file
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\WapplerSystems\Address\Domain\Model\FileReference> $items
     * @param \TYPO3\CMS\Core\Resource\File $file
     * @return bool|FileReference
     */
    protected function getIfFalRelationIfAlreadyExists(
        \TYPO3\CMS\Extbase\Persistence\ObjectStorage $items,
        \TYPO3\CMS\Core\Resource\File $file
    ) {
        $result = false;
        if ($items->count() !== 0) {
            /** @var $item FileReference */
            foreach ($items as $item) {
                // only check already persisted items
                if ($item->getFileUid() === (int)$file->getUid()
                    ||
                    ($item->getUid() &&
                        $item->getOriginalResource()->getName() === $file->getName() &&
                        $item->getOriginalResource()->getSize() === (int)$file->getSize())
                ) {
                    $result = $item;
                    break;
                }
            }
        }
        return $result;
    }

    /**
     * Get an existing related link object
     *
     * @param \WapplerSystems\Address\Domain\Model\Address $address
     * @param string $uri
     * @return bool|Link
     */
    protected function getRelatedLinkIfAlreadyExists(\WapplerSystems\Address\Domain\Model\Address $address, $uri)
    {
        $result = false;
        $links = $address->getRelatedLinks();

        if (!empty($links) && $links->count() !== 0) {
            foreach ($links as $link) {
                if ($link->getUri() === $uri) {
                    $result = $link;
                    break;
                }
            }
        }
        return $result;
    }

    /**
     * Emits signal
     *
     * @param string $signalName name of the signal slot
     * @param array $signalArguments arguments for the signal slot
     */
    protected function emitSignal($signalName, array $signalArguments)
    {
        return $this->signalSlotDispatcher->dispatch('WapplerSystems\\Address\\Domain\\Service\\AddressImportService', $signalName,
            $signalArguments);
    }
}
