<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGallerySynchronization\Model;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\Read;
use Magento\MediaGalleryApi\Api\Data\AssetInterfaceFactory;
use Magento\Framework\Filesystem\Driver\File;
use Magento\MediaGalleryApi\Model\Asset\Command\SaveInterface;
use Magento\MediaGallerySynchronizationApi\Api\SynchronizeFilesInterface;
use Psr\Log\LoggerInterface;

/**
 * Synchronize files in media storage and media assets database records
 */
class SynchronizeFiles implements SynchronizeFilesInterface
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
     * @var SaveInterface
     */
    private $saveAsset;

    /**
     * @var LoggerInterface
     */
    private $log;

    /**
     * @param Filesystem $filesystem
     * @param AssetInterfaceFactory $assetFactory
     * @param File $driver
     */
    public function __construct(
        Filesystem $filesystem,
        AssetInterfaceFactory $assetFactory,
        File $driver,
        SaveInterface $saveAsset,
        LoggerInterface $log
    ) {
        $this->filesystem = $filesystem;
        $this->assetFactory = $assetFactory;
        $this->driver = $driver;
        $this->saveAsset = $saveAsset;
        $this->log = $log;
    }

    /**
     * @inheritdoc
     */
    public function execute(array $files): void
    {
        $failedFiles = [];
        foreach ($files as $file) {
            try {
                $this->saveAsset->execute($this->createAssetFromFile($file));
            } catch (\Exception $exception) {
                $this->log->critical($exception);
                $failedFiles[] = $file->getFilename();
            }
        }

        if (!empty($failedFiles)) {
            throw new LocalizedException(
                __(
                    'Could not update media assets for files: %files',
                    [
                        'files' => $failedFiles
                    ]
                )
            );
        }
    }

    /**
     * Create Media Asset object from file information
     *
     * @param \SplFileInfo $file
     * @return \Magento\MediaGalleryApi\Api\Data\AssetInterface
     * @throws \Magento\Framework\Exception\ValidatorException
     */
    private function createAssetFromFile(\SplFileInfo $file)
    {
        $path = $file->getPath() . '/' . $file->getFileName();

        [$width, $height] = getimagesize($path);

        return $this->assetFactory->create(
            [
                'data' => [
                    'path' => $this->getPath($path),
                    'title' => $file->getBasename('.' . $file->getExtension()),
                    'created_at' => (new \DateTime())->setTimestamp($file->getCTime())->format('Y-m-d H:i:s'),
                    'updated_at' => (new \DateTime())->setTimestamp($file->getMTime())->format('Y-m-d H:i:s'),
                    'width' => $width,
                    'height' => $height,
                    'size' => $file->getSize(),
                    'content_type' => 'image/' . $file->getExtension()
                ]
            ]
        );
    }

    /**
     * Get correct path for media asset
     *
     * @param string $file
     * @return string
     * @throws \Magento\Framework\Exception\ValidatorException
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
