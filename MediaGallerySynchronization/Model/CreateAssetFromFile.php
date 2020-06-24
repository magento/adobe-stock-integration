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
use Magento\MediaGalleryApi\Api\GetAssetsByPathsInterface;

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
     * @var GetAssetsByPathsInterface
     */
    private $getMediaGalleryAssetByPath;

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
     * @param GetAssetsByPathsInterface $getMediaGalleryAssetByPath
     */
    public function __construct(
        Filesystem $filesystem,
        AssetInterfaceFactory $assetFactory,
        File $driver,
        GetAssetsByPathsInterface $getMediaGalleryAssetByPath
    ) {
        $this->filesystem = $filesystem;
        $this->assetFactory = $assetFactory;
        $this->driver = $driver;
        $this->getMediaGalleryAssetByPath = $getMediaGalleryAssetByPath;
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

        $asset = $this->getAsset($path);
        return $this->assetFactory->create(
            [
                'id' => $asset ? $asset->getId() : null,
                'path' => $this->getRelativePath($path),
                'title' => $asset ? $asset->getTitle() : $file->getBasename('.' . $file->getExtension()),
                'createdAt' => (new \DateTime())->setTimestamp($file->getCTime())->format('Y-m-d H:i:s'),
                'updatedAt' => (new \DateTime())->setTimestamp($file->getMTime())->format('Y-m-d H:i:s'),
                'width' => $width,
                'height' => $height,
                'size' => $file->getSize(),
                'contentType' => $asset ? $asset->getContentType() : 'image/' . $file->getExtension(),
                'source' => $asset ? $asset->getSource() : 'Local'
            ]
        );
    }

    /**
     * Returns asset if asset already exist by provided path
     *
     * @param string $path
     * @return null|AssetInterface
     * @throws ValidatorException
     */
    private function getAsset(string $path): ?AssetInterface
    {
        $asset = $this->getMediaGalleryAssetByPath->execute([$this->getRelativePath($path)]);
        return !empty($asset) ? $asset[0] : null;
    }

    /**
     * Get correct path for media asset
     *
     * @param string $file
     * @return string
     * @throws ValidatorException
     */
    private function getRelativePath(string $file): string
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
