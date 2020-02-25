<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryUi\Model;

use Magento\Cms\Model\Wysiwyg\Images\Storage;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\MediaGalleryApi\Model\Asset\Command\GetByIdInterface;

/**
 * Load Media Asset path from database by id and delete the file
 */
class DeleteImageByAssetId
{
    /**
     * @var GetByIdInterface
     */
    private $getAssetById;

    /**
     * @var Storage
     */
    private $imagesStorage;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * DeleteAssetById constructor.
     *
     * @param GetByIdInterface $getAssetById
     * @param Storage $imagesStorage
     * @param Filesystem $filesystem
     */
    public function __construct(
        GetByIdInterface $getAssetById,
        Storage $imagesStorage,
        Filesystem $filesystem
    ) {
        $this->getAssetById = $getAssetById;
        $this->imagesStorage = $imagesStorage;
        $this->filesystem = $filesystem;
    }

    /**
     * Delete image by asset ID
     *
     * @param int $assetId
     *
     * @return void
     *
     * @throws LocalizedException
     */
    public function execute(int $assetId): void
    {
        $image = $this->getAssetById->execute($assetId);
        $mediaFilePath = $image->getPath();
        $mediaDirectory = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);
        $absoluteMediaPath = $mediaDirectory->getAbsolutePath();

        if (!$mediaDirectory->isFile($mediaFilePath)) {
            throw new LocalizedException(__('File "%1" does not exist in media directory.', $mediaFilePath));
        }

        $this->imagesStorage->deleteFile($absoluteMediaPath . $mediaFilePath);
    }
}
