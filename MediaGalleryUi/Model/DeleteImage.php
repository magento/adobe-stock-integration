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
use Magento\MediaGalleryApi\Api\Data\AssetInterface;
use Magento\MediaGalleryApi\Api\IsPathDeniedInterface;

/**
 * Delete image from a storage
 */
class DeleteImage
{
    /**
     * @var Storage
     */
    private $imagesStorage;

    /**
     * @var IsPathDeniedInterface
     */
    private $isPathDenied;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * DeleteImage constructor.
     *
     * @param Storage $imagesStorage
     * @param Filesystem $filesystem
     * @param IsPathDeniedInterface $isPathDenied
     */
    public function __construct(
        Storage $imagesStorage,
        Filesystem $filesystem,
        IsPathDeniedInterface $isPathDenied
    ) {
        $this->imagesStorage = $imagesStorage;
        $this->filesystem = $filesystem;
        $this->isPathDenied = $isPathDenied;
    }

    /**
     * Delete asset image physically from file storage and from data storage via MediaGallery plugin.
     *
     * @see \Magento\MediaGallery\Plugin\Wysiwyg\Images\Storage
     *
     * @param AssetInterface $asset
     * @throws LocalizedException
     */
    public function execute(AssetInterface $asset): void
    {
        if ($this->isPathDenied->execute($asset->getPath())) {
            throw new LocalizedException(__('Could not delete image: destination directory is restricted.'));
        }

        $mediaDirectory = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);
        $absolutePath = $mediaDirectory->getAbsolutePath($asset->getPath());
        $this->imagesStorage->deleteFile($absolutePath);
    }
}
