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
use Magento\MediaGalleryUi\Model\Filesystem\GetSplFileInfo;

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
     * @var GetSplFileInfo
     */
    private $getSplFileInfo;

    /**
     * UploadImage constructor.
     *
     * @param Storage $imagesStorage
     * @param GetSplFileInfo $getSplFileInfo
     * @param Filesystem $filesystem
     */
    public function __construct(
        Storage $imagesStorage,
        GetSplFileInfo $getSplFileInfo,
        Filesystem $filesystem
    ) {
        $this->getSplFileInfo = $getSplFileInfo;
        $this->imagesStorage = $imagesStorage;
        $this->filesystem = $filesystem;
    }

    /**
     * Uploads the image and returns file object
     *
     * @param string $path
     * @param string|null $type
     * @return \SplFileInfo
     * @throws LocalizedException
     */
    public function execute(string $path, string $type = null) : \SplFileInfo
    {
        $mediaDirectory = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);
        if (!$mediaDirectory->isDirectory($path)) {
            throw new LocalizedException(__('Directory %1 does not exist in media directory.', $path));
        }
        $uploadResult = $this->imagesStorage->uploadFile($path, $type);
        return $this->getSplFileInfo->execute($uploadResult['path'] . '/' . $uploadResult['file']);
    }
}
