<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\MediaGallerySynchronization\Model;

use Magento\Framework\Filesystem;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem\Directory\Read;
use Magento\MediaGalleryApi\Api\IsPathBlacklistedInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\Filesystem\Driver\File;
use Magento\MediaGalleryApi\Api\GetAssetsByPathsInterface;

/**
 * Fetch files from media storage in batches
 */
class FetchMediaStorageFileBatches
{
    private const IMAGE_FILE_NAME_PATTERN = '#\.(jpg|jpeg|gif|png)$# i';
    
    /**
     * @var GetAssetsIterator
     */
    private $getAssetsIterator;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var Read
     */
    private $mediaDirectory;

    /**
     * @var IsPathBlacklistedInterface
     */
    private $isPathBlacklisted;

    /**
     * @var File
     */
    private $driver;
    
    /**
     * @var LoggerInterface
     */
    private $log;

    /**
     * @var int
     */
    private $batchSize;

    /**
     * @var GetAssetsByPathsInterface
     */
    private $getAssetsByPaths;
    
    /**
     * @param GetAssetsByPathsInterface $getAssetsByPaths
     * @param LoggerInterface $log
     * @param IsPathBlacklistedInterface $isPathBlacklisted
     * @param Filesystem $filesystem
     * @param GetAssetsIterator $assetsIterator
     * @param int $batchSize
     */
    public function __construct(
        GetAssetsByPathsInterface $getAssetsByPaths,
        LoggerInterface $log,
        IsPathBlacklistedInterface $isPathBlacklisted,
        Filesystem $filesystem,
        GetAssetsIterator $assetsIterator,
        File $driver,
        int $batchSize
    ) {
        $this->getAssetsByPaths = $getAssetsByPaths;
        $this->log = $log;
        $this->isPathBlacklisted = $isPathBlacklisted;
        $this->getAssetsIterator = $assetsIterator;
        $this->filesystem = $filesystem;
        $this->driver = $driver;
        $this->assetsPaths = [];
        $this->batchSize = $batchSize;
    }

    /**
     * Return files from files system by provided size of batch
     */
    public function execute(): \Traversable
    {
        $i = 0;
        $batch = [];

        /** @var \SplFileInfo $file */
        foreach ($this->getAssetsIterator->execute($this->getMediaDirectory()->getAbsolutePath()) as $file) {
            if (!$this->isApplicable($file->getPathName())) {
                continue;
            }
            if (!$this->isFileChanged($file)) {
                continue;
            }

            $batch[] = $file;
            if (++$i == $this->batchSize) {
                yield $batch;
                $i = 0;
                $batch = [];
            }
        }
        if (count($batch) > 0) {
            yield $batch;
        }
    }

    /**
     * Verify if file modification date changed
     *
     * @param \SplFileInfo $file
     */
    private function isFileChanged(\SplFileInfo $file): bool
    {
        $path = $this->getRelativePath($file->getPath() . '/' . $file->getFileName());
        $asset = $this->getAssetsByPaths->execute([$path]);

        if (empty($asset)) {
            return true;
        }
        return (new \DateTime())->setTimestamp($file->getMTime())->format('Y-m-d H:i:s') > $asset[0]->getUpdatedAt();
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
     * Can synchronization be applied to asset with provided path
     *
     * @param string $path
     * @return bool
     */
    private function isApplicable(string $path): bool
    {
        try {
            $relativePath = $this->getMediaDirectory()->getRelativePath($path);
            return $relativePath
                && !$this->isPathBlacklisted->execute($relativePath)
                && preg_match(self::IMAGE_FILE_NAME_PATTERN, $path);
        } catch (\Exception $exception) {
            $this->log->critical($exception);
            return false;
        }
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
