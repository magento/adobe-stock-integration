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
use Magento\MediaGallerySynchronizationApi\Model\GetContentHashInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\FileSystemException;
use Magento\MediaGalleryMetadataApi\Api\ExtractMetadataInterface;
use Magento\MediaGalleryMetadataApi\Api\Data\MetadataInterface;

/**
 * Create media asset object based on the file information
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
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
     * @var GetContentHashInterface
     */
    private $getContentHash;

    /**
     * @var ExtractMetadataInterface
     */
    private $extractMetadata;

    /**
     * @param Filesystem $filesystem
     * @param AssetInterfaceFactory $assetFactory
     * @param File $driver
     * @param GetAssetsByPathsInterface $getMediaGalleryAssetByPath
     * @param GetContentHashInterface $getContentHash
     * @param ExtractMetadataInterface $extractMetadata
     */
    public function __construct(
        Filesystem $filesystem,
        AssetInterfaceFactory $assetFactory,
        File $driver,
        GetAssetsByPathsInterface $getMediaGalleryAssetByPath,
        GetContentHashInterface $getContentHash,
        ExtractMetadataInterface $extractMetadata
    ) {
        $this->filesystem = $filesystem;
        $this->assetFactory = $assetFactory;
        $this->driver = $driver;
        $this->getMediaGalleryAssetByPath = $getMediaGalleryAssetByPath;
        $this->getContentHash = $getContentHash;
        $this->extractMetadata = $extractMetadata;
    }

    /**
     * Create media asset object based on the file information
     *
     * @param \SplFileInfo $file
     * @return AssetInterface
     * @throws LocalizedException
     * @throws ValidatorException
     */
    public function execute(\SplFileInfo $file)
    {
        $path = $file->getPath() . '/' . $file->getFileName();

        [$width, $height] = getimagesize($path);
        $asset = $this->getAsset($path);

        $metadata = $this->extractMetadata->execute($path);
        
        return $this->assetFactory->create(
            [
                'id' => $asset ? $asset->getId() : null,
                'path' => $this->getRelativePath($path),
                'title' => $this->getAssetTitle($file, $asset, $metadata),
                'description' => $metadata->getDescription(),
                'createdAt' => (new \DateTime())->setTimestamp($file->getCTime())->format('Y-m-d H:i:s'),
                'updatedAt' => (new \DateTime())->setTimestamp($file->getMTime())->format('Y-m-d H:i:s'),
                'width' => $width,
                'height' => $height,
                'hash' => $this->getHashImageContent($path),
                'size' => $file->getSize(),
                'contentType' => $asset ? $asset->getContentType() : 'image/' . $file->getExtension(),
                'source' => $asset ? $asset->getSource() : 'Local'
            ]
        );
    }

    /**
     * Returns asset title from metadata if available
     *
     * @param \SplFileInfo $file
     * @param null|AssetInterface $asset
     * @param MetadataInterface $metadata
     */
    private function getAssetTitle(\SplFileInfo $file, ?AssetInterface $asset, MetadataInterface $metadata): string
    {
        return $asset ? $asset->getTitle() : $metadata->getTitle() ?? $file->getBasename('.' . $file->getExtension());
    }

    /**
     * Returns asset if asset already exist by provided path
     *
     * @param string $path
     * @return AssetInterface|null
     * @throws ValidatorException
     * @throws LocalizedException
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
