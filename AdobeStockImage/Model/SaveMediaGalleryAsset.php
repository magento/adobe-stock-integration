<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdobeStockImage\Model;

use Magento\AdobeStockImage\Model\Extract\MediaGalleryAsset as DocumentToMediaGalleryAsset;
use Magento\Framework\Api\Search\Document;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Filesystem;
use Magento\MediaGalleryApi\Api\SaveAssetsInterface;

/**
 * Process save action of the media gallery asset.
 */
class SaveMediaGalleryAsset
{
    /**
     * @var SaveAssetsInterface
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
     * @param SaveAssetsInterface $saveMediaAsset
     * @param DocumentToMediaGalleryAsset $documentToMediaGalleryAsset
     * @param Filesystem $fileSystem
     */
    public function __construct(
        SaveAssetsInterface $saveMediaAsset,
        DocumentToMediaGalleryAsset $documentToMediaGalleryAsset,
        Filesystem $fileSystem
    ) {
        $this->saveMediaAsset = $saveMediaAsset;
        $this->documentToMediaGalleryAsset = $documentToMediaGalleryAsset;
        $this->fileSystem = $fileSystem;
    }

    /**
     * Process saving MediaGalleryAsset based on the search document and destination path.
     *
     * @param Document $document
     * @param string $destinationPath
     * @return void
     * @throws CouldNotSaveException
     */
    public function execute(Document $document, string $destinationPath): void
    {
        try {
            $fileSize = $this->calculateFileSize($destinationPath);
            $additionalData = [
                'id' => null,
                'path' => $destinationPath,
                'source' => 'Adobe Stock',
                'size' => $fileSize,
            ];

            $mediaGalleryAsset = $this->documentToMediaGalleryAsset->convert($document, $additionalData);
            $this->saveMediaAsset->execute([$mediaGalleryAsset]);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__('Could not save media gallery asset.'), $exception);
        }
    }

    /**
     * Calculates saved image file size.
     *
     * @param string $destinationPath
     * @return int
     */
    private function calculateFileSize(string $destinationPath): int
    {
        $mediaDirectory = $this->fileSystem->getDirectoryRead(DirectoryList::MEDIA);
        return $mediaDirectory->stat($mediaDirectory->getAbsolutePath($destinationPath))['size'];
    }
}
