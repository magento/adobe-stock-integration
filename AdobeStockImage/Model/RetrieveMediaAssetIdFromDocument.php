<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockImage\Model;

use Magento\AdobeStockAssetApi\Api\AssetRepositoryInterface;
use Magento\AdobeStockImage\Model\Extract\MediaGalleryAsset as DocumentToMediaGalleryAsset;
use Magento\Framework\Api\Search\Document;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Filesystem;
use Magento\AdobeStockAssetApi\Api\Data\AssetInterface;
use Magento\MediaGalleryApi\Api\Data\AssetInterface as MediaGalleryAssetInterface;
use Magento\MediaGalleryApi\Model\Asset\Command\SaveInterface;

/**
 * Process save action of the MediaGalleryAsset.
 */
class RetrieveMediaAssetIdFromDocument implements RetrieveMediaAssetIdFromDocumentInterface
{
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
     * SaveMediaGalleryAsset constructor.
     *
     * @param SaveInterface $saveMediaAsset
     * @param DocumentToMediaGalleryAsset $documentToMediaGalleryAsset
     * @param AssetRepositoryInterface $assetRepository
     * @param Filesystem $fileSystem
     */
    public function __construct(
        SaveInterface $saveMediaAsset,
        DocumentToMediaGalleryAsset $documentToMediaGalleryAsset,
        AssetRepositoryInterface $assetRepository,
        Filesystem $fileSystem
    ) {
        $this->saveMediaAsset = $saveMediaAsset;
        $this->documentToMediaGalleryAsset = $documentToMediaGalleryAsset;
        $this->assetRepository = $assetRepository;
        $this->fileSystem = $fileSystem;
    }

    /**
     * Process saving MediaGalleryAsset based on the search document and destination path.
     *
     * @param Document $document
     * @param string $destinationPath
     *
     * @return int
     * @throws CouldNotSaveException
     * @throws NoSuchEntityException
     */
    public function execute(Document $document, string $destinationPath): int
    {
        $fileSize = $this->calculateFileSize($destinationPath);
        $additionalData = [
            'id' => null,
            'path' => $destinationPath,
            'source' => 'Adobe Stock',
            'size' => $fileSize,
        ];

        /** @var MediaGalleryAssetInterface $mediaGalleryAsset */
        $mediaGalleryAsset = $this->documentToMediaGalleryAsset->convert($document, $additionalData);
        $mediaGalleryAssetId = $this->saveMediaAsset->execute($mediaGalleryAsset);
        if (!$mediaGalleryAssetId) {
            /** @var AssetInterface $mediaGalleryAsset */
            $mediaGalleryAsset = $this->assetRepository->getById($document->getId());
            $mediaGalleryAssetId = $mediaGalleryAsset->getMediaGalleryId();
        }

        return $mediaGalleryAssetId;
    }

    /**
     * Calculates saved image file size.
     *
     * @param string $destinationPath
     *
     * @return int
     */
    private function calculateFileSize(string $destinationPath): int
    {
        $mediaDirectory = $this->fileSystem->getDirectoryRead(DirectoryList::MEDIA);
        $absolutePath = $mediaDirectory->getAbsolutePath($destinationPath);
        $fileSize = $mediaDirectory->stat($absolutePath)['size'];

        return $fileSize;
    }
}
