<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\MediaGallerySynchronization\Model;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Driver\File;
use Magento\MediaGalleryApi\Api\IsPathExcludedInterface;
use Psr\Log\LoggerInterface;

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
     * @var IsPathExcludedInterface
     */
    private $isPathExcluded;

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
     * @param LoggerInterface $log
     * @param IsPathExcludedInterface $isPathExcluded
     * @param Filesystem $filesystem
     * @param GetAssetsIterator $assetsIterator
     * @param File $driver
     * @param int $batchSize
     */
    public function __construct(
        LoggerInterface $log,
        IsPathExcludedInterface $isPathExcluded,
        Filesystem $filesystem,
        GetAssetsIterator $assetsIterator,
        File $driver,
        int $batchSize
    ) {
        $this->log = $log;
        $this->isPathExcluded = $isPathExcluded;
        $this->getAssetsIterator = $assetsIterator;
        $this->filesystem = $filesystem;
        $this->driver = $driver;
        $this->batchSize = $batchSize;
    }

    /**
     * Return files from files system by provided size of batch
     */
    public function execute(): \Traversable
    {
        $i = 0;
        $batch = [];
        $mediaDirectory = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);

        /** @var \SplFileInfo $file */
        foreach ($this->getAssetsIterator->execute($mediaDirectory->getAbsolutePath()) as $file) {
            if (!$this->isApplicable($file->getPathName())) {
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
     * Can synchronization be applied to asset with provided path
     *
     * @param string $path
     * @return bool
     */
    private function isApplicable(string $path): bool
    {
        try {
            $relativePath = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA)->getRelativePath($path);
            return $relativePath
                && !$this->isPathExcluded->execute($relativePath)
                && preg_match(self::IMAGE_FILE_NAME_PATTERN, $path);
        } catch (\Exception $exception) {
            $this->log->critical($exception);
            return false;
        }
    }
}
