<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryUi\Model;

use Magento\Cms\Model\Wysiwyg\Images\Storage;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\MediaGalleryApi\Api\Data\AssetInterface;

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
     * @var Filesystem
     */
    private $filesystem;

    /**
     * DeleteImage constructor.
     *
     * @param Storage $imagesStorage
     * @param Filesystem $filesystem
     */
    public function __construct(
        Storage $imagesStorage,
        Filesystem $filesystem
    ) {
        $this->imagesStorage = $imagesStorage;
        $this->filesystem = $filesystem;
    }

    /**
     * Delete asset from a storage
     *
     * @param AssetInterface $asset
     */
    public function execute(AssetInterface $asset): void
    {
        $mediaDirectory = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);
        $absolutePath = $mediaDirectory->getAbsolutePath($asset->getPath());
        $this->imagesStorage->deleteFile($absolutePath);
    }
}
