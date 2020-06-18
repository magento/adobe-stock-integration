<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryIntegration\Plugin;

use Magento\MediaGalleryApi\Api\SaveAssetsInterface;
use Magento\MediaGalleryApi\Api\GetAssetsByPathsInterface;
use Magento\MediaGalleryApi\Api\DeleteAssetsByPathsInterface;
use Magento\Catalog\Model\ImageUploader;
use Magento\MediaGallerySynchronization\Model\CreateAssetFromFile;
use Magento\Cms\Model\Wysiwyg\Images\Storage;
use Magento\MediaGallerySynchronization\Model\Filesystem\SplFileInfoFactory;

/**
 * Save base category image by SaveAssetsInterface.
 */
class SaveBaseCategoryImageInformation
{
    /**
     * @var SplFileInfoFactory
     */
    private $splFileInfoFactory;

    /**
     * @var SaveAssetsInterface
     */
    private $saveAsset;

    /**
     * @var DeleteAssetsByPathsInterface
     */
    private $deleteAssetsByPaths;

    /**
     * @var GetAssetsByPathsInterface
     */
    private $getAssetsByPaths;
    
    /**
     * @var CreateAssetFromFile
     */
    private $createAssetFromFile;

    /**
     * @var Storage
     */
    private $storage;

    /**
     * @param DeleteAssetsByPathsInterface $deleteAssetsByPath
     * @param GetAssetsByPathsInterface $getAssetsByPaths
     * @param SplFileInfoFactory $splFileInfoFactory
     * @param CreateAssetFromFile $createAssetFromFile
     * @param SaveAssetsInterface $saveAsset
     * @param Storage $storage
     */
    public function __construct(
        DeleteAssetsByPathsInterface $deleteAssetsByPath,
        GetAssetsByPathsInterface $getAssetsByPaths,
        SplFileInfoFactory $splFileInfoFactory,
        CreateAssetFromFile $createAssetFromFile,
        SaveAssetsInterface $saveAsset,
        Storage $storage
    ) {
        $this->deleteAssetsByPaths = $deleteAssetsByPath;
        $this->getAssetsByPaths = $getAssetsByPaths;
        $this->splFileInfoFactory = $splFileInfoFactory;
        $this->createAssetFromFile = $createAssetFromFile;
        $this->saveAsset = $saveAsset;
        $this->storage = $storage;
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
        
        $this->saveAsset->execute([$this->createAssetFromFile->execute($file)]);
        $this->storage->resizeFile($absolutePath);

        return $imagePath;
    }
}
