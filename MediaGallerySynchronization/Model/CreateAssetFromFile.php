<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGallerySynchronization\Model;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\ValidatorException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\Read;
use Magento\Framework\Filesystem\Driver\File;
use Magento\MediaGalleryApi\Api\Data\AssetInterface;
use Magento\MediaGalleryApi\Api\Data\AssetInterfaceFactory;

/**
 * Create media asset object based on the file information
 */
class CreateAssetFromFile
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var File
     */
    private $driver;

    /**
     * @var Read
     */
    private $mediaDirectory;

    /**
     * @var AssetInterfaceFactory
     */
    private $assetFactory;

    /**
     * @param Filesystem $filesystem
     * @param AssetInterfaceFactory $assetFactory
     * @param File $driver
     */
    public function __construct(
        Filesystem $filesystem,
        AssetInterfaceFactory $assetFactory,
        File $driver
    ) {
        $this->filesystem = $filesystem;
        $this->assetFactory = $assetFactory;
        $this->driver = $driver;
    }

    /**
     * Create media asset object based on the file information
     *
     * @param \SplFileInfo $file
     * @return AssetInterface
     * @throws ValidatorException
     */
    public function execute(\SplFileInfo $file)
    {
        $path = $file->getPath() . '/' . $file->getFileName();

        [$width, $height] = getimagesize($path);

        return $this->assetFactory->create(
            [
                'path' => $this->getPath($path),
                'title' => $file->getBasename('.' . $file->getExtension()),
                'createdAt' => (new \DateTime())->setTimestamp($file->getCTime())->format('Y-m-d H:i:s'),
                'updatedAt' => (new \DateTime())->setTimestamp($file->getMTime())->format('Y-m-d H:i:s'),
                'width' => $width,
                'height' => $height,
                'size' => $file->getSize(),
                'contentType' => 'image/' . $file->getExtension(),
                'source' => 'Local'
            ]
        );
    }

    /**
     * Get correct path for media asset
     *
     * @param string $file
     * @return string
     * @throws ValidatorException
     */
    private function getPath(string $file): string
    {
        $path = $this->getMediaDirectory()->getRelativePath($file);

        if ($this->driver->getParentDirectory($path) === '.') {
            $path = '/' . $path;
        }

        return $path;
    }

    /**
     * Retrieve media directory instance with read permissions
     *
     * @return Read
     */
    private function getMediaDirectory(): Read
    {
        if (!$this->mediaDirectory) {
            $this->mediaDirectory = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);
        }
        return $this->mediaDirectory;
    }
}
