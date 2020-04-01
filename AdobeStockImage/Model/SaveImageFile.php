<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockImage\Model;

use Magento\AdobeStockAssetApi\Api\AssetRepositoryInterface;
use Magento\AdobeStockImage\Model\Extract\MediaGalleryAsset as DocumentToMediaGalleryAsset;
use Magento\AdobeStockImage\Model\Storage\Delete as StorageDelete;
use Magento\AdobeStockImage\Model\Storage\Save as StorageSave;
use Magento\Framework\Api\Search\Document;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Filesystem;
use Magento\MediaGalleryApi\Model\Asset\Command\SaveInterface;

/**
 * Save image file of provided with the adobe Stock integration.
 */
class SaveImageFile
{
    /**
     * @var StorageSave
     */
    private $storageSave;

    /**
     * @var StorageDelete
     */
    private $storageDelete;

    /**
     * @var AssetRepositoryInterface
     */
    private $assetRepository;

    /**
     * @var SaveInterface
     */
    private $saveMediaAsset;

    /**
     * @var DocumentToMediaGalleryAsset
     */
    private $documentToMediaGalleryAsset;

    /**
     * @var Filesystem
     */
    private $fileSystem;

    /**
     * @param StorageSave $storageSave
     * @param StorageDelete $storageDelete
     * @param SaveInterface $saveMediaAsset
     * @param DocumentToMediaGalleryAsset $documentToMediaGalleryAsset
     * @param Filesystem $fileSystem
     */
    public function __construct(
        StorageSave $storageSave,
        StorageDelete $storageDelete,
        SaveInterface $saveMediaAsset,
        DocumentToMediaGalleryAsset $documentToMediaGalleryAsset,
        Filesystem $fileSystem
    ) {
        $this->storageSave = $storageSave;
        $this->storageDelete = $storageDelete;
        $this->saveMediaAsset = $saveMediaAsset;
        $this->documentToMediaGalleryAsset = $documentToMediaGalleryAsset;
        $this->fileSystem = $fileSystem;
    }

    /**
     * Downloads the image and save it to file system database.
     *
     * @param Document $document
     * @param string $url
     * @param string $destinationPath
     *
     * @return int
     * @throws CouldNotSaveException
     * @throws AlreadyExistsException
     * @throws CouldNotDeleteException
     * @throws FileSystemException
     * @throws NoSuchEntityException
     */
    public function execute(Document $document, string $url, string $destinationPath): int
    {
        $pathAttribute = $document->getCustomAttribute('path');
        $pathValue = $pathAttribute->getValue();
        /* If the asset has been already saved, delete the previous version */
        if (null !== $pathAttribute && $pathValue) {
            $this->storageDelete->execute($pathValue);
        }

        $path = $this->storageSave->execute($url, $destinationPath);

        $mediaDirectory = $this->fileSystem->getDirectoryRead(DirectoryList::MEDIA);
        $absolutePath = $mediaDirectory->getAbsolutePath($path);
        $fileSize = $mediaDirectory->stat($absolutePath)['size'];

        $mediaGalleryAsset = $this->documentToMediaGalleryAsset->convert(
            $document,
            [
                'id' => null,
                'path' => $path,
                'source' => 'Adobe Stock',
                'size' => $fileSize
            ]
        );
        $mediaGalleryAssetId = $this->saveMediaAsset->execute($mediaGalleryAsset);

        if (!$mediaGalleryAssetId) {
            $mediaGalleryAssetId = $this->assetRepository->getById($document->getId())->getMediaGalleryId();
        }

        return $mediaGalleryAssetId;
    }
}
