<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGallerySynchronization\Model;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\ValidatorException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Driver\File;
use Magento\MediaGalleryApi\Api\GetAssetsByPathsInterface;
use Magento\MediaGalleryApi\Api\SaveAssetsInterface;
use Magento\MediaGallerySynchronizationApi\Api\SynchronizeFilesInterface;
use Psr\Log\LoggerInterface;

/**
 * Synchronize files in media storage and media assets database records
 */
class SynchronizeFiles implements SynchronizeFilesInterface
{
    /**
     * @var CreateAssetFromFile
     */
    private $createAssetFromFile;

    /**
     * @var SaveAssetsInterface
     */
    private $saveAsset;

    /**
     * @var LoggerInterface
     */
    private $log;

    /**
     * @var GetAssetsByPathsInterface
     */
    private $getAssetsByPaths;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var File
     */
    private $driver;

    /**
     * @param File $driver
     * @param Filesystem $filesystem
     * @param GetAssetsByPathsInterface $getAssetsByPaths
     * @param CreateAssetFromFile $createAssetFromFile
     * @param SaveAssetsInterface $saveAsset
     * @param LoggerInterface $log
     */
    public function __construct(
        File $driver,
        Filesystem $filesystem,
        GetAssetsByPathsInterface $getAssetsByPaths,
        CreateAssetFromFile $createAssetFromFile,
        SaveAssetsInterface $saveAsset,
        LoggerInterface $log
    ) {
        $this->driver = $driver;
        $this->filesystem = $filesystem;
        $this->getAssetsByPaths = $getAssetsByPaths;
        $this->createAssetFromFile = $createAssetFromFile;
        $this->saveAsset = $saveAsset;
        $this->log = $log;
    }

    /**
     * @inheritdoc
     */
    public function execute(array $files): void
    {
        $assets = $this->getExistingAssets($files);
        foreach ($files as $file) {
            $path = $this->getFilePath($file);
            $time = $this->getFileModificationTime($file);
            if (isset($assets[$path]) && $time === $assets[$path]) {
                continue;
            }
            try {
                $this->saveAsset->execute([$this->createAssetFromFile->execute($file)]);
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
                        'files' => implode(', ', $failedFiles)
                    ]
                )
            );
        }
    }

    /**
     * Retrieve relative file path
     *
     * @param \SplFileInfo $file
     * @return string
     */
    private function getFilePath(\SplFileInfo $file): string
    {
        return $this->getRelativePath($file->getPath() . '/' . $file->getFileName());
    }

    /**
     * Retrieve formatted file modification time
     *
     * @param \SplFileInfo $file
     * @return string
     */
    private function getFileModificationTime(\SplFileInfo $file): string
    {
        return (new \DateTime())->setTimestamp($file->getMTime())->format('Y-m-d H:i:s');
    }

    /**
     * Return existing assets from files
     *
     * @param \SplFileInfo[] $files
     * @return array
     * @throws LocalizedException
     */
    private function getExistingAssets(array $files): array
    {
        $result = [];
        $paths = array_map(function ($file) {
            return $this->getFilePath($file);
        }, $files);

        $assets = $this->getAssetsByPaths->execute($paths);

        foreach ($assets as $asset) {
            $result[$asset->getPath()] = $asset->getUpdatedAt();
        }

        return $result;
    }

    /**
     * Get correct path for media asset
     *
     * @param string $file
     * @return string
     */
    private function getRelativePath(string $file): string
    {
        $path = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA)->getRelativePath($file);

        if ($this->driver->getParentDirectory($path) === '.') {
            $path = '/' . $path;
        }

        return $path;
    }
}
