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
use Magento\MediaGallerySynchronizationApi\Api\SynchronizeFilesInterface;
use Magento\MediaGallerySynchronization\Model\Filesystem\SplFileInfoFactory;

/**
 * Uploads an image to storage
 */
class UploadImage
{
    /**
     * @var Storage
     */
    private $imagesStorage;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var SplFileInfoFactory
     */
    private $splFileInfoFactory;

    /**
     * @var SynchronizeFilesInterface
     */
    private $synchronizeFiles;

    /**
     * @param Storage $imagesStorage
     * @param SplFileInfoFactory $splFileInfoFactory
     * @param Filesystem $filesystem
     * @param SynchronizeFilesInterface $synchronizeFiles
     */
    public function __construct(
        Storage $imagesStorage,
        SplFileInfoFactory $splFileInfoFactory,
        Filesystem $filesystem,
        SynchronizeFilesInterface $synchronizeFiles
    ) {
        $this->imagesStorage = $imagesStorage;
        $this->splFileInfoFactory = $splFileInfoFactory;
        $this->filesystem = $filesystem;
        $this->synchronizeFiles = $synchronizeFiles;
    }

    /**
     * Uploads the image and returns file object
     *
     * @param string $targetFolder
     * @param string|null $type
     * @throws LocalizedException
     */
    public function execute(string $targetFolder, string $type = null): void
    {
        $mediaDirectory = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);
        if (!$mediaDirectory->isDirectory($targetFolder)) {
            throw new LocalizedException(__('Directory %1 does not exist in media directory.', $targetFolder));
        }
        $absolutePath = $mediaDirectory->getAbsolutePath($targetFolder);
        $uploadResult = $this->imagesStorage->uploadFile($absolutePath, $type);
        $this->synchronizeFiles->execute(
            [
                $this->splFileInfoFactory->create(
                    $uploadResult['path'] . '/' . $uploadResult['file']
                )
            ]
        );
    }
}
