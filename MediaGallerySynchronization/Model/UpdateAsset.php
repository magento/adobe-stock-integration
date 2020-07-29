<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGallerySynchronization\Model;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\ValidatorException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\Read;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\MediaGalleryApi\Api\Data\AssetInterface;
use Magento\MediaGalleryApi\Api\Data\AssetInterfaceFactory;
use Magento\MediaGalleryMetadataApi\Api\Data\MetadataInterface;
use Magento\MediaGallerySynchronizationApi\Model\GetContentHashInterface;

class UpdateAsset
{
    /**
     * Date format
     */
    private const DATE_FORMAT = 'Y-m-d H:i:s';

    /**
     * @var Read
     */
    private $mediaDirectory;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var File
     */
    private $driver;

    /**
     * @var TimezoneInterface;
     */
    private $date;

    /**
     * @var AssetInterfaceFactory
     */
    private $assetFactory;

    /**
     * @var GetContentHashInterface
     */
    private $getContentHash;

    /**
     * @param Filesystem $filesystem
     * @param File $driver
     * @param TimezoneInterface $date
     * @param AssetInterfaceFactory $assetFactory
     * @param GetContentHashInterface $getContentHash
     */
    public function __construct(
        Filesystem $filesystem,
        File $driver,
        TimezoneInterface $date,
        AssetInterfaceFactory $assetFactory,
        GetContentHashInterface $getContentHash
    ) {
        $this->filesystem = $filesystem;
        $this->driver = $driver;
        $this->date = $date;
        $this->assetFactory = $assetFactory;
        $this->getContentHash = $getContentHash;
    }

    /**
     * Create and format media asset object
     *
     * @param \SplFileInfo $file
     * @param AssetInterface|null $asset
     * @param MetadataInterface $assetFromFile
     * @return AssetInterface
     * @throws FileSystemException
     * @throws ValidatorException
     */
    public function execute(\SplFileInfo $file, ?AssetInterface $asset, MetadataInterface $assetFromFile): AssetInterface
    {
        $path = $file->getPath() . '/' . $file->getFileName();
        [$width, $height] = getimagesize($path);

        $assetFactory =  $this->assetFactory->create(
            [
                'id' => $asset ? $asset->getId() : null,
                'path' => $this->getRelativePath($path),
                'title' => $this->getAssetTitle($file, $asset, $assetFromFile),
                'description' => $assetFromFile->getDescription(),
                'createdAt' => $asset ? $asset->getCreatedAt() :
                    $this->date->date($file->getCTime())->format(self::DATE_FORMAT),
                'updatedAt' => $this->date->date($file->getMTime())->format(self::DATE_FORMAT),
                'width' => $width,
                'height' => $height,
                'hash' => $this->getHashImageContent($path),
                'size' => $file->getSize(),
                'contentType' => $asset ? $asset->getContentType() : 'image/' . $file->getExtension(),
                'source' => $asset ? $asset->getSource() : 'Local'
            ]
        );

        return $assetFactory;
    }

    /**
     * Returns asset title from metadata if available
     *
     * @param \SplFileInfo $file
     * @param null|AssetInterface $asset
     * @param MetadataInterface $metadata
     * @return string
     */
    private function getAssetTitle(\SplFileInfo $file, ?AssetInterface $asset, MetadataInterface $metadata): string
    {
        $title = $file->getBasename('.' . $file->getExtension());
        if ($asset) {
            $title = $asset->getTitle();
        } elseif ($metadata->getTitle() !== "") {
            $title = $metadata->getTitle();
        }

        return $title;
    }

    /**
     * Get correct path for media asset
     *
     * @param string $file
     * @return string
     * @throws ValidatorException
     */
    public function getRelativePath(string $file): string
    {
        $path = $this->getMediaDirectory()->getRelativePath($file);

        if ($this->driver->getParentDirectory($path) === '.') {
            $path = '/' . $path;
        }

        return $path;
    }

    /**
     * Get hash image content.
     *
     * @param string $path
     * @return string
     * @throws ValidatorException
     * @throws FileSystemException
     */
    private function getHashImageContent(string $path): string
    {
        $mediaDirectory = $this->getMediaDirectory();
        $imageDirectory = $mediaDirectory->readFile($mediaDirectory->getRelativePath($path));
        $hashedImageContent = $this->getContentHash->execute($imageDirectory);
        return $hashedImageContent;
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
