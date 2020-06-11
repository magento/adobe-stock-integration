<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryIntegration\Plugin;

use Magento\MediaGalleryApi\Api\SaveAssetsInterface;
use Magento\Framework\File\Uploader;
use Magento\MediaGallerySynchronization\Model\CreateAssetFromFile;
use Magento\Cms\Model\Wysiwyg\Images\Storage;
use Magento\MediaGallerySynchronization\Model\Filesystem\SplFileInfoFactory;

/**
 * Save image information by SaveAssetsInterface.
 */
class SaveImageInformation
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
     * @var CreateAssetFromFile
     */
    private $createAssetFromFile;

    /**
     * @var Storage
     */
    private $storage;

    /**
     * @param SplFileInfoFactory $splFileInfoFactory
     * @param CreateAssetFromFile $createAssetFromFile
     * @param SaveAssetsInterface $saveAsset
     * @param Storage $storage
     */
    public function __construct(
        SplFileInfoFactory $splFileInfoFactory,
        CreateAssetFromFile $createAssetFromFile,
        SaveAssetsInterface $saveAsset,
        Storage $storage
    ) {
        $this->splFileInfoFactory = $splFileInfoFactory;
        $this->createAssetFromFile = $createAssetFromFile;
        $this->saveAsset = $saveAsset;
        $this->storage = $storage;
    }

    /**
     * Saves base category image information after moving from tmp folder.
     *
     * @param Uploader $subject
     * @param array $result
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterSave(Uploader $subject, array $result): array
    {
        $file = $this->splFileInfoFactory->create($result['path'] . '/' . $result['file']);
        $this->saveAsset->execute([$this->createAssetFromFile->execute($file)]);
        $this->storage->resizeFile($result['path'] . '/' . $result['file']);

        return $result;
    }
}
