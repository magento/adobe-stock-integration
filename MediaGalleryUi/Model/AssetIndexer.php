<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryUi\Model;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\ReadInterface;
use Magento\Framework\Filesystem\Driver\File;
use Magento\MediaGalleryApi\Api\Data\AssetInterfaceFactory;
use Magento\MediaGalleryApi\Model\Asset\Command\SaveInterface;
use Magento\MediaGalleryUi\Model\Filesystem\IndexerInterface;
use Psr\Log\LoggerInterface;

/**
 * Create MediaGallery asset and save it to database based on file information
 */
class AssetIndexer implements IndexerInterface
{
    /**
     * @var AssetInterfaceFactory
     */
    private $assetFactory;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var ReadInterface
     */
    private $mediaDirectory;

    /**
     * @var SaveInterface
     */
    private $saveAsset;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var File
     */
    private $driver;

    /**
     * Constructor
     *
     * @param AssetInterfaceFactory $assetFactory
     * @param Filesystem $filesystem
     * @param SaveInterface $saveAsset
     * @param LoggerInterface $logger
     * @param File $file
     */
    public function __construct(
        AssetInterfaceFactory $assetFactory,
        Filesystem $filesystem,
        SaveInterface $saveAsset,
        LoggerInterface $logger,
        File $file
    ) {
        $this->assetFactory = $assetFactory;
        $this->filesystem = $filesystem;
        $this->saveAsset = $saveAsset;
        $this->logger = $logger;
        $this->driver = $file;
    }

    /**
     * Create MediaGallery asset and save it to database based on file information
     *
     * @param \SplFileInfo $item
     * @throws \Exception
     */
    public function execute(\SplFileInfo $item): void
    {
        $file = $item->getPath() . '/' . $item->getFileName();

        [$width, $height] = getimagesize($file);

        $asset = $this->assetFactory->create(
            [
                'data' => [
                    'path' => $this->getPath($file),
                    'title' => $item->getBasename('.' . $item->getExtension()),
                    'created_at' => (new \DateTime())->setTimestamp($item->getCTime())->format('Y-m-d H:i:s'),
                    'updated_at' => (new \DateTime())->setTimestamp($item->getMTime())->format('Y-m-d H:i:s'),
                    'width' => $width,
                    'height' => $height,
                    'size' => $item->getSize(),
                    'content_type' => 'image/' . $item->getExtension()
                ]
            ]
        );

        try {
            $this->saveAsset->execute($asset);
        } catch (\Exception $exception) {
            $this->logger->critical($exception);
        }
    }

    /**
     * Get correct path for media asset
     *
     * @param string $file
     * @return string
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
     * Media directory lazy loading
     *
     * @return ReadInterface
     */
    private function getMediaDirectory(): ReadInterface
    {
        if (!$this->mediaDirectory) {
            $this->mediaDirectory = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);
        }
        return $this->mediaDirectory;
    }
}
