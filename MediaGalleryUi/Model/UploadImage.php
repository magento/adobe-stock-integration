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
use Magento\MediaGalleryUi\Model\Filesystem\SplFileInfoFactory;

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
     * UploadImage constructor.
     * @param Storage $imagesStorage
     * @param SplFileInfoFactory $splFileInfoFactory
     * @param Filesystem $filesystem
     */
    public function __construct(
        Storage $imagesStorage,
        SplFileInfoFactory $splFileInfoFactory,
        Filesystem $filesystem
    ) {
        $this->splFileInfoFactory = $splFileInfoFactory;
        $this->imagesStorage = $imagesStorage;
        $this->filesystem = $filesystem;
    }

    /**
     * Uploads the image and returns file object
     *
     * @param string $targetFolder
     * @param string|null $type
     * @return \SplFileInfo
     * @throws LocalizedException
     */
    public function execute(string $targetFolder, string $type = null) : \SplFileInfo
    {
        $mediaDirectory = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);
        if (!$mediaDirectory->isDirectory($targetFolder)) {
            throw new LocalizedException(__('Directory %1 does not exist in media directory.', $targetFolder));
        }
        $uploadResult = $this->imagesStorage->uploadFile($mediaDirectory->getAbsolutePath($targetFolder), $type);
        return $this->splFileInfoFactory->create($uploadResult['path'] . '/' . $uploadResult['file']);
    }
}
