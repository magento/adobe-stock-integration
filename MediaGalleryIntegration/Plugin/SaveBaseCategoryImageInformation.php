<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryIntegration\Plugin;

use Magento\MediaGalleryApi\Api\GetAssetsByPathsInterface;
use Magento\MediaGalleryApi\Api\DeleteAssetsByPathsInterface;
use Magento\Catalog\Model\ImageUploader;
use Magento\Cms\Model\Wysiwyg\Images\Storage;
use Magento\MediaGallerySynchronizationApi\Api\SynchronizeFilesInterface;
use Magento\MediaGallerySynchronization\Model\Filesystem\SplFileInfoFactory;

/**
 * Save base category image by SaveAssetsInterface.
 */
class SaveBaseCategoryImageInformation
{
    /**
     * @var DeleteAssetsByPathsInterface
     */
    private $deleteAssetsByPaths;

    /**
     * @var GetAssetsByPathsInterface
     */
    private $getAssetsByPaths;

    /**
     * @var SplFileInfoFactory
     */
    private $splFileInfoFactory;

    /**
     * @var Storage
     */
    private $storage;
    
    /**
     * @var SynchronizeFilesInterface
     */
    private $synchronizeFiles;

    /**
     * @param DeleteAssetsByPathsInterface $deleteAssetsByPath
     * @param GetAssetsByPathsInterface $getAssetsByPaths
     * @param Storage $storage
     * @param SynchronizeFilesInterface $synchronizeFiles
     * @param SplFileInfoFactory $splFileInfoFactory
     */
    public function __construct(
        DeleteAssetsByPathsInterface $deleteAssetsByPath,
        GetAssetsByPathsInterface $getAssetsByPaths,
        Storage $storage,
        SynchronizeFilesInterface $synchronizeFiles,
        SplFileInfoFactory $splFileInfoFactory
    ) {
        $this->deleteAssetsByPaths = $deleteAssetsByPath;
        $this->getAssetsByPaths = $getAssetsByPaths;
        $this->storage = $storage;
        $this->synchronizeFiles = $synchronizeFiles;
        $this->splFileInfoFactory = $splFileInfoFactory;
    }

    /**
     * Saves base category image information after moving from tmp folder.
     *
     * @param ImageUploader $subject
     * @param string $imagePath
     */
    public function afterMoveFileFromTmp(ImageUploader $subject, string $imagePath): string
    {
        $absolutePath = $this->storage->getCmsWysiwygImages()->getStorageRoot() . $imagePath;
        $file = $this->splFileInfoFactory->create($absolutePath);
        $tmpPath = $subject->getBaseTmpPath() . '/' . $file->getFileName();
        $tmpAssets = $this->getAssetsByPaths->execute([$tmpPath]);
        
        if (!empty($tmpAssets)) {
            $this->deleteAssetsByPaths->execute([$tmpAssets[0]->getPath()]);
        }

        $this->synchronizeFiles->execute([$absolutePath]);

        return $imagePath;
    }
}
