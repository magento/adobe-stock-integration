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

/**
 * Batch generator for filesystem files, get files by providet size of batch
 */
class AssetsBatchGenerator
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
     * @var array
     */
    private $assetsPaths;

    /**
     * @var ResolveNonExistedAssets
     */
    private $resolveNonExistedAssets;
    
    /**
     * @param ResolveNonExistedAssets $resolveNonExistedAssets
     * @param IsPathBlacklistedInterface $isPathBlacklisted
     * @param Filesystem $filesystem
     * @param GetAssetsIterator $assetsIterator
     */
    public function __construct(
        ResolveNonExistedAssets $resolveNonExistedAssets,
        IsPathBlacklistedInterface $isPathBlacklisted,
        Filesystem $filesystem,
        GetAssetsIterator $assetsIterator
    ) {
        $this->resolveNonExistedAssets = $resolveNonExistedAssets;
        $this->isPathBlacklisted = $isPathBlacklisted;
        $this->getAssetsIterator = $assetsIterator;
        $this->filesystem = $filesystem;
        $this->assetsPaths = [];
    }

    /**
     * Return files from files system by provided size of batch
     *
     * @param int $size
     */
    public function getItems(int $size): \Generator
    {
        $i = 0;
        $batch = [];

        /** @var \SplFileInfo $file */
        foreach ($this->getAssetsIterator->execute($this->getMediaDirectory()->getAbsolutePath()) as $file) {
            if (!$this->isApplicable($file->getPathName())) {
                continue;
            }
            $this->assetsPaths[] = $this->getRelativePath($file->getPathName(), $file->getFileName());

            $batch[] = $file;
            if (++$i == $size) {
                yield $batch;
                $i = 0;
                $batch = [];
            }
        }
        if (count($batch) > 0) {
            yield $batch;
        }
        
        $this->resolveNonExistedAssets->execute($this->assetsPaths);
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
     * Return asset's relative path
     *
     * @param string $assetPath
     * @param string $fileName
     * @return string
     */
    private function getRelativePath(string $assetPath, string $fileName): string
    {
        $relativePath = $this->getMediaDirectory()->getRelativePath($assetPath);

        return $relativePath === $fileName ? '/' . $relativePath : $relativePath;
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
